<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

/**
 * AdempiereController
 * ─────────────────────────────────────────────────────────────────────────────
 * Halaman diagnostik & status koneksi ke Adempiere ERP.
 *
 * Routes:
 *   GET  /adempiere/status       → status()
 *   POST /adempiere/clear-cache  → clearCache()
 * ─────────────────────────────────────────────────────────────────────────────
 */
class AdempiereController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════════
    // PUBLIC ACTIONS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Tampilkan halaman status & diagnostik Adempiere.
     */
    public function status()
    {
        $isDemo = env('DEMO_MODE', 'true') === 'true';
        $config = $this->currentConfig();
        $checks = $isDemo ? [] : $this->runDiagnostics();

        return view('adempiere.status', compact('isDemo', 'config', 'checks'));
    }

    /**
     * Reset cache status koneksi, lalu redirect ke halaman status.
     */
    public function clearCache()
    {
        Cache::forget('adempiere_connected');

        return redirect()->route('adempiere.status')
            ->with('success', 'Cache koneksi Adempiere telah di-reset. Silakan cek hasil diagnostik di bawah.');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // DIAGNOSTICS — internal
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Konfigurasi Adempiere yang sedang aktif (dari config/adempiere.php / .env).
     */
    private function currentConfig(): array
    {
        return [
            'base_url'      => config('adempiere.base_url'),
            'username'      => config('adempiere.username'),
            'client_id'     => config('adempiere.client_id'),
            'org_id'        => config('adempiere.org_id'),
            'role_id'       => config('adempiere.role_id'),
            'warehouse_id'  => config('adempiere.warehouse_id'),
            'language'      => config('adempiere.language'),
            'service_types' => config('adempiere.service_types', []),
        ];
    }

    /**
     * Jalankan serangkaian tes diagnostik secara berurutan.
     * Jika tes awal gagal, tes berikutnya yang bergantung padanya dilewati.
     *
     * @return array<string, array{label:string, ok:bool|null, msg:string, url?:string}>
     */
    private function runDiagnostics(): array
    {
        $checks  = [];
        $baseUrl = config('adempiere.base_url');
        $timeout = (int) config('adempiere.soap_timeout', 10);

        // ── 1. PHP SOAP Extension ──────────────────────────────────────────────
        $soapLoaded = extension_loaded('soap');
        $checks['soap'] = [
            'label' => 'PHP SOAP Extension',
            'ok'    => $soapLoaded,
            'msg'   => $soapLoaded
                ? 'Extension soap aktif (php -m | grep soap)'
                : 'Extension soap TIDAK aktif. Edit php.ini → uncomment "extension=soap" → restart server.',
        ];

        if (!$soapLoaded) {
            return $checks; // tidak ada gunanya lanjutkan tanpa SOAP
        }

        // ── 2. WSDL ADService (login endpoint) ────────────────────────────────
        $adWsdl = "{$baseUrl}/ADService?wsdl";
        [$ok, $msg, $adFunctions] = $this->testWsdl($adWsdl, $timeout);
        $checks['wsdl_ad'] = [
            'label'     => 'WSDL ADService (login)',
            'ok'        => $ok,
            'msg'       => $msg,
            'url'       => $adWsdl,
            'functions' => $adFunctions,    // signature fungsi dari WSDL
        ];

        // ── 3. WSDL ModelADService (data CRUD) ────────────────────────────────
        $modelWsdl = "{$baseUrl}/ModelADService?wsdl";
        [$ok2, $msg2, $modelFunctions] = $this->testWsdl($modelWsdl, $timeout);
        $checks['wsdl_model'] = [
            'label'     => 'WSDL ModelADService (data query)',
            'ok'        => $ok2,
            'msg'       => $msg2,
            'url'       => $modelWsdl,
            'functions' => $modelFunctions,
        ];

        // ── 4. Login test (hanya jika ADService WSDL berhasil) ────────────────
        if ($checks['wsdl_ad']['ok']) {
            [$loginOk, $loginMsg] = $this->testLogin($baseUrl, $timeout);
            $checks['login'] = [
                'label' => 'Login ke Adempiere',
                'ok'    => $loginOk,
                'msg'   => $loginMsg,
            ];
        } else {
            $checks['login'] = [
                'label' => 'Login ke Adempiere',
                'ok'    => null,
                'msg'   => 'Dilewati — ADService WSDL tidak dapat diakses.',
            ];
        }

        return $checks;
    }

    /**
     * Test apakah WSDL URL bisa diakses dengan membuat SoapClient sementara.
     * Juga mengembalikan daftar fungsi yang diketahui oleh WSDL (berguna untuk debug).
     *
     * @return array{0:bool, 1:string, 2:array}  [success, message, functions]
     */
    private function testWsdl(string $url, int $timeout): array
    {
        try {
            $client = new \SoapClient($url, [
                'exceptions'         => true,
                'cache_wsdl'         => WSDL_CACHE_NONE,
                'connection_timeout' => $timeout,
            ]);

            // Ambil signature fungsi dari WSDL — berguna untuk debug parameter XFire
            $functions = $client->__getFunctions() ?? [];

            return [true, 'WSDL berhasil diakses dan diparsing.', $functions];

        } catch (\SoapFault $e) {
            $msg = $e->getMessage();
            if (
                stripos($msg, 'Could not connect') !== false ||
                stripos($msg, 'failed to load') !== false ||
                stripos($msg, 'Connection refused') !== false ||
                stripos($msg, 'timed out') !== false
            ) {
                return [false, "Tidak dapat terhubung ke Adempiere: {$msg}", []];
            }
            return [true, "WSDL dapat diakses (catatan: {$msg})", []];

        } catch (\Throwable $e) {
            return [false, "Error: " . $e->getMessage(), []];
        }
    }

    /**
     * Lakukan login test langsung (fresh, tidak pakai cache) ke ADService.
     * Mencoba beberapa variasi parameter sesuai format XFire / ADInterface.
     *
     * @return array{0:bool, 1:string}  [success, message]
     */
    private function testLogin(string $baseUrl, int $timeout): array
    {
        try {
            $client = new \SoapClient("{$baseUrl}/ADService?wsdl", [
                'exceptions'         => true,
                'cache_wsdl'         => WSDL_CACHE_NONE,
                'connection_timeout' => $timeout,
            ]);

            // Ambil signature untuk ditampilkan jika login gagal
            $functions  = $client->__getFunctions() ?? [];
            $loginSig   = collect($functions)->first(fn($f) => stripos($f, 'login') !== false) ?? '';

            $params = [
                'user'        => config('adempiere.username'),
                'pass'        => config('adempiere.password'),
                'lang'        => config('adempiere.language'),
                'ClientID'    => (int) config('adempiere.client_id'),
                'RoleID'      => (int) config('adempiere.role_id'),
                'OrgID'       => (int) config('adempiere.org_id'),
                'WarehouseID' => (int) config('adempiere.warehouse_id'),
                'stage'       => 0,   // required by ADLoginRequest WSDL (xsd:int)
            ];

            // WSDL: ADLoginResponse login(ADLoginRequest $ADLoginRequest)
            // → nama parameter PHP adalah 'ADLoginRequest'.
            $response = $client->login(['ADLoginRequest' => $params]);

            // ADLoginResponse memiliki 'status' (xsd:int), BUKAN 'result'.
            // XFire membungkus return value dalam property 'return'.
            // status >= 0 → berhasil,  status < 0 → gagal.
            $status = $response->return->status
                   ?? $response->out->status
                   ?? $response->LoginResponse->status
                   ?? $response->status
                   ?? -1;

            if (((int) $status) >= 0) {
                return [true, "Login berhasil! (status: {$status})"];
            }

            $raw = substr(json_encode($response) ?: '', 0, 300);
            return [
                false,
                "Login ditolak. Status: {$status}"
                . ". Cek username/password/ClientID/RoleID di .env."
                . " | WSDL signature: {$loginSig}"
                . " | Raw: {$raw}",
            ];

        } catch (\SoapFault $e) {
            // Jika masih encoding error, coba tanpa wrapper (beberapa ADInterface versi lain)
            $soapMsg = $e->getMessage();
            if (stripos($soapMsg, 'Encoding') !== false || stripos($soapMsg, 'property') !== false) {
                return [
                    false,
                    "SOAP Encoding Error: {$soapMsg}. "
                    . "Parameter 'in0' mungkin tidak sesuai dengan WSDL ini. "
                    . "Buka WSDL di browser dan cari nama parameter untuk method 'login'.",
                ];
            }
            return [false, "SOAP Fault: {$soapMsg}"];
        } catch (\Throwable $e) {
            return [false, "Exception: " . $e->getMessage()];
        }
    }
}
