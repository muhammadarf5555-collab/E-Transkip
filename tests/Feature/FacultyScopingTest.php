<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Tests\TestCase;

class FacultyScopingTest extends TestCase
{
    protected User $fitkAdmin;

    protected User $saintekAdmin;

    protected User $febiAdmin;

    protected User $superAdmin;

    protected Mahasiswa $inforStudent;

    protected Mahasiswa $fitkStudent;

    protected Mahasiswa $febiStudent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@uinssc.ac.id'],
            ['name' => 'Super Administrator', 'password' => bcrypt('password'), 'role' => 'super_admin']
        );

        $this->fitkAdmin = User::firstOrCreate(
            ['email' => 'admin.fitk@uinssc.ac.id'],
            ['name' => 'Admin FITK', 'password' => bcrypt('password'), 'role' => 'admin_fakultas', 'fakultas_id' => 1]
        );

        $this->saintekAdmin = User::firstOrCreate(
            ['email' => 'rizky@gmail.com'],
            ['name' => 'rizky.S.KOM', 'password' => bcrypt('password'), 'role' => 'admin_fakultas', 'fakultas_id' => 13]
        );

        $this->febiAdmin = User::firstOrCreate(
            ['email' => 'admin.febi@uinssc.ac.id'],
            ['name' => 'Admin FEBI', 'password' => bcrypt('password'), 'role' => 'admin_fakultas', 'fakultas_id' => 6]
        );

        // Find or create representative students
        $this->inforStudent = Mahasiswa::where('program_studi_id', 1)->first()
            ?? Mahasiswa::create([
                'nim' => '2388019999',
                'nama' => 'Test Mahasiswa Informatika',
                'fakultas_id' => 13,
                'program_studi_id' => 1,
                'status' => 'Aktif',
            ]);

        $this->fitkStudent = Mahasiswa::where('fakultas_id', 1)->where('program_studi_id', '!=', 1)->first()
            ?? Mahasiswa::create([
                'nim' => '2381019999',
                'nama' => 'Test Mahasiswa FITK',
                'fakultas_id' => 1,
                'status' => 'Aktif',
            ]);

        $this->febiStudent = Mahasiswa::where('fakultas_id', 6)->first()
            ?? Mahasiswa::create([
                'nim' => '2382019999',
                'nama' => 'Test Mahasiswa FEBI',
                'fakultas_id' => 6,
                'status' => 'Aktif',
            ]);
    }

    public function test_fitk_admin_can_access_infor_and_fitk_students(): void
    {
        $this->assertTrue($this->fitkAdmin->canAccessMahasiswa($this->fitkStudent));
        $this->assertTrue($this->fitkAdmin->canAccessMahasiswa($this->inforStudent));
        $this->assertFalse($this->fitkAdmin->canAccessMahasiswa($this->febiStudent));
    }

    public function test_saintek_admin_can_access_infor_but_not_fitk_or_febi(): void
    {
        $this->assertTrue($this->saintekAdmin->canAccessMahasiswa($this->inforStudent));
        $this->assertFalse($this->saintekAdmin->canAccessMahasiswa($this->fitkStudent));
        $this->assertFalse($this->saintekAdmin->canAccessMahasiswa($this->febiStudent));
    }

    public function test_fitk_admin_mahasiswa_index_lists_infor_and_fitk_students(): void
    {
        $response = $this->actingAs($this->fitkAdmin)->get(route('admin.mahasiswa.index'));
        $response->assertStatus(200);
        $response->assertSee($this->fitkStudent->nama);
        $response->assertDontSee($this->febiStudent->nama);
    }

    public function test_saintek_admin_mahasiswa_index_does_not_see_other_faculty_students(): void
    {
        $response = $this->actingAs($this->saintekAdmin)->get(route('admin.mahasiswa.index'));
        $response->assertStatus(200);
        $response->assertDontSee($this->fitkStudent->nama);
        $response->assertDontSee($this->febiStudent->nama);
    }

    public function test_fitk_admin_can_view_infor_student_show_page(): void
    {
        $response = $this->actingAs($this->fitkAdmin)->get(route('admin.mahasiswa.show', $this->inforStudent));
        $response->assertStatus(200);
    }

    public function test_fitk_admin_cannot_view_febi_student_show_page(): void
    {
        $response = $this->actingAs($this->fitkAdmin)->get(route('admin.mahasiswa.show', $this->febiStudent));
        $response->assertStatus(403);
    }

    public function test_saintek_admin_cannot_view_fitk_student_show_page(): void
    {
        $response = $this->actingAs($this->saintekAdmin)->get(route('admin.mahasiswa.show', $this->fitkStudent));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_master_data_pages(): void
    {
        $responseFakultas = $this->actingAs($this->superAdmin)->get(route('admin.fakultas.index'));
        $responseFakultas->assertStatus(200);

        $responseProdi = $this->actingAs($this->superAdmin)->get(route('admin.program-studi.index'));
        $responseProdi->assertStatus(200);

        $responseMk = $this->actingAs($this->superAdmin)->get(route('admin.mata-kuliah.index'));
        $responseMk->assertStatus(200);
    }

    public function test_no_duplicate_program_studi_names(): void
    {
        $duplicateCount = ProgramStudi::select('nama_program_studi')
            ->groupBy('nama_program_studi')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $this->assertEquals(0, $duplicateCount, 'There should be zero duplicate program studi names in the database.');
    }
}
