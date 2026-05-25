<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratTemplate;

class SuratTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Surat Keputusan (SK)
        SuratTemplate::updateOrCreate(
            ['kode' => 'SK'], // KUNCI UTAMA (Hanya cek berdasarkan kode unik)
            [
                'nama' => 'Surat Keputusan',
                'klasifikasi' => 'SK',
                'lampiran' => '-',
                'jenis_surat' => 'keputusan',
                'fields' => [
                    'menimbang' => 'textarea',
                    'mengingat' => 'textarea',
                    'memperhatikan' => 'textarea',
                    'memutuskan' => 'textarea',
                    'ditetapkan_di' => 'text',
                    'tanggal_penetapan' => 'date',
                    'nama_ketua' => 'text',
                    'nama_sekretaris' => 'text',
                ],
                'konten' => "KEPUTUSAN\n\nNomor: {nomor_surat}\n\nMenimbang : {menimbang}\n\nMengingat : {mengingat}\n\nMemperhatikan : {memperhatikan}\n\nMEMUTUSKAN\n\n{memutuskan}\n\nDitetapkan di {ditetapkan_di}\npada tanggal {tanggal_penetapan}\n\nKETUA,\n\n{nama_ketua}\n\nSEKRETARIS,\n\n{nama_sekretaris}",
                'urutan' => 1,
                'is_active' => true,
                'has_attachment' => false,
                'status' => 'aktif',
            ]
        );

        // 2. Surat Tugas (ST)
        SuratTemplate::updateOrCreate(
            ['kode' => 'ST'], // KUNCI UTAMA
            [
                'nama' => 'Surat Tugas',
                'klasifikasi' => 'ST',
                'lampiran' => '-',
                'jenis_surat' => 'tugas',
                'fields' => [
                    'nama_diberi_tugas' => 'text',
                    'ttl' => 'text',
                    'jabatan' => 'text',
                    'alamat' => 'textarea',
                    'kegiatan' => 'text',
                    'penyelenggara' => 'text',
                    'tanggal_pelaksanaan' => 'text',
                    'nama_ketua' => 'hidden',
                    'nama_sekretaris' => 'hidden',
                    'nia_ketua' => 'hidden',
                    'nia_sekretaris' => 'hidden',
                ],
                'konten' => '<div style="text-align: center; font-weight: bold; margin-bottom: 5px; letter-spacing: 5px; text-decoration: underline;">SURAT TUGAS</div>
<div style="text-align: center; margin-bottom: 25px;">Nomor : {nomor_surat}</div>

<div style="text-align: left; font-style: italic; margin-bottom: 15px;">Bismillahirrahmanirrahim</div>

<div style="margin-bottom: 15px; text-align: justify;">
    {nama_organisasi_lengkap} memberikan tugas kepada :
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; border: none;">
    <tbody>
        <tr>
            <td style="width: 5%; vertical-align: top; border: none; text-align: center;">1.</td>
            <td style="width: 25%; vertical-align: top; border: none;">Nama</td>
            <td style="width: 2%; vertical-align: top; border: none; text-align: center;">:</td>
            <td style="width: 68%; vertical-align: top; border: none;">{nama_diberi_tugas}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; border: none;"></td>
            <td style="vertical-align: top; border: none;">Tempat / tanggal lahir</td>
            <td style="vertical-align: top; border: none; text-align: center;">:</td>
            <td style="vertical-align: top; border: none;">{ttl}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; border: none;"></td>
            <td style="vertical-align: top; border: none;">Jabatan</td>
            <td style="vertical-align: top; border: none; text-align: center;">:</td>
            <td style="vertical-align: top; border: none;">{jabatan}</td>
        </tr>
        <tr>
            <td style="vertical-align: top; border: none;"></td>
            <td style="vertical-align: top; border: none;">Alamat</td>
            <td style="vertical-align: top; border: none; text-align: center;">:</td>
            <td style="vertical-align: top; border: none;">{alamat}</td>
        </tr>
    </tbody>
</table>

<div style="margin-bottom: 15px; text-align: justify; line-height: 1.5;">
    Untuk mengikuti {kegiatan} yang diselenggarakan oleh {penyelenggara} pada tanggal {tanggal_pelaksanaan}.
</div>

<div style="margin-bottom: 15px; text-align: justify;">
    Demikian Surat Tugas ini dibuat, untuk dipergunakan sebagaimana mestinya.
</div>

<div style="text-align: left; font-style: italic; margin-bottom: 20px;">
    Wallahulmuwafiq ila aqwamiththariq
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; border: none;">
    <tbody>
        <tr>
            <td style="width: 60%; border: none;"></td>
            <td style="width: 40%; text-align: right; border: none; padding: 0;">
                {nama_wilayah}, <span style="border-bottom: 1px solid #000; display: inline-block; padding-bottom: 1px;">{tanggal_hijriah}</span><br>
                <span>{tanggal_masehi_formatted} M</span>
            </td>
        </tr>
    </tbody>
</table>

<div style="text-align: center; font-weight: bold; margin-bottom: 25px; line-height: 1.3;">
    {tingkat_organisasi_upper}<br>
    {nama_organisasi_lengkap_baris2}<br>
    {nama_wilayah_upper}
</div>

<table class="tanda-tangan" style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 25px;">
    <tbody>
        <tr>
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                Ketua
                <div style="height: 75px;"></div> 
                <strong style="text-decoration: underline;">{nama_ketua}</strong><br>
                NIA. {nia_ketua}
            </td>
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                Sekretaris
                <div style="height: 75px;"></div> 
                <strong style="text-decoration: underline;">{nama_sekretaris}</strong><br>
                NIA. {nia_sekretaris}
            </td>
        </tr>
    </tbody>
</table>',
                'urutan' => 2,
                'is_active' => true,
                'has_attachment' => false,
                'status' => 'aktif',
            ]
        );

        // 3. Surat Undangan (SU)
        SuratTemplate::updateOrCreate(
            ['kode' => 'SU'], // KUNCI UTAMA
            [
                'nama' => 'Surat Undangan',
                'klasifikasi' => 'SU',
                'lampiran' => '-',
                'jenis_surat' => 'undangan',
                'fields' => [
                    'perihal' => 'text',
                    'tujuan' => 'text',
                    'acara' => 'text',
                    'hari' => 'text',
                    'tanggal' => 'date',
                    'waktu' => 'text',
                    'tempat' => 'text',
                    'nama_ketua' => 'text',
                    'nama_sekretaris' => 'text',
                ],
                'konten' => "SURAT UNDANGAN\n\nNomor: {nomor_surat}\n\nPerihal: {perihal}\n\nYth. {tujuan}\n\nAssalamu'alaikum Wr. Wb.\n\nDengan hormat, kami mengundang saudara untuk menghadiri:\n\nAcara: {acara}\nHari/Tanggal: {hari}, {tanggal}\nWaktu: {waktu}\nTempat: {tempat}\n\nDemikian undangan ini kami sampaikan.\n\nWassalamu'alaikum Wr. Wb.\n\nKETUA,\n\n{nama_ketua}\n\nSEKRETARIS,\n\n{nama_sekretaris}",
                'urutan' => 3,
                'is_active' => true,
                'has_attachment' => false,
                'status' => 'aktif',
            ]
        );

        // 4. Surat Keterangan (SKT)
        SuratTemplate::updateOrCreate(
            ['kode' => 'SKT'], // KUNCI UTAMA
            [
                'nama' => 'Surat Keterangan',
                'klasifikasi' => 'SKT',
                'lampiran' => '-',
                'jenis_surat' => 'keterangan',
                'fields' => [
                    'nik' => 'text',
                    'nama' => 'text',
                    'tempat_lahir' => 'text',
                    'tanggal_lahir' => 'date',
                    'alamat' => 'textarea',
                    'keterangan' => 'textarea',
                    'keperluan' => 'text',
                    'nama_ketua' => 'text',
                    'nama_sekretaris' => 'text',
                ],
                'konten' => "SURAT KETERANGAN\n\nNomor: {nomor_surat}\n\nYang bertanda tangan di bawah ini menerangkan bahwa:\n\nNIK : {nik}\nNama : {nama}\nTempat, Tgl Lahir : {tempat_lahir}, {tanggal_lahir}\nAlamat : {alamat}\n\nKeterangan : {keterangan}\n\nSurat ini dibuat untuk keperluan {keperluan}.\n\nDemikian surat keterangan ini dibuat untuk dapat digunakan sebagaimana mestinya.\n\nKETUA,\n\n{nama_ketua}\n\nSEKRETARIS,\n\n{nama_sekretaris}",
                'urutan' => 4,
                'is_active' => true,
                'has_attachment' => false,
                'status' => 'aktif',
            ]
        );

        // 5. Surat Rekomendasi Pengesahan (SRP)
        SuratTemplate::updateOrCreate(
            ['kode' => 'SRP'], // KUNCI UTAMA
            [
                'nama' => 'Surat Rekomendasi Pengesahan',
                'klasifikasi' => 'SRP',
                'lampiran' => '1 (susunan pengurus)',
                'jenis_surat' => 'pengesahan',
                'fields' => [
                    'kop_organisasi' => 'hidden',
                    'nama_desa' => 'text',
                    'nama_desa_lower' => 'hidden',
                    'status_desa_lower' => 'hidden',
                    'masa_bhakti' => 'text',
                    'status_desa' => 'text',
                    'surat_ranting_nomor' => 'text',
                    'surat_ranting_tanggal' => 'date',
                    'surat_prnu_nomor' => 'text',
                    'tingkat_organisasi' => 'hidden',
                    'tingkat_organisasi_upper' => 'hidden',
                    'nama_wilayah' => 'hidden',
                    'nama_wilayah_upper' => 'hidden',
                    'nama_organisasi_upper' => 'hidden',
                    'alamat_organisasi' => 'hidden',
                    'email_organisasi' => 'hidden',
                    'nama_organisasi_lengkap' => 'hidden',
                    'pembuka_surat' => 'hidden',
                    'nama_ketua' => 'hidden',
                    'nia_ketua' => 'hidden',
                    'nama_sekretaris' => 'hidden',
                    'nia_sekretaris' => 'hidden',
                    'tanggal_hijriah' => 'hidden',
                    'tanggal_masehi' => 'hidden',
                    'ttd_ketua' => 'hidden',
                    'ttd_sekretaris' => 'hidden',
                    'stempel' => 'hidden',
                ],
                'konten' => '<div style="text-align: center; font-weight: bold; margin-bottom: 5px; text-decoration: underline;">SURAT REKOMENDASI PENGESAHAN</div>
<div style="text-align: center; margin-bottom: 15px;">Nomor: {nomor_surat}</div>

<div style="text-align: center; margin-bottom: 15px;">
Tentang<br>
<strong>SUSUNAN PENGURUS PIMPINAN RANTING</strong><br>
<strong>IKATAN PELAJAR NAHDLATUL ULAMA\' {status_desa} {nama_desa}</strong><br>
<strong>MASA BAKTI {masa_bhakti}</strong>
</div>

<div style="text-align: left; font-style: italic; margin-bottom: 15px;">Bismillahirrahmanirrahim</div>
<div style="margin-bottom: 10px;">{nama_organisasi_lengkap}, setelah:</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; border: none;">
    <tbody>
        <tr>
            <td style="width: 110px; vertical-align: top; font-weight: bold; border: none;">Menimbang</td>
            <td style="width: 15px; vertical-align: top; text-align: center; border: none;">:</td>
            <td style="vertical-align: top; text-align: justify; border: none;">
                <div style="margin-bottom: 3px;">1. Bahwa untuk menjamin adanya keberlangsungan organisasi, diperlukan kepengurusan yang solid, berkapasitas tinggi dan memiliki legitimasi yang kuat dalam menjalankan tugas-tugas organisasi;</div>
                <div style="margin-bottom: 3px;">2. Bahwa agar kepengurusan Pimpinan Ranting Ikatan Pelajar Nahdlatul Ulama {status_desa_lower} {nama_desa_lower} yang telah tersusun memiliki legitimasi yang kuat, maka perlu disahkan dengan Surat Pengesahan Pimpinan Cabang {nama_organisasi_lower} Gresik;</div>
                <div style="margin-bottom: 3px;">3. Bahwa untuk mendapatkan Surat Pengesahan Pimpinan Cabang {nama_organisasi_lower} Gresik, perlu diterbitkan Surat Rekomendasi Pengesahan.</div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; font-weight: bold; border: none;">Mengingat</td>
            <td style="vertical-align: top; text-align: center; border: none;">:</td>
            <td style="vertical-align: top; text-align: justify; border: none;">
                <div style="margin-bottom: 3px;">1. Peraturan Dasar IPNU Bab VII Pasal 12;</div>
                <div style="margin-bottom: 3px;">2. Peraturan Rumah Tangga IPNU Bab V Pasal 13;</div>
                <div style="margin-bottom: 3px;">3. Peraturan Organisasi IPNU Bab X Pasal 57.</div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; font-weight: bold; border: none;">Memperhatikan</td>
            <td style="vertical-align: top; text-align: center; border: none;">:</td>
            <td style="vertical-align: top; text-align: justify; border: none;">
                <div style="margin-bottom: 3px;">1. Surat Pimpinan Ranting Ikatan Pelajar Nahdlatul Ulama\' {status_desa_lower} {nama_desa_lower} Nomor: {surat_ranting_nomor} Tanggal {surat_ranting_tanggal_formatted} Perihal Permohonan Rekomendasi;</div>
                <div style="margin-bottom: 3px;">2. Surat Rekomendasi PRNU {status_desa_lower} {nama_desa_lower} Nomor: {surat_prnu_nomor}.</div>
            </td>
        </tr>
    </tbody>
</table>

<div style="text-align: center; font-weight: bold; margin-top: 15px; margin-bottom: 10px;">MEMUTUSKAN</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: none;">
    <tbody>
        <tr>
            <td style="width: 110px; vertical-align: top; font-weight: bold; border: none;">Menetapkan</td>
            <td style="width: 15px; vertical-align: top; text-align: center; border: none;">:</td>
            <td style="vertical-align: top; text-align: justify; border: none;">
            <div style="margin-bottom: 3px;">1. Memberikan rekomendasi pengesahan kepada Pimpinan Ranting Ikatan Pelajar Nahdlatul Ulama {status_desa_lower} {nama_desa_lower} masa bhakti {masa_bhakti};</div>
                <div style="margin-bottom: 3px;">2. Meneruskan rekomendasi ini kepada Pimpinan Cabang {nama_organisasi_lower} Gresik untuk diterbitkan Surat Pengesahan;</div>
                <div style="margin-bottom: 3px;">3. Surat Rekomendasi Pengesahan ini berlaku sebagai surat pengesahan sementara sampai terbitnya Surat Pengesahan dari Pimpinan Cabang {nama_organisasi_lower} Gresik;</div>
                <div style="margin-bottom: 3px;">4. Surat Rekomendasi Pengesahan ini berlaku sejak tanggal ditetapkan dan akan ditinjau kembali apabila terdapat kekurangan.</div>
            </td>
        </tr>
    </tbody>
</table>
<div style="text-align: left; font-style: italic; margin-top: 5px; margin-bottom: 15px;">Wallahulmuwaffiq ila aqwamiththariq</div>

<table style="width: auto; margin-left: auto; border-collapse: collapse; margin-bottom: 25px; border: none;">
    <tbody>
        <tr>
            <td style="border: none; padding: 2px 5px 2px 0;">Ditetapkan di</td>
            <td style="border: none; padding: 2px 5px;">:</td>
            <td style="border: none; padding: 2px 0;">{nama_wilayah}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 5px 2px 0; vertical-align: top;">Pada tanggal</td>
            <td style="border: none; padding: 2px 5px; vertical-align: top;">:</td>
            <td style="border: none; padding: 2px 0;">
                <span style="border-bottom: 1px solid #000; display: inline-block; padding-bottom: 1px;">{tanggal_hijriah}</span><br>
                <span>{tanggal_masehi_formatted} M</span>
            </td>
        </tr>
    </tbody>
</table>

<div class="ttd-container" style="page-break-inside: avoid; margin-top: 15px;">
    
    <div style="text-align: center; font-weight: bold; margin-bottom: 15px; line-height: 1.3;">
        {tingkat_organisasi_upper}<br>
        {nama_organisasi_lengkap_baris2}<br>
        {nama_wilayah_upper}
    </div>

    <table class="tanda-tangan" style="width: 100%; border-collapse: collapse; border: none;">
        <tbody>
            <tr>
                <td style="width: 40%; text-align: center; border: none; vertical-align: top;">
                    Ketua
                    <div style="min-height: 60px;">
                        [TTD_KETUA]
                    </div>
                    <strong style="text-decoration: underline;">{nama_ketua}</strong><br>
                    NIA. {nia_ketua}
                </td>
                
                <td style="width: 20%; text-align: center; border: none; vertical-align: middle;">
                    [STEMPEL]
                </td>

                <td style="width: 40%; text-align: center; border: none; vertical-align: top;">
                    Sekretaris
                    <div style="min-height: 60px;">
                        [TTD_SEKRETARIS]
                    </div>
                    <strong style="text-decoration: underline;">{nama_sekretaris}</strong><br>
                    NIA. {nia_sekretaris}
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style="page-break-inside: avoid; margin-top: 20px;">
    <strong>Tembusan Kepada:</strong><br>
    <table style="width: 100%; border: none; margin-top: 2px;">
        <tr>
            <td style="width: 20px; border: none; padding: 0;">1.</td>
            <td style="border: none; padding: 0;">Pimpinan Cabang {jenis_organisasi_upper} Gresik</td>
        </tr>
        <tr>
            <td style="border: none; padding: 0;">2.</td>
            <td style="border: none; padding: 0;">MWC NU Kecamatan {nama_wilayah}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 0;">3.</td>
            <td style="border: none; padding: 0;">PRNU  {status_desa_lower} {nama_desa_lower}</td>
        </tr>
    </table>
</div>',
                'urutan' => 5,
                'has_attachment' => true,
                'is_active' => true,
                'status' => 'aktif',
            ]
        );
    }
}
