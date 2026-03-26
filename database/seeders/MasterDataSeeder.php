<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1) TRUNCATE: JANGAN DI DALAM TRANSACTION (MySQL auto-commit)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('case_symptoms')->truncate();
        DB::table('case_bases')->truncate();
        DB::table('diagnosis_symptoms')->truncate();
        DB::table('diagnoses')->truncate();
        DB::table('symptoms')->truncate();
        DB::table('damages')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2) INSERT: boleh pakai transaction
        DB::transaction(function () {

            // ===== 1) DAMAGES (16) =====
            $damages = [
                ['code'=>'H1','name'=>'Baterai drop / tidak mengisi','category'=>'hardware','description'=>null,'solution'=>'Cek adaptor/charger, cek health baterai, kalibrasi baterai, pertimbangkan ganti baterai.'],
                ['code'=>'H2','name'=>'Charger/adapter bermasalah','category'=>'hardware','description'=>null,'solution'=>'Cek output adaptor, coba adaptor lain, cek port DC-in, hindari adaptor non-original.'],
                ['code'=>'H3','name'=>'Hard disk/SSD bermasalah (bad sector/health turun)','category'=>'hardware','description'=>null,'solution'=>'Backup data segera, cek SMART/health, jalankan scan disk, pertimbangkan ganti HDD/SSD.'],
                ['code'=>'H4','name'=>'RAM bermasalah','category'=>'hardware','description'=>null,'solution'=>'Reseat RAM, tes satu per satu, bersihkan slot, jalankan memory test, ganti RAM jika perlu.'],
                ['code'=>'H5','name'=>'Overheat (kipas/heatsink/pasta)','category'=>'hardware','description'=>null,'solution'=>'Bersihkan kipas/heatsink, ganti thermal paste, pastikan airflow baik, gunakan cooling pad jika perlu.'],
                ['code'=>'H6','name'=>'Layar bermasalah (blank/flicker/backlight)','category'=>'hardware','description'=>null,'solution'=>'Cek kabel fleksibel, tes monitor eksternal, update driver GPU, cek panel/backlight.'],
                ['code'=>'H7','name'=>'Keyboard bermasalah (tombol mati/input sendiri)','category'=>'hardware','description'=>null,'solution'=>'Bersihkan keyboard, cek driver, test di safe mode, ganti keyboard jika kerusakan fisik.'],
                ['code'=>'H8','name'=>'Mainboard bermasalah (short/konslet/tidak menyala)','category'=>'hardware','description'=>null,'solution'=>'Lepas power, cek short, servis komponen pada board oleh teknisi, kemungkinan penggantian board.'],
                ['code'=>'S1','name'=>'OS corrupt / gagal boot','category'=>'software','description'=>null,'solution'=>'Gunakan recovery/startup repair, cek boot order, reinstall OS bila perlu, cek storage untuk memastikan tidak rusak.'],
                ['code'=>'S2','name'=>'Terkena malware/virus berat','category'=>'software','description'=>null,'solution'=>'Scan antivirus offline, bersihkan malware, reset browser, pertimbangkan reinstall OS jika parah.'],
                ['code'=>'S3','name'=>'Driver bermasalah (VGA/WiFi/Audio)','category'=>'software','description'=>null,'solution'=>'Reinstall/update driver resmi, rollback driver, update Windows, cek device manager.'],
                ['code'=>'S4','name'=>'Lemot parah karena startup/service (tanpa kerusakan hardware)','category'=>'software','description'=>null,'solution'=>'Disable startup, uninstall aplikasi berat, bersihkan temporary, cek background service, update OS.'],
                ['code'=>'S5','name'=>'Blue Screen (BSOD) sering','category'=>'software','description'=>null,'solution'=>'Cek driver, update OS, cek dump log, tes RAM dan storage karena BSOD bisa akibat hardware.'],
                ['code'=>'S6','name'=>'Storage penuh / sistem error low space','category'=>'software','description'=>null,'solution'=>'Kosongkan space, cleanup disk, pindah data, uninstall aplikasi, aktifkan storage sense.'],
                ['code'=>'S7','name'=>'Aplikasi sering crash / runtime error','category'=>'software','description'=>null,'solution'=>'Reinstall aplikasi, update dependency/runtime (VC++/.NET), scan file system, cek konflik driver.'],
                ['code'=>'S8','name'=>'WiFi/Network error karena konfigurasi','category'=>'software','description'=>null,'solution'=>'Reset network, forget & reconnect WiFi, flush DNS, cek proxy/VPN, update driver jika perlu.'],
            ];
            DB::table('damages')->insert($damages);
            $damageMap = DB::table('damages')->pluck('id', 'code')->toArray();

            // ===== 2) SYMPTOMS (30) =====
            $symptoms = [
                ['code'=>'G01','name'=>'Laptop tidak bisa masuk OS (gagal boot)','description'=>null],
                ['code'=>'G02','name'=>'Bootloop (restart terus)','description'=>null],
                ['code'=>'G03','name'=>'Muncul BSOD','description'=>null],
                ['code'=>'G04','name'=>'Lambat saat startup','description'=>null],
                ['code'=>'G05','name'=>'Sering freeze/hang','description'=>null],
                ['code'=>'G06','name'=>'Aplikasi sering crash / not responding','description'=>null],
                ['code'=>'G07','name'=>'Pop-up iklan/redirect aneh (indikasi malware)','description'=>null],
                ['code'=>'G08','name'=>'Antivirus tidak bisa update/ter-disable','description'=>null],
                ['code'=>'G09','name'=>'Tidak bisa nyala sama sekali','description'=>null],
                ['code'=>'G10','name'=>'Mati mendadak saat digunakan','description'=>null],
                ['code'=>'G11','name'=>'Baterai tidak mengisi (plugged in not charging)','description'=>null],
                ['code'=>'G12','name'=>'Baterai cepat habis','description'=>null],
                ['code'=>'G13','name'=>'Hanya bisa nyala jika charger terpasang','description'=>null],
                ['code'=>'G14','name'=>'Bunyi aneh dari storage (klik-klik HDD)','description'=>null],
                ['code'=>'G15','name'=>'Sering error disk / file corrupt','description'=>null],
                ['code'=>'G16','name'=>'Kapasitas storage cepat penuh','description'=>null],
                ['code'=>'G17','name'=>'Sering error memory / aplikasi tiba-tiba close','description'=>null],
                ['code'=>'G18','name'=>'Sering restart saat multitasking','description'=>null],
                ['code'=>'G19','name'=>'Laptop cepat panas','description'=>null],
                ['code'=>'G20','name'=>'Kipas berisik terus','description'=>null],
                ['code'=>'G21','name'=>'Frame drop/performa turun saat ringan','description'=>null],
                ['code'=>'G22','name'=>'Layar blank tapi lampu power hidup','description'=>null],
                ['code'=>'G23','name'=>'Layar berkedip/flicker/garis','description'=>null],
                ['code'=>'G24','name'=>'Brightness/backlight tidak normal (redup)','description'=>null],
                ['code'=>'G25','name'=>'Keyboard beberapa tombol tidak berfungsi','description'=>null],
                ['code'=>'G26','name'=>'Keyboard mengetik sendiri','description'=>null],
                ['code'=>'G27','name'=>'WiFi tidak muncul/disable sendiri','description'=>null],
                ['code'=>'G28','name'=>'WiFi sering putus','description'=>null],
                ['code'=>'G29','name'=>'Suara tidak keluar (audio)','description'=>null],
                ['code'=>'G30','name'=>'Device manager tanda seru / driver error','description'=>null],
            ];
            DB::table('symptoms')->insert($symptoms);
            $symptomMap = DB::table('symptoms')->pluck('id', 'code')->toArray();

            // ===== 3) CASE BASES (20) =====
            $cases = [
                ['C001','H1', [['G11',5],['G12',4],['G13',5],['G10',3]]],
                ['C002','H2', [['G13',5],['G11',4],['G09',2]]],
                ['C003','H3', [['G14',5],['G15',5],['G05',3],['G01',2]]],
                ['C004','H3', [['G15',5],['G05',4],['G04',3],['G01',2]]],
                ['C005','H4', [['G17',5],['G18',5],['G05',3],['G03',2]]],
                ['C006','H5', [['G19',5],['G20',4],['G21',4],['G10',2]]],
                ['C007','H6', [['G22',5],['G23',4],['G24',3]]],
                ['C008','H7', [['G25',5],['G26',4]]],
                ['C009','H8', [['G09',5],['G13',3],['G10',4]]],
                ['C010','S1', [['G01',5],['G02',4],['G15',2]]],
                ['C011','S2', [['G07',5],['G08',4],['G04',3],['G05',2]]],
                ['C012','S3', [['G30',5],['G29',3],['G27',3],['G21',2]]],
                ['C013','S4', [['G04',5],['G05',3],['G16',2]]],
                ['C014','S5', [['G03',5],['G18',3],['G17',3],['G30',2]]],
                ['C015','S6', [['G16',5],['G04',3],['G05',2]]],
                ['C016','S7', [['G06',5],['G05',3],['G30',2]]],
                ['C017','S8', [['G27',5],['G28',4]]],
                ['C018','S5', [['G03',5],['G05',4],['G15',3]]],
                ['C019','H5', [['G19',5],['G21',4],['G04',3]]],
                ['C020','S3', [['G27',4],['G30',5],['G28',3]]],
            ];

            foreach ($cases as [$caseCode, $damageCode, $symList]) {
                $caseId = DB::table('case_bases')->insertGetId([
                    'case_code' => $caseCode,
                    'damage_id' => $damageMap[$damageCode],
                    'note' => 'Seed case '.$caseCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($symList as [$symCode, $weight]) {
                    DB::table('case_symptoms')->insert([
                        'case_base_id' => $caseId,
                        'symptom_id' => $symptomMap[$symCode],
                        'weight' => $weight,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }
}