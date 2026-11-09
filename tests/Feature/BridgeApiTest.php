<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\SiakadApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BridgeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@uinssc.ac.id',
        ]);

        Config::set('services.bridge.base_url', 'https://bridge.uinssc.ac.id/api');
        Config::set('services.bridge.token', 'test-token');
    }

    public function test_bridge_api_handles_401_unauthorized_gracefully(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/*' => Http::response([
                'status' => 'false',
                'message' => 'Token tidak valid.',
            ], 401),
        ]);

        $service = app(SiakadApiService::class);
        $status = $service->testBridgeConnection(2);

        $this->assertSame('unauthorized', $status['status']);
        $this->assertSame(401, $status['http_code']);
        $this->assertStringContainsString('Token tidak valid', $status['message']);

        // Test that sync returns failure without throwing exception
        $syncResult = $service->syncProgramStudiFromBridge(2);
        $this->assertFalse($syncResult['success']);
        $this->assertSame(401, $syncResult['status_code']);
        $this->assertStringContainsString('401 Unauthorized', $syncResult['message']);
    }

    public function test_bridge_api_handles_network_failure_gracefully(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/*' => function () {
                throw new \Exception('Connection refused');
            },
        ]);

        $service = app(SiakadApiService::class);
        $status = $service->testBridgeConnection(2);

        $this->assertSame('connection_error', $status['status']);
        $this->assertStringContainsString('Connection refused', $status['message']);
    }

    public function test_bridge_api_syncs_prodi_when_response_is_valid(): void
    {
        $fakultas = Fakultas::create([
            'kode_fakultas' => 2,
            'nama_fakultas' => 'SYARIAH',
            'nama_fakultas_singkat' => 'FS',
        ]);

        Http::fake([
            'https://bridge.uinssc.ac.id/api/program_studi*' => Http::response([
                'status' => true,
                'message' => 'Data Found',
                'count' => 1,
                'data' => [
                    [
                        'id_prodi' => 15,
                        'kode_prodi' => '313',
                        'nama_prodi' => 'HUKUM TATANEGARA ISLAM',
                        'jenjang' => 'S1',
                        'sks_tempuh' => 144,
                        'gelar_akademik' => 'SH.',
                        'gelar_akademik_full' => 'Sarjana Hukum',
                        'fakultas' => 'SYARIAH',
                        'fakultas_kode' => 2,
                    ],
                ],
            ], 200),
        ]);

        $service = app(SiakadApiService::class);
        $result = $service->syncProgramStudiFromBridge(2);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['total']);

        $prodi = ProgramStudi::where('kode_prodi', '313')->first();
        $this->assertNotNull($prodi);
        $this->assertSame('HUKUM TATANEGARA ISLAM', $prodi->nama_program_studi);
        $this->assertSame('SH.', $prodi->gelar_akademik);
        $this->assertSame('S1', $prodi->jenjang);
        $this->assertSame($fakultas->id, $prodi->fakultas_id);
    }

    public function test_admin_master_views_render_safely_even_when_api_is_unauthorized(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/*' => Http::response([
                'status' => 'false',
                'message' => 'Token tidak valid.',
            ], 401),
        ]);

        $responseFakultas = $this->actingAs($this->admin)->get(route('admin.fakultas.index'));
        $responseFakultas->assertOk();
        $responseFakultas->assertSee('TOKEN INVALID (HTTP 401)');

        $responseProdi = $this->actingAs($this->admin)->get(route('admin.program-studi.index'));
        $responseProdi->assertOk();
        $responseProdi->assertSee('TOKEN INVALID (HTTP 401)');
    }
}
