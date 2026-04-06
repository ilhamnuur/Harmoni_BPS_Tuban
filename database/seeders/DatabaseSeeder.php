<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Isi Data Tim
        $teamNames = [
            'Subbagian Umum',                           // ID 1
            'Tim Statistik Sosial',                    // ID 2
            'Tim Statistik Distribusi dan Jasa',       // ID 3
            'Tim Statistik Produksi',                  // ID 4
            'Tim Neraca Wilayah dan Analisis Statistik',// ID 5
            'Tim Pengolahan dan Layanan Statistik',    // ID 6
            'Tim Pembinaan Statistik Sektoral',        // ID 7
            'Kepala BPS'                               // ID 8
        ];

        foreach ($teamNames as $name) {
            Team::create(['nama_tim' => $name]);
        }

        // 2. Isi Data Tipe Aktivitas (Update: Tambah Dinas Luar)
        $types = [
            ['name' => 'Tugas Lapangan', 'description' => 'Kegiatan pengawasan atau pendataan di lapangan'],
            ['name' => 'Rapat', 'description' => 'Kegiatan rapat dinas dengan daftar hadir digital'],
            ['name' => 'Dinas Luar', 'description' => 'Kegiatan perjalanan dinas luar kantor/daerah'],
        ];

        foreach ($types as $type) {
            ActivityType::create($type);
        }

        // 3. Persiapan Password Seragam
        $password = Hash::make('password123');
        $now = Carbon::now();

        // Data User dengan penambahan Email dan penyesuaian Username
        $users = [
            // Utama & Testing
            ['nama_lengkap' => 'Kepala BPS Tuban', 'nip' => '197501011995011001', 'username' => 'kepala.bps', 'role' => 'Kepala', 'team_id' => 8, 'email' => 'kepala.tuban@bps.go.id'],
            ['nama_lengkap' => 'Ketua Tim', 'nip' => '198002022005011002', 'username' => 'ketua.tim', 'role' => 'Katim', 'team_id' => 2, 'email' => 'katim@bps.go.id'],
            ['nama_lengkap' => 'Pegawai', 'nip' => '199003032015011003', 'username' => 'pegawai', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'pegawai@bps.go.id'],
            ['nama_lengkap' => 'Admin Utama', 'nip' => '197006221992031042', 'username' => 'admin', 'role' => 'Admin', 'team_id' => null, 'email' => 'admin.bps@bps.go.id'],

            // Data Riil Pegawai
            ['nama_lengkap' => 'Wicaksono', 'nip' => '197008051990031001', 'username' => 'wicaksono', 'role' => 'Kepala', 'team_id' => 8, 'email' => 'wicaksono@bps.go.id'],
            ['nama_lengkap' => 'Dodik Hendarto Arief', 'nip' => '198809292011011008', 'username' => 'dodik.hendarto', 'role' => 'Katim', 'team_id' => 1, 'email' => 'dodik.hendarto@bps.go.id'],
            ['nama_lengkap' => 'Yudhi Prasetyono', 'nip' => '197008101989031001', 'username' => 'yudhi.prasetyono', 'role' => 'Katim', 'team_id' => 7, 'email' => 'yudhi.prasetyono@bps.go.id'],
            ['nama_lengkap' => "Zaidatul Ma'rifah", 'nip' => '197211051994122001', 'username' => 'zaidatul.marifah', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'ifahzm@bps.go.id'],
            ['nama_lengkap' => 'Eni Indiastuti', 'nip' => '196901011994012001', 'username' => 'eni.indiastuti', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'eni.indiastuti@bps.go.id'],
            ['nama_lengkap' => 'Arif Suroso', 'nip' => '197401262002121002', 'username' => 'arif.suroso', 'role' => 'Katim', 'team_id' => 5, 'email' => 'arif.suroso@bps.go.id'],
            ['nama_lengkap' => 'Ika Rahmawati', 'nip' => '198710192010032001', 'username' => 'ika.rahmawati', 'role' => 'Katim', 'team_id' => 3, 'email' => 'ikarahma@bps.go.id'],
            ['nama_lengkap' => 'Nuzul Djoko Susanto', 'nip' => '197902162002121003', 'username' => 'nuzul.djoko', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'nuzul.susanto@bps.go.id'],
            ['nama_lengkap' => 'Umdatul Ummah', 'nip' => '197611182002122001', 'username' => 'umdatul.ummah', 'role' => 'Katim', 'team_id' => 4, 'email' => 'umdatul.ummah@bps.go.id'],
            ['nama_lengkap' => 'Triana Pujilestari', 'nip' => '198503202014032002', 'username' => 'triana.puji', 'role' => 'Katim', 'team_id' => 6, 'email' => 'triana.puji@bps.go.id'],
            ['nama_lengkap' => 'Respati Yekti Wibowo', 'nip' => '198805112011011014', 'username' => 'respati.yekti', 'role' => 'Katim', 'team_id' => 2, 'email' => 'respati.wibowo@bps.go.id'],
            ['nama_lengkap' => 'Maryama Yuyinatun Mahmudah', 'nip' => '199401262016022001', 'username' => 'maryama.yuyinatun', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'maryama.yuyinatun@bps.go.id'],
            ['nama_lengkap' => 'Joko Suprijanto', 'nip' => '197801272006041013', 'username' => 'joko.suprijanto', 'role' => 'Pegawai', 'team_id' => 2, 'email' => 'joko.suprijanto@bps.go.id'],
            ['nama_lengkap' => "Nisa'ul Khusna", 'nip' => '199504302018022001', 'username' => 'nisaul.khusna', 'role' => 'Pegawai', 'team_id' => 3, 'email' => 'nisaul.khusna@bps.go.id'],
            ['nama_lengkap' => 'Mei Fadlillah Ningcahyanti', 'nip' => '199305292016022001', 'username' => 'mei.fadillah', 'role' => 'Pegawai', 'team_id' => 2, 'email' => 'mei.fadlillah@bps.go.id'],
            ['nama_lengkap' => 'Yasmina Salisa', 'nip' => '199504222018022002', 'username' => 'yasmina.salisa', 'role' => 'Pegawai', 'team_id' => 4, 'email' => 'yasmina.salisa@bps.go.id'],
            ['nama_lengkap' => 'Mohammad Ilham Nur Rohman', 'nip' => '199607092019121001', 'username' => 'ilham.nur', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'ilham.nur@bps.go.id'],
            ['nama_lengkap' => 'Rizky Wahyuningsih', 'nip' => '199601032019012001', 'username' => 'rizky.wahyuningsih', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'rizkyw@bps.go.id'],
            ['nama_lengkap' => 'Nanda Eka Putri R', 'nip' => '199408182019032001', 'username' => 'nanda.eka', 'role' => 'Pegawai', 'team_id' => 7, 'email' => 'nanda.ekaputri@bps.go.id'],
            ['nama_lengkap' => 'Agus Triyanto', 'nip' => '197110191994011001', 'username' => 'agus.triyanto', 'role' => 'Pegawai', 'team_id' => 5, 'email' => 'agus.triyanto@bps.go.id'],
            ['nama_lengkap' => 'Achmad Yunus', 'nip' => '197407011994011001', 'username' => 'achmad.yunus', 'role' => 'Pegawai', 'team_id' => 5, 'email' => 'achmad.yunus@bps.go.id'],
            ['nama_lengkap' => 'Andik Kusris Tanto', 'nip' => '197208101994011001', 'username' => 'andik.kusris', 'role' => 'Pegawai', 'team_id' => 3, 'email' => 'andik.tanto@bps.go.id'],
            ['nama_lengkap' => 'Ainul Alim', 'nip' => '197706092001121003', 'username' => 'ainul.alim', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'ainul.alim@bps.go.id'],
            ['nama_lengkap' => 'Andry Prasetyo', 'nip' => '197908012006041023', 'username' => 'andry.prasetyo', 'role' => 'Pegawai', 'team_id' => 4, 'email' => 'andry.prasetyo@bps.go.id'],
            ['nama_lengkap' => 'Suryo Tri Buwono', 'nip' => '198305312006041009', 'username' => 'suryo.tri', 'role' => 'Pegawai', 'team_id' => 3, 'email' => 'suryo.buwono@bps.go.id'],
            ['nama_lengkap' => 'Beny Sidharta', 'nip' => '197812032007101001', 'username' => 'beny.ssidharta', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'beny.ssidharta@bps.go.id'],
            ['nama_lengkap' => 'Endra Supriantomo', 'nip' => '198209062007011002', 'username' => 'endra.supriantomo', 'role' => 'Pegawai', 'team_id' => 3, 'email' => 'endra.supriantomo@bps.go.id'],
            ['nama_lengkap' => 'Akhmad Subkhan', 'nip' => '199001132012121002', 'username' => 'akhmad.subkhan', 'role' => 'Pegawai', 'team_id' => 2, 'email' => 'subkhan@bps.go.id'],
            ['nama_lengkap' => 'Haiza Maulana Arisa Putra', 'nip' => '199812022022031004', 'username' => 'haiza.maulana', 'role' => 'Pegawai', 'team_id' => 4, 'email' => 'haiza.putra@bps.go.id'],
            ['nama_lengkap' => 'Nanda Puji Sri Lestari', 'nip' => '199706252022032015', 'username' => 'nanda.puji', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'nanda.lestari@bps.go.id'],
            ['nama_lengkap' => 'Luky Kurnianto', 'nip' => '197211051994122001', 'username' => 'luky.kurnianto', 'role' => 'Pegawai', 'team_id' => 7, 'email' => 'luky.kurnianto@bps.go.id'],
            ['nama_lengkap' => 'Dimas Ferdyansyah', 'nip' => '200109112024121004', 'username' => 'dimas.ferdyansyah', 'role' => 'Pegawai', 'team_id' => 5, 'email' => 'dimas.ferdyansyah@bps.go.id'],
            ['nama_lengkap' => 'Anik Setyaningsih', 'nip' => '199503102025062003', 'username' => 'anik.setyaningsih', 'role' => 'Admin', 'team_id' => null, 'email' => 'anik.setyaningsih@bps.go.id'],
            ['nama_lengkap' => 'Dasriatun', 'nip' => '198304302025212029', 'username' => 'darsriatun', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'darsriatun-pppk@bps.go.id'],
            ['nama_lengkap' => 'Zaki Mubarok', 'nip' => '197505092025211022', 'username' => 'zaky.mubarok', 'role' => 'Pegawai', 'team_id' => 1, 'email' => 'zakimubarok-pppk@bps.go.id'],
            ['nama_lengkap' => 'Galuh Fianda Fantri', 'nip' => '199602072025212049', 'username' => 'galuh.fianda', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'galuhfantri-pppk@bps.go.id'],
            
            // Tambahan Pegawai Baru
            ['nama_lengkap' => 'Eko Hardiyanto', 'nip' => '', 'username' => 'eko.hardiyanto', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'eko.hardi@bps.go.id'],
            ['nama_lengkap' => 'Muhammad Ismail Putra', 'nip' => '', 'username' => 'muhammad.ismail', 'role' => 'Pegawai', 'team_id' => 6, 'email' => 'tugasismail7@gmail.com'],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}