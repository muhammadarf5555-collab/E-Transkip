$ErrorActionPreference = "Stop"

# Backup custom DatabaseSeeder.php
Move-Item -Path "database\seeders\DatabaseSeeder.php" -Destination "database\seeders\DatabaseSeeder.php.bak" -Force

# Create default DatabaseSeeder.php
$defaultSeeder = @"
<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
"@
Set-Content -Path "database\seeders\DatabaseSeeder.php" -Value $defaultSeeder

# Git Initialization and Adding Files
git init
git add .

# Unstage custom files
$customFiles = @(
    "app/Models/AuditLog.php",
    "app/Models/Fakultas.php",
    "app/Models/Mahasiswa.php",
    "app/Models/ProgramStudi.php",
    "app/Models/SiakadMahasiswa.php",
    "app/Models/SiakadMataKuliah.php",
    "app/Models/SiakadNilai.php",
    "app/Models/Transkrip.php",
    "app/Models/Wisudawan.php",
    "app/Http/Controllers/AuditLogController.php",
    "app/Http/Controllers/FakultasController.php",
    "app/Http/Controllers/MahasiswaController.php",
    "app/Http/Controllers/ProgramStudiController.php",
    "app/Http/Controllers/SiakadMahasiswaController.php",
    "app/Http/Controllers/SiakadMataKuliahController.php",
    "app/Http/Controllers/SiakadNilaiController.php",
    "app/Http/Controllers/TranskripController.php",
    "app/Http/Controllers/WisudawanController.php"
)

foreach ($file in $customFiles) {
    if (Test-Path $file) {
        git reset HEAD $file
    }
}

# Unstage migrations from today
$migrations = Get-ChildItem -Path "database\migrations\2026_09_20_*"
foreach ($mig in $migrations) {
    git reset HEAD $mig.FullName
}

# Commit
git commit -m "Instalasi awal Laravel 13"

# Restore DatabaseSeeder
Remove-Item -Path "database\seeders\DatabaseSeeder.php" -Force
Move-Item -Path "database\seeders\DatabaseSeeder.php.bak" -Destination "database\seeders\DatabaseSeeder.php" -Force

# Push to GitHub
git remote add origin https://github.com/muhammadarf5555-collab/E-Transkip.git
git branch -M main
git push -u origin main
