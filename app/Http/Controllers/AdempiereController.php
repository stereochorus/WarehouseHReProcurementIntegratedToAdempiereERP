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

        // ── 4. getVersion() — reachability test, tidak butuh auth ────────────
        if ($checks['wsdl_ad']['ok']) {
            [$verOk, $verMsg] = $this->testGetVersion($baseUrl, $timeout);
            $checks['getversion'] = [
                'label' => 'Adempiere Service (getVersion)',
                'ok'    => $verOk,
                'msg'   => $verMsg,
            ];
        } else {
            $checks['getversion'] = [
                'label' => 'Adempiere Service (getVersion)',
                'ok'    => null,
                'msg'   => 'Dilewati — ADService WSDL tidak dapat diakses.',
            ];
        }

        // ── 5. Credential test via ModelADService (embedded auth) ─────────────
        // ModelADService menyisipkan ADLoginRequest di setiap request — ini adalah
        // cara resmi untuk autentikasi di ADInterface. Tidak perlu ADService.login().
        if ($checks['wsdl_model']['ok']) {
            [$loginOk, $loginMsg] = $this->testCredentials($baseUrl, $timeout);
            $checks['login'] = [
                'label' => 'Autentikasi Credentials (ModelADService)',
                'ok'    => $loginOk,
                'msg'   => $loginMsg,
            ];
        } else {
            $checks['login'] = [
                'label' => 'Autentikasi Credentials (ModelADService)',
                'ok'    => null,
                'msg'   => 'Dilewati — ModelADService WSDL tidak dapat diakses.',
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
    /**
     * Test reachability via ADService.getVersion() — tidak butuh autentikasi.
     *
     * @return array{0:bool, 1:string}
     */
    private function testGetVersion(string $baseUrl, int $timeout): array
    {
        $namespace = 'http://3e.pl/ADInterface';
        try {
            $client = new \SoapClient(null, [
                'location'     => "{$baseUrl}/ADService",
                'uri'          => $namespace,
                'soap_version' => SOAP_1_1,
                'style'        => SOAP_RPC,
                'use'          => SOAP_LITERAL,
                'exceptions'   => true,
                'connection_timeout' => $timeout,
            ]);
            $version = $client->getVersion();
            return [true, "Service aktif. Versi Adempiere: " . htmlspecialchars((string) $version)];
        } catch (\SoapFault $e) {
            return [false, "getVersion() gagal: " . $e->getMessage()];
        } catch (\Throwable $e) {
            return [false, "Exception: " . $e->getMessage()];
        }
    }

    /**
     * Test autentikasi via ModelADService.queryData() dengan embedded ADLoginRequest.
     * Ini adalah cara resmi ADInterface — credentials disisipkan di setiap request.
     * serviceType sengaja dikosongkan; jika Adempiere membalas "Service not found"
     * (bukan auth error), berarti credentials sudah valid.
     *
     * @return array{0:bool, 1:string}
     */
    private function testCredentials(string $baseUrl, int $timeout): array
    {
        $namespace = 'http://3e.pl/ADInterface';
        $esc = fn(string $v) => htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $loginXml = '<user>'        . $esc((string) config('adempiere.username'))       . '</user>'
                  . '<pass>'        . $esc((string) config('adempiere.password'))       . '</pass>'
                  . '<lang>'        . $esc((string) config('adempiere.language'))       . '</lang>'
                  . '<ClientID>'    . intval(config('adempiere.client_id'))             . '</ClientID>'
                  . '<RoleID>'      . intval(config('adempiere.role_id'))               . '</RoleID>'
                  . '<OrgID>'       . intval(config('adempiere.org_id'))                . '</OrgID>'
                  . '<WarehouseID>' . intval(config('adempiere.warehouse_id'))          . '</WarehouseID>'
                  . '<stage>0</stage>';

        $rawXml = '<ModelCRUDRequest xmlns="' . $namespace . '">'
                . '<ModelCRUD>'
                . '<serviceType></serviceType>'
                . '<TableName>AD_Client</TableName>'
                . '<RecordID>0</RecordID>'
                . '<Filter></Filter>'
                . '<RetriveResultAs>Element</RetriveResultAs>'
                . '<Action>Read</Action>'
                . '<PageNo>0</PageNo>'
                . '</ModelCRUD>'
                . '<ADLoginRequest>' . $loginXml . '</ADLoginRequest>'
                . '</ModelCRUDRequest>';

        $client = null;
        try {
            $client = new \SoapClient(null, [
                'location'     => "{$baseUrl}/ModelADService",
                'uri'          => $namespace,
                'soap_version' => SOAP_1_1,
                'style'        => SOAP_RPC,
                'use'          => SOAP_LITERAL,
                'exceptions'   => true,
                'trace'        => true,
                'connection_timeout' => $timeout,
            ]);

            $soapVar     = new \SoapVar($rawXml, XSD_ANYXML);
            $response    = $client->queryData($soapVar);
            $responseStr = strtolower(json_encode($response) ?: '');

            // Jika response mengandung auth error → credentials salah
            if (str_contains($responseStr, 'you need to login') || str_contains($responseStr, 'login failed')) {
                $xmlReq = htmlspecialchars($client->__getLastRequest() ?: '');
                return [false, 'Credentials ditolak: "You need to login".'
                    . "<br><small>Request: <pre style='font-size:10px'>{$xmlReq}</pre></small>"];
            }

            // "Service type  not configured" = credentials valid, service type belum dibuat di Adempiere
            // Ini adalah kondisi EXPECTED pada tahap setup.
            $raw = substr(json_encode($response) ?: '', 0, 300);
            $hint = str_contains($responseStr, 'service type') || str_contains($responseStr, 'not configured')
                ? '<br><b>Langkah selanjutnya:</b> Buat Web Service Type di Adempiere ('
                  . 'System Admin → General Rules → Web Service → Web Service Type).'
                : '';
            return [true, "Credentials valid! Adempiere menerima request.<br>Response: <code>{$raw}</code>{$hint}"];

        } catch (\SoapFault $e) {
            $msg    = $e->getMessage();
            $xmlReq = htmlspecialchars($client?->__getLastRequest()  ?: '(kosong)');
            $xmlRes = htmlspecialchars($client?->__getLastResponse() ?: '(kosong)');

            // Jika error bukan tentang auth, credentials mungkin sudah benar
            $authErrors = ['login', 'credential', 'password', 'unauthorized', 'access denied'];
            $isAuthError = collect($authErrors)->contains(fn($w) => stripos($msg, $w) !== false);

            if (!$isAuthError) {
                return [
                    true,
                    "Credentials diterima (error bukan auth: {$msg})"
                    . "<br><small>Request: <pre style='font-size:10px'>{$xmlReq}</pre></small>",
                ];
            }

            return [
                false,
                "Credentials ditolak: {$msg}"
                . "<br><b>Request XML:</b><pre style='font-size:10px'>{$xmlReq}</pre>"
                . "<br><b>Response XML:</b><pre style='font-size:10px'>{$xmlRes}</pre>",
            ];
        } catch (\Throwable $e) {
            return [false, "Exception: " . $e->getMessage()];
        }
    }

    /**
     * @deprecated Digantikan oleh testGetVersion() + testCredentials()
     */
    private function testLogin(string $baseUrl, int $timeout): array
    {
        $namespace = 'http://3e.pl/ADInterface';

        $client = new \SoapClient("{$baseUrl}/ADService?wsdl", [
            'exceptions'         => true,
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'connection_timeout' => $timeout,
            'trace'              => true,   // aktifkan agar bisa lihat XML request
        ]);

        $params = [
            'user'        => config('adempiere.username'),
            'pass'        => config('adempiere.password'),
            'lang'        => config('adempiere.language'),
            'ClientID'    => (int) config('adempiere.client_id'),
            'RoleID'      => (int) config('adempiere.role_id'),
            'OrgID'       => (int) config('adempiere.org_id'),
            'WarehouseID' => (int) config('adempiere.warehouse_id'),
            'stage'       => 0,
        ];

        $loginObj = (object) $params;

        // ── Strategi 1: pass object langsung (tanpa wrapper key) ──────────────
        // PHP SoapClient RPC/literal: positional argument → dipetakan ke part
        // pertama dari message, yang bertipe ADLoginRequest.
        try {
            $response = $client->login($loginObj);
            return $this->parseLoginResponse($response, 'Strategi-1 (direct object)');
        } catch (\SoapFault $e1) {
            // lanjut ke strategi 2
        }

        // ── Strategi 2: SoapVar dengan namespace eksplisit ───────────────────
        try {
            $soapVar  = new \SoapVar($loginObj, SOAP_ENC_OBJECT, 'ADLoginRequest', $namespace);
            $response = $client->login($soapVar);
            return $this->parseLoginResponse($response, 'Strategi-2 (SoapVar)');
        } catch (\SoapFault $e2) {
            // lanjut ke strategi 3
        }

        // ── Strategi 3: named key array dengan stdClass ───────────────────────
        try {
            $response = $client->login(['ADLoginRequest' => $loginObj]);
            return $this->parseLoginResponse($response, 'Strategi-3 (named key)');
        } catch (\SoapFault $e3) {
            // lanjut ke strategi 4
        } catch (\Throwable $ignored) {
            // lanjut ke strategi 4
        }

        // ── Strategi 4: Non-WSDL mode — bypass WSDL type validation ──────────
        // PHP 8.x SoapClient kadang gagal encode element-reference message parts
        // dari WSDL XFire. Non-WSDL mode melewati validasi type sepenuhnya dan
        // memungkinkan kita melihat XML request/response aktual.
        $nonWsdlClient = new \SoapClient(null, [
            'location'     => "{$baseUrl}/ADService",
            'uri'          => $namespace,
            'soap_version' => SOAP_1_1,
            'style'        => SOAP_RPC,
            'use'          => SOAP_LITERAL,   // WSDL: use="literal" bukan encoded
            'exceptions'   => true,
            'trace'        => true,
        ]);

        try {
            // XSD_ANYXML: inject raw XML langsung ke SOAP body.
            // Ini memberi kontrol penuh atas namespace.
            // XFire membutuhkan <ADLoginRequest xmlns="http://3e.pl/ADInterface">
            // bukan <ADLoginRequest> tanpa namespace.
            $esc = fn(string $v) => htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $rawXml = '<ADLoginRequest xmlns="' . $namespace . '">'
                . '<user>'        . $esc((string) ($params['user']        ?? '')) . '</user>'
                . '<pass>'        . $esc((string) ($params['pass']        ?? '')) . '</pass>'
                . '<lang>'        . $esc((string) ($params['lang']        ?? '')) . '</lang>'
                . '<ClientID>'    . intval($params['ClientID']    ?? 0)           . '</ClientID>'
                . '<RoleID>'      . intval($params['RoleID']      ?? 0)           . '</RoleID>'
                . '<OrgID>'       . intval($params['OrgID']       ?? 0)           . '</OrgID>'
                . '<WarehouseID>' . intval($params['WarehouseID'] ?? 0)           . '</WarehouseID>'
                . '<stage>'       . intval($params['stage']       ?? 0)           . '</stage>'
                . '</ADLoginRequest>';

            $soapVar  = new \SoapVar($rawXml, XSD_ANYXML);
            $response = $nonWsdlClient->login($soapVar);

            $xmlReq = htmlspecialchars($nonWsdlClient->__getLastRequest() ?: '');
            $parsed = $this->parseLoginResponse($response, 'Strategi-4 (XSD_ANYXML + ns)');
            if ($parsed[0] && $xmlReq) {
                $parsed[1] .= "<br><small>Request XML: <pre style='font-size:10px'>{$xmlReq}</pre></small>";
            }
            return $parsed;

        } catch (\SoapFault $e4) {
            $xmlReq = htmlspecialchars($nonWsdlClient->__getLastRequest()  ?: '(kosong)');
            $xmlRes = htmlspecialchars($nonWsdlClient->__getLastResponse() ?: '(kosong)');
            return [
                false,
                "Semua strategi gagal. Terakhir: " . $e4->getMessage()
                . "<br><b>Request XML:</b><pre style='font-size:10px'>{$xmlReq}</pre>"
                . "<br><b>Response XML:</b><pre style='font-size:10px'>{$xmlRes}</pre>",
            ];
        } catch (\Throwable $e) {
            return [false, "Exception Strategi-4: " . $e->getMessage()];
        }
    }

    /**
     * Parse ADLoginResponse → [bool success, string message]
     */
    private function parseLoginResponse(mixed $response, string $strategy): array
    {
        // ADLoginResponse: status (xsd:int). XFire membungkus dalam 'return'.
        // status >= 0 → berhasil, status < 0 → gagal.
        $status = $response->return->status
               ?? $response->out->status
               ?? $response->LoginResponse->status
               ?? $response->status
               ?? -1;

        if (((int) $status) >= 0) {
            return [true, "Login berhasil via {$strategy}! (status: {$status})"];
        }

        $raw = substr(json_encode($response) ?: '', 0, 400);
        return [
            false,
            "{$strategy} → status={$status}. Raw: {$raw}",
        ];
    }
}
