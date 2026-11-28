<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use App\Models\User;
use App\Models\Wisudawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranscriptSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_transcript_and_wisudawan_automatically_sync_when_grades_change(): void
    {
        $fakultas = Fakultas::create(['nama_fakultas' => 'Fakultas Sains dan Teknologi', 'kode_fakultas' => 'FST']);
        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'nama_program_studi' => 'Teknik Informatika',
            'kode_program_studi' => 'TIF',
            'jenjang' => 'S1',
        ]);

        $user = User::factory()->create([
            'name' => 'Muhammad Arif Rizky',
            'email' => 'mhs2388010017@uinssc.ac.id',
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '2388010017',
            'nama' => 'Muhammad Arif Rizky',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'status' => 'Lulus',
        ]);

        $wisudawan = Wisudawan::create([
            'mahasiswa_id' => $mhs->id,
            'nim' => '2388010017',
            'nama' => 'Muhammad Arif Rizky',
            'periode_wisuda' => 'Wisuda Ganjil 2026/2027',
            'ipk' => 0.0,
            'total_sks' => 0,
        ]);

        $mk1 = SiakadMataKuliah::create([
            'kode_mata_kuliah' => 'INF62301',
            'nama_mata_kuliah' => 'Algoritma dan Pemrograman I',
            'sks' => 3,
        ]);
        $mk2 = SiakadMataKuliah::create([
            'kode_mata_kuliah' => 'INF62304',
            'nama_mata_kuliah' => 'Algoritma dan Pemrograman II',
            'sks' => 3,
        ]);

        // Grade 1: 3 SKS, A (4.00) => Mutu 12
        $n1 = SiakadNilai::create([
            'mahasiswa_id' => $mhs->id,
            'mata_kuliah_id' => $mk1->id,
            'semester' => '1',
            'nilai_huruf' => 'A',
            'bobot' => 4.00,
            'sks' => 3,
        ]);

        // Grade 2: 3 SKS, C+ (2.25) => Mutu 6.75 => Total: 18.75 / 6 = 3.125 -> 3.13
        $n2 = SiakadNilai::create([
            'mahasiswa_id' => $mhs->id,
            'mata_kuliah_id' => $mk2->id,
            'semester' => '2',
            'nilai_huruf' => 'C+',
            'bobot' => 2.25,
            'sks' => 3,
        ]);

        $trx = Transkrip::where('mahasiswa_id', $mhs->id)->first();
        $this->assertNotNull($trx);
        $this->assertEquals(6, $trx->total_sks);
        $this->assertEquals(3.13, $trx->ipk);

        $wisudawan = Wisudawan::where('mahasiswa_id', $mhs->id)->first();
        $this->assertNotNull($wisudawan);
        $this->assertEquals(3.13, (float) $wisudawan->ipk);

        // Update Grade 2 to A (4.00) => Mutu 12 => Total: 24 / 6 = 4.00
        $n2->update([
            'nilai_huruf' => 'A',
            'bobot' => 4.00,
        ]);

        $trx->refresh();
        $wisudawan->refresh();

        $this->assertEquals(4.00, $trx->ipk);
        $this->assertEquals(4.00, (float) $wisudawan->ipk);
        $this->assertEquals('Dengan Pujian (Cum Laude)', $trx->predikat);
        $this->assertEquals('Dengan Pujian (Cum Laude)', $wisudawan->predikat_kelulusan);
    }

    public function test_student_name_change_automatically_cascades_to_user_and_wisudawan(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'mhs2388010017@uinssc.ac.id',
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '2388010017',
            'nama' => 'Nama Lama',
            'status' => 'Lulus',
        ]);

        $wisudawan = Wisudawan::create([
            'mahasiswa_id' => $mhs->id,
            'nim' => '2388010017',
            'nama' => 'Nama Lama',
            'periode_wisuda' => 'Wisuda Ganjil 2026/2027',
            'ipk' => 3.57,
            'total_sks' => 134,
        ]);

        // Ubah nama mahasiswa
        $mhs->update(['nama' => 'Muhammad Arif Rizky']);

        $user->refresh();
        $wisudawan->refresh();

        $this->assertEquals('Muhammad Arif Rizky', $user->name);
        $this->assertEquals('Muhammad Arif Rizky', $wisudawan->nama);
    }

    public function test_student_dashboard_displays_synchronized_ipk_and_wisudawan_status(): void
    {
        $fakultas = Fakultas::create(['nama_fakultas' => 'Fakultas Sains dan Teknologi', 'kode_fakultas' => 'FST']);
        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'nama_program_studi' => 'Teknik Informatika',
            'kode_program_studi' => 'TIF',
            'jenjang' => 'S1',
        ]);

        $user = User::factory()->create([
            'name' => 'Muhammad Arif Rizky',
            'email' => 'mhs2388010017@uinssc.ac.id',
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '2388010017',
            'nama' => 'Muhammad Arif Rizky',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'status' => 'Lulus',
        ]);

        $wisudawan = Wisudawan::create([
            'mahasiswa_id' => $mhs->id,
            'nim' => '2388010017',
            'nama' => 'Muhammad Arif Rizky',
            'periode_wisuda' => 'Wisuda Ganjil 2026/2027',
            'ipk' => 0.0,
            'total_sks' => 0,
        ]);

        $mk = SiakadMataKuliah::create([
            'kode_mata_kuliah' => 'INF62301',
            'nama_mata_kuliah' => 'Algoritma dan Pemrograman I',
            'sks' => 3,
        ]);

        SiakadNilai::create([
            'mahasiswa_id' => $mhs->id,
            'mata_kuliah_id' => $mk->id,
            'semester' => '1',
            'nilai_huruf' => 'A',
            'bobot' => 4.00,
            'sks' => 3,
        ]);

        $response = $this->actingAs($user)->get(route('mahasiswa.dashboard'));

        $response->assertOk();
        $response->assertSee('4.00');
    }

    public function test_deleted_wisudawan_is_not_force_recreated_on_login_or_recalculation(): void
    {
        $user = User::factory()->create([
            'name' => 'Muhammad Arif Rizky',
            'email' => 'mhs2388010017@uinssc.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '2388010017',
            'nama' => 'Muhammad Arif Rizky',
            'status' => 'Aktif',
        ]);

        // Pastikan tidak ada data wisudawan
        $this->assertDatabaseMissing('wisudawans', ['nim' => '2388010017']);

        // Login sebagai mahasiswa menggunakan NIM
        $response = $this->post('/login', [
            'login' => '2388010017',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Pastikan tetap TIDAK dibuatkan wisudawan secara paksa
        $this->assertDatabaseMissing('wisudawans', ['nim' => '2388010017']);

        // Panggil recalculateTranskrip
        $mhs->recalculateTranskrip();
        $this->assertDatabaseMissing('wisudawans', ['nim' => '2388010017']);
    }
}
