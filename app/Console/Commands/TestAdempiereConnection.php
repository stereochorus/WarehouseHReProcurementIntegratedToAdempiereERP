<?php

namespace App\Console\Commands;

use App\Services\AdempiereService;
use Illuminate\Console\Command;

/**
 * Artisan command untuk menguji koneksi ke Adempiere SOAP.
 *
 * Cara pakai:
 *   php artisan adempiere:test
 *   php artisan adempiere:test --wsdl        (tampilkan WSDL info)
 *   php artisan adempiere:test --query       (coba query produk)
 */
class TestAdempiereConnection extends Command
{
    protected $signature = 'adempiere:test
                            {--wsdl  : Tampilkan daftar operasi dari WSDL}
                            {--query : Coba query data M_Product}';

    protected $description = 'Test koneksi SOAP ke Adempiere ADInterface';

    public function handle(): int
    {
        $baseUrl = config('adempiere.base_url');

        $this->newLine();
        $this->line('┌─────────────────────────────────────────────────────────┐');
        $this->line('│        Adempiere SOAP Connection Test                   │');
        $this->line('└─────────────────────────────────────────────────────────┘');
        $this->newLine();

        // ── 1. Tampilkan konfigurasi ──────────────────────────────────────
        $this->info('📋 Konfigurasi:');
        $this->table(['Key', 'Value'], [
            ['base_url',     $baseUrl],
            ['username',     config('adempiere.username')],
            ['password',     str_repeat('*', strlen(config('adempiere.password')))],
            ['client_id',    config('adempiere.client_id')],
            ['org_id',       config('adempiere.org_id')],
            ['role_id',      config('adempiere.role_id')],
            ['warehouse_id', config('adempiere.warehouse_id')],
            ['language',     config('adempiere.language')],
        ]);

        // ── 2. Cek endpoint WSDL ──────────────────────────────────────────
        $this->info('🔌 Mengecek WSDL endpoint...');

        $services = ['ADService', 'ModelADService', 'WebService'];
        $available = [];

        foreach ($services as $svc) {
            $url = "{$baseUrl}/{$svc}?wsdl";
            try {
                $ctx  = stream_context_create(['http' => ['timeout' => 5]]);
                $body = @file_get_contents($url, false, $ctx);
                if ($body && (str_contains($body, 'wsdl') || str_contains($body, 'definitions'))) {
                    $this->line("  <fg=green>✓</> {$svc} — <href={$url}>{$url}</>");
                    $available[] = $svc;
                } else {
                    $this->line("  <fg=yellow>?</> {$svc} — respons tidak dikenali");
                }
            } catch (\Throwable $e) {
                $this->line("  <fg=red>✗</> {$svc} — " . $e->getMessage());
            }
        }

        if (empty($available)) {
            $this->newLine();
            $this->error('Tidak ada endpoint WSDL yang dapat diakses.');
            $this->line('Pastikan Adempiere berjalan di: ' . $baseUrl);
            return self::FAILURE;
        }

        // ── 3. WSDL detail (opsional) ─────────────────────────────────────
        if ($this->option('wsdl')) {
            $this->newLine();
            $this->info('📄 Detail WSDL (operasi yang tersedia):');
            foreach ($available as $svc) {
                $this->line("\n  <fg=cyan>[{$svc}]</>");
                $this->showWsdlOperations("{$baseUrl}/{$svc}?wsdl");
            }
        }

        // ── 4. Test Login via ADService ───────────────────────────────────
        $this->newLine();
        $this->info('🔑 Mencoba login ke ADService...');

        try {
            $client = new \SoapClient("{$baseUrl}/ADService?wsdl", [
                'trace'      => true,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]);

            $loginParams = [
                'user'        => config('adempiere.username'),
                'pass'        => config('adempiere.password'),
                'lang'        => config('adempiere.language'),
                'ClientID'    => config('adempiere.client_id'),
                'RoleID'      => config('adempiere.role_id'),
                'OrgID'       => config('adempiere.org_id'),
                'WarehouseID' => config('adempiere.warehouse_id'),
            ];

            $response = $client->login(['LoginRequest' => $loginParams]);
            $result   = $response->LoginResponse->result ?? 'unknown';

            if (strtolower($result) === 'success') {
                $this->line("  <fg=green>✓</> Login BERHASIL — result: {$result}");
            } else {
                $this->line("  <fg=red>✗</> Login GAGAL — result: {$result}");
                $this->line('  Error: ' . ($response->LoginResponse->errorInfo ?? '-'));
                return self::FAILURE;
            }

        } catch (\SoapFault $e) {
            $this->error("Login SoapFault: {$e->getMessage()}");
            $this->line('');
            $this->line('<fg=yellow>Kemungkinan penyebab:</>');
            $this->line('  - Username/password salah');
            $this->line('  - Client ID / Role ID / Org ID / Warehouse ID tidak cocok');
            $this->line('  - Nama operasi login berbeda di versi ini');
            $this->newLine();
            $this->line('<fg=yellow>Cek request XML yang dikirim:</>');
            $this->line($client->__getLastRequest() ?? '-');
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Login error: ' . $e->getMessage());
            return self::FAILURE;
        }

        // ── 5. Test Query (opsional) ──────────────────────────────────────
        if ($this->option('query')) {
            $this->newLine();
            $this->info('📦 Mencoba query M_Product via ModelADService...');
            try {
                $adempiere = app(AdempiereService::class);
                $products  = $adempiere->getProducts();

                if (empty($products)) {
                    $this->line('  <fg=yellow>!</> Query berhasil tapi tidak ada data produk yang kembali.');
                    $this->line('     Pastikan Service Type "GetProductList" sudah dikonfigurasi di Adempiere.');
                } else {
                    $this->line("  <fg=green>✓</> Berhasil! Ditemukan " . count($products) . " produk.");
                    $this->table(
                        ['id', 'name', 'stock', 'price'],
                        array_map(fn($p) => [$p['id'], $p['name'], $p['stock'], number_format($p['price'])],
                            array_slice($products, 0, 5))
                    );
                }
            } catch (\Throwable $e) {
                $this->error('Query gagal: ' . $e->getMessage());
                $this->line('');
                $this->line('<fg=yellow>Kemungkinan penyebab:</>');
                $this->line('  - Service Type "GetProductList" belum dikonfigurasi di Adempiere');
                $this->line('  - Role tidak punya akses ke tabel M_Product');
            }
        }

        // ── Ringkasan ─────────────────────────────────────────────────────
        $this->newLine();
        $this->line('┌─────────────────────────────────────────────────────────┐');
        $this->line('│  ✅  Koneksi ke Adempiere BERHASIL                      │');
        $this->line('│                                                         │');
        $this->line('│  Langkah selanjutnya:                                   │');
        $this->line('│  1. Konfigurasi Web Service Types di Adempiere          │');
        $this->line('│  2. Set DEMO_MODE=false di .env                         │');
        $this->line('│  3. Test query: php artisan adempiere:test --query      │');
        $this->line('└─────────────────────────────────────────────────────────┘');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Parse WSDL dan tampilkan operasi yang tersedia.
     */
    private function showWsdlOperations(string $wsdlUrl): void
    {
        try {
            $xml = @file_get_contents($wsdlUrl, false,
                stream_context_create(['http' => ['timeout' => 5]])
            );
            if (!$xml) {
                $this->line('     Tidak bisa membaca WSDL');
                return;
            }

            // Suppress XML parse errors untuk WSDL yang tidak standard
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadXML($xml);

            $operations = $dom->getElementsByTagNameNS('*', 'operation');
            $names = [];
            foreach ($operations as $op) {
                $name = $op->getAttribute('name');
                if ($name && !in_array($name, $names)) {
                    $names[] = $name;
                }
            }

            if (empty($names)) {
                $this->line('     (tidak bisa parse operasi dari WSDL ini)');
            } else {
                foreach ($names as $n) {
                    $this->line("     - {$n}");
                }
            }
        } catch (\Throwable $e) {
            $this->line('     Error parse WSDL: ' . $e->getMessage());
        }
    }
}
