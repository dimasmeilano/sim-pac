<?php

namespace App\Services;

use App\Models\Organization;
use biladina\hijridatetime\HijriDateTime;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;
use IntlDateFormatter;

class SuratService
{
    /**
     * Render Isi Surat Keseluruhan
     * (Menangani penggantian Variabel Form, Variabel Sistem, dan Tanda Tangan)
     */
    public function renderIsiSurat($nomorSurat, $org, $isiSuratMentah, $dataForm = [], $tanggalSurat = null, $statusValidasi = 'draft')
    {
        // ====================================================================
        // [FAILSAFE] Pastikan $dataForm selalu berbentuk Array!
        // Jika yang masuk adalah string (misal JSON dari database), ubah ke array.
        // Jika bukan keduanya, paksa menjadi array kosong agar aplikasi tidak crash.
        // ====================================================================
        if (is_string($dataForm)) {
            $dataForm = json_decode($dataForm, true) ?? [];
        } elseif (!is_array($dataForm) && !is_object($dataForm)) {
            $dataForm = [];
        }

        // ====================================================================
        // 1. REPLACE VARIABEL DARI FORM DINAMIS (Termasuk Huruf Besar/Kecilnya)
        // ====================================================================
        foreach ($dataForm as $key => $value) {
            $nilai = $value ?? '';
            $isiSuratMentah = str_replace('{' . $key . '}', nl2br(e($nilai)), $isiSuratMentah);
            $isiSuratMentah = str_replace('{' . $key . '_lower}', strtolower($nilai), $isiSuratMentah);
            $isiSuratMentah = str_replace('{' . $key . '_upper}', strtoupper($nilai), $isiSuratMentah);
        }

        // ====================================================================
        // 2. REPLACE VARIABEL SISTEM (Otomatis dari Database & Waktu)
        // ====================================================================
        $rawNamaWilayah = strtoupper($org->name ?? '');
        $namaWilayahBersih = trim(preg_replace('/\b(PAC\.|PAC|PR\.|PR|PC\.|PC|PW\.|PW|IPNU|IPPNU|PIMPINAN|ANAK|CABANG|RANTING)\b/i', '', $rawNamaWilayah));

        $orgType = strtolower($org->type ?? 'pac');
        $tingkatUpper = '';
        $sebutanWilayah = '';

        if ($orgType === 'pac') {
            $tingkatUpper = 'PIMPINAN ANAK CABANG';
            $sebutanWilayah = 'KECAMATAN';
        } elseif ($orgType === 'ranting') {
            $tingkatUpper = 'PIMPINAN RANTING';
            $sebutanWilayah = 'DESA';
        } elseif ($orgType === 'komisariat') {
            $tingkatUpper = 'PIMPINAN KOMISARIAT';
            $sebutanWilayah = 'SEKOLAH/KAMPUS';
        } else {
            $tingkatUpper = strtoupper($orgType);
            $sebutanWilayah = 'KABUPATEN';
        }

        $isiSuratMentah = str_replace('{sebutan_wilayah_upper}', $sebutanWilayah, $isiSuratMentah);

        if (in_array(strtolower($tanggalSurat ?? ''), ['draft', 'selesai', 'pengajuan'])) {
            $statusValidasi = $tanggalSurat;
            $tanggalSurat = null;
        }

        $tglSurat = $tanggalSurat ?? now();

        // [FAILSAFE 2] Try-Catch untuk mencegah sistem hancur jika format tanggal tidak wajar
        try {
            $tanggalMasehi = \Carbon\Carbon::parse($tglSurat)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $tglSurat = now(); // Jika error/tanggal aneh, paksa gunakan hari ini
            $tanggalMasehi = \Carbon\Carbon::parse($tglSurat)->translatedFormat('d F Y');
        }

        $tanggalHijriah = $this->getTanggalHijriahOtomatis($tglSurat);

        $isiSuratMentah = str_replace('{nomor_surat}', $nomorSurat, $isiSuratMentah);
        $isiSuratMentah = str_replace('{nama_wilayah}', ucwords(strtolower($namaWilayahBersih)), $isiSuratMentah);
        $isiSuratMentah = str_replace('{nama_wilayah_upper}', $namaWilayahBersih, $isiSuratMentah);
        $isiSuratMentah = str_replace('{tingkat_organisasi_upper}', $tingkatUpper, $isiSuratMentah);
        $isiSuratMentah = str_replace('{tanggal_masehi_formatted}', $tanggalMasehi, $isiSuratMentah);
        $isiSuratMentah = str_replace('{tanggal_hijriah}', $tanggalHijriah, $isiSuratMentah);
        $isiSuratMentah = str_ireplace('{tanggal_surat}', $tanggalMasehi, $isiSuratMentah); // <-- TAMBAHAN BARU

        $isiSuratMentah = str_replace('{nia_ketua}', $org->ketua->nia ?? '..................', $isiSuratMentah);
        $isiSuratMentah = str_replace('{nia_sekretaris}', $org->sekretaris->nia ?? '..................', $isiSuratMentah);

        // ====================================================================
        // 3. RENDER TANDA TANGAN & STEMPEL
        // ====================================================================
        $jenisOrg = strtolower($org->jenis_organisasi ?? '');

        // 3A. Siapkan wadah gambar (Default Kosong jika masih Draft)
        $imgKetua = '';
        $imgSekretaris = '';
        $imgStempel = '';
        $imgKetuaPartner = '';
        $imgSekretarisPartner = '';
        $imgStempelPartner = '';

        // Cari Partner (Harus dicari di luar status 'selesai' agar namanya bisa diambil)
        $partnerOrg = \App\Models\Organization::where('name', $org->name)
            ->where('type', $org->type)
            ->where('jenis_organisasi', $jenisOrg === 'ipnu' ? 'ippnu' : 'ipnu')
            ->first();

        // Ambil Nama Teks (NAMA HARUS SELALU MUNCUL MESKI DRAFT)
        $namaKetua = strtoupper($org->ketua->name ?? '......................');
        $namaSekretaris = strtoupper($org->sekretaris->name ?? '......................'); // <-- TAMBAHAN BARU

        $namaKetuaPartner = strtoupper($partnerOrg->ketua->name ?? '......................');
        $namaSekretarisPartner = strtoupper($partnerOrg->sekretaris->name ?? '......................'); // <-- TAMBAHAN BARU

        // LAKUKAN REPLACE TEKS NAMA KETUA & SEKRETARIS (Mandiri) <-- TAMBAHAN BARU
        $isiSuratMentah = str_replace('{nama_ketua}', $namaKetua, $isiSuratMentah);
        $isiSuratMentah = str_replace('{nama_sekretaris}', $namaSekretaris, $isiSuratMentah);

        // 3B. JIKA STATUS SELESAI, BARU TARIK GAMBARNYA!
        if ($statusValidasi === 'selesai') {
            $imgKetua = $this->getTtdImage($org->ttd_ketua ?? null);
            $imgSekretaris = $this->getTtdImage($org->ttd_sekretaris ?? null);
            $imgStempel = $this->getStampImage($org->stempel ?? null);

            $imgKetuaPartner = $partnerOrg ? $this->getTtdImage($partnerOrg->ttd_ketua ?? null) : '';
            $imgSekretarisPartner = $partnerOrg ? $this->getTtdImage($partnerOrg->ttd_sekretaris ?? null) : '';
            $imgStempelPartner = $partnerOrg ? $this->getStampImage($partnerOrg->stempel ?? null) : '';
        }

        // 3C. Replace untuk Surat Mandiri (Pakai gambar kosong jika draf, gambar asli jika selesai)
        $isiSuratMentah = str_replace('[TTD_KETUA]', $imgKetua, $isiSuratMentah);
        $isiSuratMentah = str_replace('[TTD_SEKRETARIS]', $imgSekretaris, $isiSuratMentah);
        $isiSuratMentah = str_replace('[STEMPEL]', $imgStempel, $isiSuratMentah);

        // 3D. Replace untuk Surat Bersama/Panitia
        if ($jenisOrg === 'ipnu') {
            $isiSuratMentah = str_replace('[TTD_KETUA_IPNU]', $imgKetua, $isiSuratMentah);
            $isiSuratMentah = str_replace('[TTD_SEKRETARIS_IPNU]', $imgSekretaris, $isiSuratMentah);
            $isiSuratMentah = str_replace('[STEMPEL_IPNU]', $imgStempel, $isiSuratMentah);
            $isiSuratMentah = str_replace('{nama_ketua_ipnu}', $namaKetua, $isiSuratMentah);

            $isiSuratMentah = str_replace('[TTD_KETUA_IPPNU]', $imgKetuaPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('[TTD_SEKRETARIS_IPPNU]', $imgSekretarisPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('[STEMPEL_IPPNU]', $imgStempelPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('{nama_ketua_ippnu}', $namaKetuaPartner, $isiSuratMentah);
        } elseif ($jenisOrg === 'ippnu') {
            $isiSuratMentah = str_replace('[TTD_KETUA_IPPNU]', $imgKetua, $isiSuratMentah);
            $isiSuratMentah = str_replace('[TTD_SEKRETARIS_IPPNU]', $imgSekretaris, $isiSuratMentah);
            $isiSuratMentah = str_replace('[STEMPEL_IPPNU]', $imgStempel, $isiSuratMentah);
            $isiSuratMentah = str_replace('{nama_ketua_ippnu}', $namaKetua, $isiSuratMentah);

            $isiSuratMentah = str_replace('[TTD_KETUA_IPNU]', $imgKetuaPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('[TTD_SEKRETARIS_IPNU]', $imgSekretarisPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('[STEMPEL_IPNU]', $imgStempelPartner, $isiSuratMentah);
            $isiSuratMentah = str_replace('{nama_ketua_ipnu}', $namaKetuaPartner, $isiSuratMentah);
        }

        // ====================================================================
        // 3E. RENDER QR CODE (HANYA JIKA SELESAI)
        // ====================================================================
        $qrCodeHtml = '';
        if ($statusValidasi === 'selesai') {
            $linkValidasi = route('verifikasi.surat', ['nomor' => base64_encode($nomorSurat)]);

            try {
                // Menggunakan Library QR Code Bawaan Laravel (100% Offline & Anti Gagal)
                $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(80)->margin(0)->generate($linkValidasi);
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);
                $qrCodeHtml = '<img src="' . $qrBase64 . '" style="width: 75px; height: 75px;" />';
            } catch (\Exception $e) {
                $qrCodeHtml = '';
            }
        }

        // Timpa teks [QR_TTE] dengan gambar QR
        $isiSuratMentah = str_replace('[QR_TTE]', $qrCodeHtml, $isiSuratMentah);

        // ====================================================================
        // 4. SAPU BERSIH SISA PLACEHOLDER
        // ====================================================================
        $placeholderSisa = [
            '[TTD_KETUA_IPNU]',
            '[TTD_KETUA_IPPNU]',
            '[TTD_SEKRETARIS_IPNU]',
            '[TTD_SEKRETARIS_IPPNU]',
            '[TTD_KETUA]',
            '[TTD_SEKRETARIS]',
            '[STEMPEL]',
            '[STEMPEL_IPNU]',
            '[STEMPEL_IPPNU]'
        ];

        // QR Code hanya muncul saat status selesai
        if ($statusValidasi !== 'selesai') {
            $placeholderSisa[] = '[QR_TTE]';
        }

        $isiSuratFinal = str_replace($placeholderSisa, '', $isiSuratMentah);

        return $isiSuratFinal;
    }


    /**
     * Konversi Gambar TTD menjadi Base64 agar aman dirender oleh PDF
     */
    private function getTtdImage($pathTtd)
    {
        if (!empty($pathTtd) && Storage::disk('public')->exists($pathTtd)) {
            $pathFile = storage_path('app/public/' . $pathTtd);
            $type = pathinfo($pathFile, PATHINFO_EXTENSION);
            $data = file_get_contents($pathFile);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            // Render gambar transparan tanpa border
            return '<img src="' . $base64 . '" style="max-height: 70px; width: auto; background: transparent;">';
        }
        return '';
    }

    /**
     * Render Khusus untuk Template Surat Umum (WYSIWYG)
     * Bertugas mengganti {isi_surat} dan merakit [BLOK_TANDA_TANGAN]
     */
    public function renderTemplateUmum($templateKonten, $request, $org, $statusValidasi = 'draft')
    {
        // 1. Ganti tag [ISI_SURAT_BEBAS] milik Anda dengan ketikan dari TinyMCE
        $html = str_replace('[ISI_SURAT_BEBAS]', $request->isi_surat_bebas, $templateKonten);
        // (Cadangan jika ada template lama yang pakai {isi_surat})
        $html = str_replace('{isi_surat}', $request->isi_surat_bebas, $html);

        // 2. Siapkan Blok Tanda Tangan Mentah
        $penerbit = $request->penerbit_surat ?? 'mandiri';
        $blokTandaTangan = '';

        if ($penerbit === 'bersama') {
            $blokTandaTangan = '
            <div style="page-break-inside: avoid; margin-top: 30px;">
                <div style="text-align: center; font-weight: bold; line-height: 1.3;">
                    {tingkat_organisasi_upper}<br>
                    IKATAN PELAJAR NAHDLATUL ULAMA<br>
                    IKATAN PELAJAR PUTRI NAHDLATUL ULAMA<br>
                    {sebutan_wilayah_upper} {nama_wilayah_upper}
                </div>
                <table style="width: 100%; border-collapse: collapse; border: none; text-align: center; margin-top: 15px;">
                    <tr>
                        <td style="width: 50%; vertical-align: bottom; border: none; position: relative;">
                            [STEMPEL_IPNU]
                            <div style="min-height: 80px; padding-top: 10px;">[TTD_KETUA_IPNU]</div>
                            <strong style="text-decoration: underline;">{nama_ketua_ipnu}</strong><br>
                            KETUA IPNU
                        </td>
                        <td style="width: 50%; vertical-align: bottom; border: none; position: relative;">
                            [STEMPEL_IPPNU]
                            <div style="min-height: 80px; padding-top: 10px;">[TTD_KETUA_IPPNU]</div>
                            <strong style="text-decoration: underline;">{nama_ketua_ippnu}</strong><br>
                            KETUA IPPNU
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 10px; margin-bottom: 15px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 80px; vertical-align: top; border: none;">
                [QR_TTE]
            </td>
            <td style="vertical-align: top; border: none; padding-left: 15px; font-size: 10px; color: #333; line-height: 1.4;">
                <i><b>Validasi Keaslian Dokumen:</b><br>
                Surat ini telah ditandatangani secara elektronik oleh Ketua {tingkat_organisasi_upper} IPNU-IPPNU {nama_wilayah_upper}.<br>
                Scan QR Code di samping untuk memastikan keaslian surat melalui sistem.</i>
            </td>
        </tr>
    </table>
</div>
            </div>';
        } elseif ($penerbit === 'panitia') {
            $kegiatan = $request->nama_kegiatan_panitia ?? $request->perihal ?? '[NAMA KEGIATAN]';
            $namaKegiatan = strtoupper($kegiatan);

            $ketuaPanitia = $request->nama_ketua_panitia ? strtoupper($request->nama_ketua_panitia) : '......................';
            $sekretarisPanitia = $request->nama_sekretaris_panitia ? strtoupper($request->nama_sekretaris_panitia) : '......................';

            $blokTandaTangan = '
            <div style="text-align: center; margin-top: 30px; font-weight: bold; line-height: 1.3;">
                PANITIA PELAKSANA ' . $namaKegiatan . '<br>
                {tingkat_organisasi_upper} IPNU IPPNU {nama_wilayah_upper}
            </div>
            <table style="width: 100%; border-collapse: collapse; border: none; text-align: center; margin-top: 25px;">
                <tr>
                    <td style="width: 50%; vertical-align: bottom; border: none;">
                        <div style="min-height: 60px;"></div>
                        <strong style="text-decoration: underline;">' . $ketuaPanitia . '</strong><br><i>Ketua</i>
                    </td>
                    <td style="width: 50%; vertical-align: bottom; border: none;">
                        <div style="min-height: 60px;"></div>
                        <strong style="text-decoration: underline;">' . $sekretarisPanitia . '</strong><br><i>Sekretaris</i>
                    </td>
                </tr>
            </table>

            <div style="page-break-inside: avoid; margin-top: 30px;">
                <div style="text-align: center; line-height: 1.3;">
                    Mengetahui,<br><strong>{tingkat_organisasi_upper}<br>IKATAN PELAJAR NAHDLATUL ULAMA<br>IKATAN PELAJAR PUTRI NAHDLATUL ULAMA<br>{sebutan_wilayah_upper} {nama_wilayah_upper}</strong>
                </div>
                <table style="width: 100%; border-collapse: collapse; border: none; text-align: center; margin-top: 15px;">
                    <tr>
                        <td style="width: 50%; vertical-align: bottom; border: none; position: relative;">
                            [STEMPEL_IPNU]
                            <div style="min-height: 80px; padding-top: 10px;">[TTD_KETUA_IPNU]</div>
                            <strong style="text-decoration: underline;">{nama_ketua_ipnu}</strong><br><i>Ketua IPNU</i>
                        </td>
                        <td style="width: 50%; vertical-align: bottom; border: none; position: relative;">
                            [STEMPEL_IPPNU]
                            <div style="min-height: 80px; padding-top: 10px;">[TTD_KETUA_IPPNU]</div>
                            <strong style="text-decoration: underline;">{nama_ketua_ippnu}</strong><br><i>Ketua IPPNU</i>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="margin-top: 10px; margin-bottom: 15px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 80px; vertical-align: top; border: none;">
                [QR_TTE]
            </td>
            <td style="vertical-align: top; border: none; padding-left: 15px; font-size: 10px; color: #333; line-height: 1.4;">
                <i><b>Validasi Keaslian Dokumen:</b><br>
                Surat ini telah ditandatangani secara elektronik oleh Ketua {tingkat_organisasi_upper} IPNU-IPPNU {nama_wilayah_upper}.<br>
                Scan QR Code di samping untuk memastikan keaslian surat melalui sistem.</i>
            </td>
        </tr>
    </table>
</div>';
        } else {
            // MANDIRI
            $blokTandaTangan = '
            <div style="page-break-inside: avoid; margin-top: 30px;">
                <table style="width: 100%; border-collapse: collapse; border: none; text-align: center;">
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
                </table>
               <div style="margin-top: 10px; margin-bottom: 15px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 80px; vertical-align: top; border: none;">
                [QR_TTE]
            </td>
            <td style="vertical-align: top; border: none; padding-left: 15px; font-size: 10px; color: #333; line-height: 1.4;">
                <i><b>Validasi Keaslian Dokumen:</b><br>
                Surat ini telah ditandatangani secara elektronik oleh Ketua {tingkat_organisasi_upper} IPNU-IPPNU {nama_wilayah_upper}.<br>
                Scan QR Code di samping untuk memastikan keaslian surat melalui sistem.</i>
            </td>
        </tr>
    </table>
</div>';
        }

        // 3. Masukkan Blok Tanda Tangan ke dalam Template HTML
        $html = str_replace('[BLOK_TANDA_TANGAN]', $blokTandaTangan, $html);

        // 4. KEAJAIBAN TERJADI DI SINI:
        // Lempar HTML yang sudah utuh ini ke mesin utama `renderIsiSurat` agar Stempel, 
        // Tanggal Hijriah, dan Huruf Kapitalnya diproses otomatis!
        $dataForm = [
            'perihal'      => strtoupper($request->perihal ?? ''),
            'tujuan_surat' => $request->tujuan_surat ?? '',
            'lampiran'     => $request->lampiran ?? '-',
        ];
        return $this->renderIsiSurat(
            $request->nomor_surat,
            $org,
            $html,
            $dataForm,
            $request->tanggal_surat,
            $statusValidasi
        );
    }

    /**
     * Konversi Tanggal Masehi ke Hijriah menggunakan Library/IntlFormatter
     */
    public function getTanggalHijriahOtomatis($tanggalMasehi = null)
    {
        $dateObj = $tanggalMasehi ? new DateTime($tanggalMasehi) : new DateTime('now');

        try {
            // Pastikan class HijriDateTime sudah diload atau ada di namespace yang tepat
            $hijri = new HijriDateTime($dateObj);
            $tanggalHijriah = $hijri->format('_j _F _Y');

            if (empty($tanggalHijriah)) {
                $tanggalHijriah = $hijri->date("_j _F _Y");
            }
            $tanggalHijriah = preg_replace('/\s+/', ' ', $tanggalHijriah);

            if (str_ends_with(strtoupper($tanggalHijriah), 'H')) return $tanggalHijriah;
            return $tanggalHijriah . ' H';
        } catch (\Throwable $e) {
            // Fallback menggunakan PHP bawaan
            $formatter = new IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                'Asia/Jakarta',
                IntlDateFormatter::TRADITIONAL
            );

            $hasilIntl = $formatter->format($dateObj);
            $hasilIntl = str_replace(' AH', '', $hasilIntl);

            if (!str_ends_with(strtoupper($hasilIntl), 'H')) {
                $hasilIntl = $hasilIntl . ' H';
            }
            return $hasilIntl;
        }
    }

    private function getStampImage($pathStempel)
    {
        if (!empty($pathStempel) && Storage::disk('public')->exists($pathStempel)) {
            $pathFile = storage_path('app/public/' . $pathStempel);
            $type = pathinfo($pathFile, PATHINFO_EXTENSION);
            $data = file_get_contents($pathFile);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            // Stempel biasanya agak transparan (opacity) agar teks di belakangnya tetap terbaca,
            // dan posisinya dibuat absolute agar bisa "menabrak" tanda tangan (overlap) layaknya stempel asli.
            return '<img src="' . $base64 . '" style="width: 100px; height: auto; position: absolute; left: 50%; transform: translateX(-50%); opacity: 0.8; z-index: -1;">';
        }
        return '';
    }
}
