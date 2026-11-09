<?php

namespace Tests\Feature\Auth;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SiakadAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_login_in_dummy_mode(): void
    {
        config(['services.siakad.base_url' => 'dummy']);

        $response = $this->post('/login', [
            'login' => '210101001',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'ahmad.fauzi@uinssc.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertEquals('Ahmad Fauzi', $user->name);

        $mhs = Mahasiswa::where('nim', '210101001')->first();
        $this->assertNotNull($mhs);
        $this->assertEquals($user->id, $mhs->user_id);

        $response->assertRedirect(route('mahasiswa.dashboard', absolute: false));
    }

    public function test_mahasiswa_can_login_via_siakad_bridge_api(): void
    {
        config(['services.siakad.base_url' => 'https://bridge.ecampuz.com']);

        Http::fake([
            '*/api/v2/portal/login' => Http::response([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'token' => 'mocked-sanctum-bearer-token-12345',
                    'username' => '210101001',
                    'nama' => 'Budi Pratama',
                    'email' => 'budi@uinssc.ac.id',
                    'fakultas' => 'Fakultas Sains dan Teknologi',
                    'prodi' => 'Teknik Informatika',
                    'angkatan' => '2021',
                ],
            ], 200),
            '*/api/v2/profil' => Http::response([
                'success' => true,
                'data' => [
                    'nim' => '210101001',
                    'nama' => 'Budi Pratama',
                    'email' => 'budi@uinssc.ac.id',
                    'fakultas' => 'Fakultas Sains dan Teknologi',
                    'prodi' => 'Teknik Informatika',
                ],
            ], 200),
        ]);

        $response = $this->post('/login', [
            'login' => '210101001',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@uinssc.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertEquals('Budi Pratama', $user->name);

        $mhs = Mahasiswa::where('nim', '210101001')->first();
        $this->assertNotNull($mhs);
        $this->assertEquals($user->id, $mhs->user_id);

        $response->assertRedirect(route('mahasiswa.dashboard', absolute: false));
    }

    public function test_mahasiswa_cannot_login_with_invalid_siakad_credentials(): void
    {
        config(['services.siakad.base_url' => 'https://bridge.ecampuz.com']);

        Http::fake([
            '*/api/v2/portal/login' => Http::response([
                'success' => false,
                'message' => 'NIM atau password salah.',
            ], 401),
        ]);

        $response = $this->post('/login', [
            'login' => '210101001',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_admin_can_login_locally(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@uinssc.ac.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@uinssc.ac.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
