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
        $now = Carbon::now();

        // 1. Isi Data Tim
        $teams = [
            'Subbagian Umum',                           // ID 1
            'Tim Statistik Sosial',                    // ID 2
            'Tim Statistik Distribusi dan Jasa',       // ID 3
            'Tim Statistik Produksi',                  // ID 4
            'Tim Neraca Wilayah dan Analisis Statistik',// ID 5
            'Tim Pengolahan dan Layanan Statistik',    // ID 6
            'Tim Pembinaan Statistik Sektoral',        // ID 7
            'Kepala BPS'                               // ID 8
        ];

        foreach ($teams as $t) {
            Team::create(['nama_tim' => $t]);
        }

        // 2. Isi Data Tipe Aktivitas
        $types = [
            ['name' => 'Tugas Lapangan', 'description' => 'Kegiatan pengawasan atau pendataan di lapangan'],
            ['name' => 'Rapat', 'description' => 'Kegiatan rapat dinas dengan daftar hadir digital'],
        ];

        foreach ($types as $type) {
            ActivityType::create($type);
        }

        // 3. Isi Data User Lengkap dengan NIP dan Timestamps
        $users = [
            ['id' => 1, 'nama_lengkap' => 'Kepala BPS Tuban', 'nip' => '197501011995011001', 'username' => 'kepala_bps', 'password' => '$2y$12$DV0piQJl9qco7zR/adAEdeE75YQ6i2iP5HMZVqa3ZPOeJjY9/BtQO', 'role' => 'Admin', 'team_id' => 8, 'created_at' => '2026-02-09 05:29:44', 'updated_at' => '2026-02-09 05:29:44'],
            ['id' => 2, 'nama_lengkap' => 'Ketua Tim Statistik', 'nip' => '198002022005011002', 'username' => 'katim_survei', 'password' => '$2y$12$6DTzkntIqt.DDubF.KGJOe/JE1yKJpc2vZ8/Wexy.41Ftn606Iu6O', 'role' => 'Katim', 'team_id' => 2, 'created_at' => '2026-02-09 05:29:44', 'updated_at' => '2026-02-09 05:29:44'],
            ['id' => 3, 'nama_lengkap' => 'Muhammad Ismail Putra', 'nip' => '199003032015011003', 'username' => 'Ismail', 'password' => '$2y$12$ixc3qCQtA24CfgcMjDfejODGfiheYGpcCtpm7pHD1PDLAzc4J5AHm', 'role' => 'Katim', 'team_id' => 1, 'created_at' => '2026-02-09 05:29:45', 'updated_at' => '2026-02-11 19:15:10'],
            ['id' => 4, 'nama_lengkap' => 'Lulus Haryono', 'nip' => '197804042000031004', 'username' => 'lulus', 'password' => '$2y$12$8MvuJ2wosdoqVUsFZJEtD.X5QzTNfNweyXS/WR5x7Ci0UbfrnRFIm', 'role' => 'Katim', 'team_id' => 1, 'created_at' => '2026-02-11 19:18:36', 'updated_at' => '2026-02-11 19:18:36'],
            ['id' => 5, 'nama_lengkap' => 'Respati Yekti Wibowo', 'nip' => '198205052008011005', 'username' => 'respati.yekti', 'password' => '$2y$12$XzObzJe5E0zrNYfAo09H5uLeDZ3h32lMV8GUSR4Dh8u0YfMs7P0wi', 'role' => 'Katim', 'team_id' => 2, 'created_at' => '2026-02-11 19:19:17', 'updated_at' => '2026-02-11 19:19:17'],
            ['id' => 6, 'nama_lengkap' => 'Umdatul Ummah', 'nip' => '198506062010012006', 'username' => 'umdatul.ummah', 'password' => '$2y$12$XQ34gHrjW.3gLZEgDso.oeKVuNaRbkaTwmhAMPYcGG/cIa/MaJHfe', 'role' => 'Katim', 'team_id' => 4, 'created_at' => '2026-02-11 19:19:56', 'updated_at' => '2026-02-11 19:19:56'],
            ['id' => 7, 'nama_lengkap' => 'Ika Rahmawati', 'nip' => '198807072012012007', 'username' => 'ika.rahmawati', 'password' => '$2y$12$8Nri1IjJutjAUsg1Idxd1unMh6wPGdFlwN6Cd9G2K8OkQFfWPNKH6', 'role' => 'Katim', 'team_id' => 3, 'created_at' => '2026-02-11 19:20:24', 'updated_at' => '2026-02-11 19:20:24'],
            ['id' => 8, 'nama_lengkap' => 'Arif Suroso', 'nip' => '197608081998031008', 'username' => 'arif.suroso', 'password' => '$2y$12$5/tRV36veNA9zpioGp6YDObxPeZYkr2HjgHL2P2M8AOS1GT7RmPhu', 'role' => 'Katim', 'team_id' => 5, 'created_at' => '2026-02-11 19:20:58', 'updated_at' => '2026-02-11 19:20:58'],
            ['id' => 9, 'nama_lengkap' => 'Triana Pujilestari', 'nip' => '198309092006042009', 'username' => 'triana.puji', 'password' => '$2y$12$QEEEz2VME5oki52hPzmB6e/kZGqJYuw/o1jd.4sQvZYkSwXOvKkjO', 'role' => 'Katim', 'team_id' => 6, 'created_at' => '2026-02-11 19:21:40', 'updated_at' => '2026-02-11 19:21:40'],
            ['id' => 10, 'nama_lengkap' => 'Yudhi Prasetyono', 'nip' => '197910102002121010', 'username' => 'yudhi.prasetyono', 'password' => '$2y$12$D5EOJ6zRMRuEwdbheiliC.3lKhUR9vJoHm4FHWP/Y2NEu3cT5CQ.W', 'role' => 'Katim', 'team_id' => 7, 'created_at' => '2026-02-11 19:22:30', 'updated_at' => '2026-02-11 19:22:30'],
            ['id' => 11, 'nama_lengkap' => 'Eni Indiastuti', 'nip' => '198111112009012011', 'username' => 'eni.indiastuti', 'password' => '$2y$12$raDWu.f3A/uKNYGCsDZwv.ZtHfNhN1kmU6bT5cj6fbLQq5nplmEPq', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:27:27', 'updated_at' => '2026-02-11 19:27:27'],
            ['id' => 12, 'nama_lengkap' => 'Rizky Wahyunigsih', 'nip' => '199512122018012012', 'username' => 'rizky.wahyuningsih', 'password' => '$2y$12$n6v0IiRLbTEtFZQ2uo4QYud9PF93uNmuQL4rA0l/kcIRPwbke5RSK', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:33:41', 'updated_at' => '2026-02-11 19:33:41'],
            ['id' => 13, 'nama_lengkap' => 'Ainul Alim', 'nip' => '199201132015031013', 'username' => 'ainul.alim', 'password' => '$2y$12$OlbsKH/GA9MVDlcsfVPLd.ufpYUJfLRswsYSSUZRcfc5DppYjJhGO', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:34:10', 'updated_at' => '2026-02-11 19:34:10'],
            ['id' => 14, 'nama_lengkap' => 'Nanda Puji Sri Lestari', 'nip' => '199402142017012014', 'username' => 'nanda.puji', 'password' => '$2y$12$cnZIm7wSVk.3P0xr4T3jkeAI5Mmdk0dycodZWtKV5UHJSMzgYffOS', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:34:39', 'updated_at' => '2026-02-11 19:34:39'],
            ['id' => 15, 'nama_lengkap' => 'Anik Setyaningsih', 'nip' => '198403152008012015', 'username' => 'anik.setyaningsih', 'password' => '$2y$12$eypwhj9aYn800txmz30hrOllugNZbEThmN71Lv3EPV0g3VnysAWUy', 'role' => 'Admin', 'team_id' => 1, 'created_at' => '2026-02-11 19:35:20', 'updated_at' => '2026-02-12 19:35:05'],
            ['id' => 16, 'nama_lengkap' => 'Darsriatun', 'nip' => '198704162011012016', 'username' => 'darsriatun', 'password' => '$2y$12$AW9w0XtCaUUo95KhIg5S/.LhNNEU1hKNv9pMYYaHOo5rMRGAbh8xy', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:35:52', 'updated_at' => '2026-02-11 19:35:52'],
            ['id' => 17, 'nama_lengkap' => 'Zaky Mubarok', 'nip' => '199605172020011017', 'username' => 'zaky.mubarok', 'password' => '$2y$12$RGp35ToPCzjy4q1XBmFYOOksxScowlgWzDZhBCge2nD40ENIXSxMW', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:36:21', 'updated_at' => '2026-02-11 19:36:21'],
            ['id' => 18, 'nama_lengkap' => 'Susik Susanto', 'nip' => '198106182006041018', 'username' => 'susik.susanto', 'password' => '$2y$12$glHJ443qBf.1aD29xAHw2uYnqBNK0InDAVEqjtUPjC.UPVcOv5q3O', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:36:49', 'updated_at' => '2026-02-11 19:36:49'],
            ['id' => 19, 'nama_lengkap' => 'Johan Ardianto', 'nip' => '198907192014021019', 'username' => 'johan.ardianto', 'password' => '$2y$12$.7AfQUW5oPYyafSsB/ncPuBjcvosHRUCVWfZtrLRwEj8nIxCgvpVG', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:37:18', 'updated_at' => '2026-02-11 19:37:18'],
            ['id' => 20, 'nama_lengkap' => 'Mei Fadillah Ningcahyanti', 'nip' => '199708202021012020', 'username' => 'mei.fadillah', 'password' => '$2y$12$eKFSqW2sgZt/1ifp6RLgD.BB7Q/tvLH82sHX5cXKl3MJUzdKOBQ92', 'role' => 'Pegawai', 'team_id' => 2, 'created_at' => '2026-02-11 19:38:09', 'updated_at' => '2026-02-11 19:38:09'],
            ['id' => 21, 'nama_lengkap' => 'Joko Suprijanto', 'nip' => '197509211998011021', 'username' => 'joko.suprijanto', 'password' => '$2y$12$8pii/x.PsxvIHlrQndRGHuyYVqU3tLhRSOCLtf.PC/IpcH6hwSbHK', 'role' => 'Pegawai', 'team_id' => 2, 'created_at' => '2026-02-11 19:38:42', 'updated_at' => '2026-02-11 19:38:42'],
            ['id' => 22, 'nama_lengkap' => 'Akhmad Subkhan', 'nip' => '198010222005011022', 'username' => 'akhmad.subkhan', 'password' => '$2y$12$9FK6sERl1IfR4w8sZGr5F.9zhwkVaBUZ2tqQHhk4LoAWaYgQfe26.', 'role' => 'Pegawai', 'team_id' => 2, 'created_at' => '2026-02-11 19:39:56', 'updated_at' => '2026-02-11 19:39:56'],
            ['id' => 23, 'nama_lengkap' => 'Yasmina Salisa', 'nip' => '199811232022032023', 'username' => 'yasmina.salisa', 'password' => '$2y$12$L/rPcQbf3NtnBhBadvf4WOxM5DwuKCubRURv/Zps84K1aTWgrKmrO', 'role' => 'Pegawai', 'team_id' => 4, 'created_at' => '2026-02-11 19:40:32', 'updated_at' => '2026-02-11 19:40:32'],
            ['id' => 24, 'nama_lengkap' => 'Andry Prasetyo', 'nip' => '198212242009011024', 'username' => 'andry.prasetyo', 'password' => '$2y$12$pjkuc11Vj71z1unqACDWe.nfJG9u.CgjbOsRVjj2VrW7W88pc2UcO', 'role' => 'Pegawai', 'team_id' => 4, 'created_at' => '2026-02-11 19:41:25', 'updated_at' => '2026-02-11 19:41:25'],
            ['id' => 25, 'nama_lengkap' => 'Haiza Maulana Arisa Putra', 'nip' => '200001252023011025', 'username' => 'haiza.maulana', 'password' => '$2y$12$IfFeG8sQFoWbqCYfAPtUCe2Ts3JXK2/0WLHsxdfS3T6zh593qZ8iq', 'role' => 'Pegawai', 'team_id' => 4, 'created_at' => '2026-02-11 19:42:19', 'updated_at' => '2026-02-11 19:42:19'],
            ['id' => 26, 'nama_lengkap' => 'Wicaksono', 'nip' => '198402262008011026', 'username' => 'wicaksono', 'password' => '$2y$12$zZ0wtOpEHZMn4CFsZiFww.pQegwZSwCz8bmKNms9ejA0WiqPtVYoW', 'role' => 'Pegawai', 'team_id' => 3, 'created_at' => '2026-02-11 19:42:46', 'updated_at' => '2026-02-11 19:42:46'],
            ['id' => 27, 'nama_lengkap' => "Nisa'Ul Khusna", 'nip' => '199303272016012027', 'username' => 'nisa', 'password' => '$2y$12$j7xDLoGmCZKpuJyRaWbXg.yqYdYls9OBV8RHP1Fu6au275tUcYbzi', 'role' => 'Pegawai', 'team_id' => 3, 'created_at' => '2026-02-11 19:43:28', 'updated_at' => '2026-02-11 19:43:28'],
            ['id' => 28, 'nama_lengkap' => 'Andik Kusris Tanto', 'nip' => '198604282010011028', 'username' => 'andik.kusris', 'password' => '$2y$12$zsdS.a4l7WHAXVg2nHUX7eCJgL3XNtDP/n9UlH2GRr7TANabeHZUK', 'role' => 'Pegawai', 'team_id' => 3, 'created_at' => '2026-02-11 19:44:00', 'updated_at' => '2026-02-11 19:44:00'],
            ['id' => 29, 'nama_lengkap' => 'Suryo Tri Buwono', 'nip' => '198805292012011029', 'username' => 'suryo.tri', 'password' => '$2y$12$fEiEOzpGA0F/nS2F2XzG8.y8S.npMtyTNxqvYUSZ.6e98LHvpNbX6', 'role' => 'Pegawai', 'team_id' => 3, 'created_at' => '2026-02-11 19:44:22', 'updated_at' => '2026-02-11 19:44:22'],
            ['id' => 30, 'nama_lengkap' => 'Endra Supriantomo', 'nip' => '198106302006041030', 'username' => 'endra.supriantomo', 'password' => '$2y$12$Ofpo.rPhNu1F0YNlZ8pR5ux03S7y3tzBq3bfp0xdQ1AWUURR8ndZS', 'role' => 'Pegawai', 'team_id' => 3, 'created_at' => '2026-02-11 19:44:54', 'updated_at' => '2026-02-11 19:44:54'],
            ['id' => 31, 'nama_lengkap' => 'Dimas Ferdyansyah', 'nip' => '199907312022031031', 'username' => 'dimas.feryansyah', 'password' => '$2y$12$fhOGVbkZ7Yxwr81nnAz12.lYPHcGON7RHwWxmv6hvqDkzpUf0HtHy', 'role' => 'Pegawai', 'team_id' => 5, 'created_at' => '2026-02-11 19:45:34', 'updated_at' => '2026-02-11 19:45:34'],
            ['id' => 32, 'nama_lengkap' => 'Achmad Yunus', 'nip' => '198508302010011032', 'username' => 'achmad.yunus', 'password' => '$2y$12$2RKpT/XUWZv32LLdOR9VjOyBtck7yLR1/87mYOFA2mF0zyEIHlyAO', 'role' => 'Pegawai', 'team_id' => 5, 'created_at' => '2026-02-11 19:46:05', 'updated_at' => '2026-02-11 19:46:05'],
            ['id' => 33, 'nama_lengkap' => 'Agus Triyanto', 'nip' => '197709121999031033', 'username' => 'agus.triyanto', 'password' => '$2y$12$8GEU6TWvh9GoAY6gBGT1OOYO2Kr60.vMAEnjpVZXwlslxIYqYskIC', 'role' => 'Pegawai', 'team_id' => 5, 'created_at' => '2026-02-11 19:46:43', 'updated_at' => '2026-02-11 19:46:43'],
            ['id' => 34, 'nama_lengkap' => 'Nuzul Djoko Susanto', 'nip' => '197410241996031034', 'username' => 'nuzul.djoko', 'password' => '$2y$12$ly9o/LINYb.fSyu1/n9JOORBmY8f6DxW.NL0Y3sMY.0xpw4nhsMpa', 'role' => 'Pegawai', 'team_id' => 6, 'created_at' => '2026-02-11 19:47:15', 'updated_at' => '2026-02-11 19:47:15'],
            ['id' => 35, 'nama_lengkap' => 'M. Ilham Nur Rohman', 'nip' => '200111252024011035', 'username' => 'ilham.nur', 'password' => '$2y$12$7Wv8/8Zf46XlXKm77R5yEuavnAbA7M5SdyNnJLcL4Du/bHTRdeMym', 'role' => 'Pegawai', 'team_id' => 6, 'created_at' => '2026-02-11 19:47:47', 'updated_at' => '2026-02-11 19:47:47'],
            ['id' => 36, 'nama_lengkap' => 'Beny Ssidharta', 'nip' => '198312262009011036', 'username' => 'beny.ssidharta', 'password' => '$2y$12$arDU19Ur5wcKuM9kyGGpeOgg79.fn9bzX3Uj2N7dsju52kn054MfW', 'role' => 'Pegawai', 'team_id' => 6, 'created_at' => '2026-02-11 19:48:30', 'updated_at' => '2026-02-11 19:48:30'],
            ['id' => 37, 'nama_lengkap' => 'Galuh Fianda Fantri', 'nip' => '199501172018012037', 'username' => 'galuh.fianda', 'password' => '$2y$12$QyqbujT1CUMY.eGzQArZQ.KYE3eM.lRbpIuWN048YFjfEIHoH9aCq', 'role' => 'Pegawai', 'team_id' => 6, 'created_at' => '2026-02-11 19:50:50', 'updated_at' => '2026-02-11 19:50:50'],
            ['id' => 38, 'nama_lengkap' => 'Nanda Eka Putri R', 'nip' => '199802282021012038', 'username' => 'nanda.eka', 'password' => '$2y$12$l6Vp8tXRW8hIVdAkZrYYLO8zm5Wr8ewbURFm2ttb464OKoQBgabBe', 'role' => 'Pegawai', 'team_id' => 7, 'created_at' => '2026-02-11 19:51:29', 'updated_at' => '2026-02-11 19:51:29'],
            ['id' => 40, 'nama_lengkap' => 'Maryama Yuyinatun M', 'nip' => '198204202006042040', 'username' => 'maryama.yuyinatun', 'password' => '$2y$12$3bsk.gF9RgBbBi8ySqAXneeOqz.4SdRqgFXtaMVEjDbmxAKjWzleS', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:52:27', 'updated_at' => '2026-02-11 19:52:27'],
            ['id' => 41, 'nama_lengkap' => 'M. Ali Imron', 'nip' => '198605212010011041', 'username' => 'ali.imron', 'password' => '$2y$12$UKGdmQEoftc0DTkepE.M6OXnaBinkquw6OLC.apaMdiacvsHGQXTu', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:53:00', 'updated_at' => '2026-02-11 19:57:43'],
            ['id' => 42, 'nama_lengkap' => 'Andhie Surya Mustari', 'nip' => '197006221992031042', 'username' => 'andhie.surya', 'password' => '$2y$12$AqUgDInMefR3nujsbsHj3ecEkbNEQ6FkQwBpxtBY6QozRRx7UU14.', 'role' => 'Admin', 'team_id' => 8, 'created_at' => '2026-02-11 19:54:13', 'updated_at' => '2026-02-11 21:46:58'],
            ['id' => 43, 'nama_lengkap' => "Zaidatul Ma'rifah", 'nip' => '199107232014022043', 'username' => 'zaidatul', 'password' => '$2y$12$igqhXF3SKyBkm0Kp4b69Tu7mlo.tnq9zdaqzVJa2qymVev7oZBshm', 'role' => 'Pegawai', 'team_id' => 1, 'created_at' => '2026-02-11 19:55:07', 'updated_at' => '2026-02-11 19:55:07'],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}