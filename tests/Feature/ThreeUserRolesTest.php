<?php

namespace Tests\Feature;

use App\Models\AturanBobotNilai;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\PengajuanKoreksi;
use App\Models\ProgramStudi;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreeUserRolesTest extends TestCase
{
    use RefreshDatabase;

    protected Fakultas $fakultas;

    protected ProgramStudi $prodi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakultas = Fakultas::create(['nama_fakultas' => 'Fakultas Sains dan Teknologi']);
        $this->prodi = ProgramStudi::create([
            'fakultas_id' => $this->fakultas->id,
            'nama_program_studi' => 'Teknik Informatika',
        ]);

        // Seed basic grade weights
        $weights = [
            ['nilai_huruf' => 'A', 'bobot' => 4.00],
            ['nilai_huruf' => 'A-', 'bobot' => 3.50],
            ['nilai_huruf' => 'B+', 'bobot' => 3.25],
            ['nilai_huruf' => 'B', 'bobot' => 3.00],
            ['nilai_huruf' => 'B-', 'bobot' => 2.75],
            ['nilai_huruf' => 'C+', 'bobot' => 2.25],
            ['nilai_huruf' => 'C', 'bobot' => 2.00],
            ['nilai_huruf' => 'D', 'bobot' => 1.00],
            ['nilai_huruf' => 'E', 'bobot' => 0.00],
        ];
        foreach ($weights as $w) {
            AturanBobotNilai::create($w);
        }
    }

    public function test_root_url_redirects_immediately_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_redirected_to_super_admin_dashboard_and_has_full_access(): void
    {
        $superAdmin = User::factory()->create([
            'email' => 'super@uinssc.ac.id',
            'role' => 'super_admin',
            'status_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'super@uinssc.ac.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('super-admin.dashboard'));

        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Portal Pengawas Super Administrator')
            ->assertSee('Mode Pengawas');
    }

    public function test_admin_fakultas_redirected_to_admin_dashboard_and_blocked_from_super_admin(): void
    {
        $adminFakultas = User::factory()->create([
            'email' => 'admin.fst@uinssc.ac.id',
            'role' => 'admin_fakultas',
            'fakultas_id' => $this->fakultas->id,
            'status_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin.fst@uinssc.ac.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Can access admin dashboard
        $this->actingAs($adminFakultas)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin Fakultas');

        // Blocked from super admin dashboard
        $this->actingAs($adminFakultas)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }

    public function test_mahasiswa_redirected_to_mahasiswa_dashboard_and_blocked_from_admin_areas(): void
    {
        $userMhs = User::factory()->create([
            'email' => 'mhs@uinssc.ac.id',
            'role' => 'mahasiswa',
            'status_aktif' => true,
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '220001',
            'nama' => 'Mahasiswa Test',
            'fakultas_id' => $this->fakultas->id,
            'program_studi_id' => $this->prodi->id,
            'jenjang' => 'S1',
            'angkatan' => '2022',
        ]);

        // Login using NIM
        $response = $this->post('/login', [
            'login' => '220001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));

        // Can access mahasiswa dashboard
        $this->actingAs($userMhs)
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('Transkrip Akademik');

        // Blocked from admin and super admin
        $this->actingAs($userMhs)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($userMhs)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }

    public function test_super_admin_creates_admin_fakultas_directly(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('super-admin.admin-fakultas.store'), [
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi.santoso@uinssc.ac.id',
            'password' => 'password123',
            'fakultas_id' => $this->fakultas->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@uinssc.ac.id',
            'role' => 'admin_fakultas',
            'fakultas_id' => $this->fakultas->id,
            'status_aktif' => true,
        ]);
    }

    public function test_mahasiswa_can_submit_correction_and_admin_approves_it(): void
    {
        $userMhs = User::factory()->create(['role' => 'mahasiswa', 'status_aktif' => true]);
        $mhs = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'nim' => '220002',
            'nama' => 'Siti Aminah',
            'fakultas_id' => $this->fakultas->id,
            'program_studi_id' => $this->prodi->id,
            'jenjang' => 'S1',
            'angkatan' => '2022',
        ]);

        $mk = SiakadMataKuliah::create([
            'kode_mata_kuliah' => 'INF101',
            'nama_mata_kuliah' => 'Algoritma & Pemrograman',
            'sks' => 3,
        ]);

        $nilai = SiakadNilai::create([
            'mahasiswa_id' => $mhs->id,
            'mata_kuliah_id' => $mk->id,
            'semester' => '1',
            'nilai_huruf' => 'C',
            'nilai_angka' => 2.00,
            'bobot' => 2.00,
            'sks' => 3,
            'sumber_data' => 'SIAKAD',
        ]);

        Transkrip::create([
            'mahasiswa_id' => $mhs->id,
            'total_sks' => 3,
            'total_mutu' => 6.00,
            'ipk' => 2.00,
            'status_validasi' => 'Draft',
            'kode_verifikasi' => 'uuid-mhs-koreksi',
        ]);

        // Student submits correction
        $this->actingAs($userMhs)->post(route('mahasiswa.koreksi.store'), [
            'jenis_koreksi' => 'Koreksi Nilai Mata Kuliah',
            'mata_kuliah_id' => $mk->id,
            'nilai_lama' => 'C',
            'nilai_diajukan' => 'A',
            'alasan' => 'Revisi nilai tugas akhir praktikum sesuai lembar dosen pengampu',
        ])->assertRedirect();

        $koreksi = PengajuanKoreksi::where('mahasiswa_id', $mhs->id)->firstOrFail();
        $this->assertEquals('Menunggu Review', $koreksi->status);

        // Admin Fakultas approves correction
        $admin = User::factory()->create([
            'role' => 'admin_fakultas',
            'fakultas_id' => $this->fakultas->id,
            'status_aktif' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.koreksi.approve', $koreksi->id), [
            'catatan_admin' => 'Disetujui berdasarkan bukti resmi dosen',
        ])->assertRedirect();

        $koreksi->refresh();
        $this->assertEquals('Disetujui', $koreksi->status);

        // Grade should automatically update to A with bobot 4.00 and IPK recalculated to 4.00
        $nilai->refresh();
        $this->assertEquals('A', $nilai->nilai_huruf);
        $this->assertEquals(4.00, $nilai->bobot);

        $trx = Transkrip::where('mahasiswa_id', $mhs->id)->firstOrFail();
        $this->assertEquals(4.00, $trx->ipk);
    }
}
