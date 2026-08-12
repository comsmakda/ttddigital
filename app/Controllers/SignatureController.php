<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Signature;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class SignatureController extends Controller
{
    public function showCreate(): void
    {
        $this->requireAuth();
        $this->view('signature/create', [
            'jenis' => $this->input('jenis', 'surat'),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/ttd/buat');
        }

        $jenis = $this->input('jenis', 'surat');
        $namaPenandatangan = $this->input('nama_penandatangan', '');
        $jabatan = $this->input('jabatan_penandatangan', '');
        $tanggal = $this->input('tanggal_ttd', date('Y-m-d'));

        $errors = [];
        if (!in_array($jenis, ['surat', 'sertifikat'], true)) {
            $errors[] = 'Jenis dokumen tidak valid.';
        }
        if ($namaPenandatangan === '') {
            $errors[] = 'Nama penandatangan wajib diisi.';
        }
        if ($jabatan === '') {
            $errors[] = 'Jabatan penandatangan wajib diisi.';
        }
        if ($tanggal === '') {
            $errors[] = 'Tanggal TTD wajib diisi.';
        }

        $nomorSurat = null;
        $perihal = null;
        $namaSertifikat = null;

        if ($jenis === 'surat') {
            $nomorSurat = $this->input('nomor_surat', '');
            $perihal = $this->input('perihal', '');
            if ($nomorSurat === '') {
                $errors[] = 'Nomor surat wajib diisi.';
            }
            if ($perihal === '') {
                $errors[] = 'Perihal wajib diisi.';
            }
        } else {
            $namaSertifikat = $this->input('nama_sertifikat', '');
            if ($namaSertifikat === '') {
                $errors[] = 'Nama sertifikat wajib diisi.';
            }
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $_SESSION['_old'] = $_POST;
            $this->redirect('/ttd/buat?jenis=' . urlencode($jenis));
        }
        unset($_SESSION['_old']);

        $kode = Signature::generateUniqueKode();
        $admin = Auth::user();

        $id = Signature::create([
            'kode_unik' => $kode,
            'jenis' => $jenis,
            'nomor_surat' => $nomorSurat,
            'perihal' => $perihal,
            'nama_sertifikat' => $namaSertifikat,
            'nama_penandatangan' => $namaPenandatangan,
            'jabatan_penandatangan' => $jabatan,
            'tanggal_ttd' => $tanggal,
            'created_by' => $admin['id'] ?? null,
        ]);

        $qrRelativePath = $this->generateQrCode($kode);
        Signature::updateQrPath($id, $qrRelativePath);

        $this->flash('success', 'TTD berhasil dibuat dengan kode ' . $kode . '.');
        $this->redirect('/ttd/' . $id);
    }

    public function show(string $id): void
    {
        $this->requireAuth();

        $signature = Signature::findById((int) $id);
        if (!$signature) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $this->view('signature/show', ['signature' => $signature]);
    }

    public function revoke(string $id): void
    {
        $this->requireAuth();

        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/dashboard');
        }

        $keterangan = $this->input('keterangan', '');
        Signature::revoke((int) $id, $keterangan);

        $this->flash('success', 'TTD berhasil dibatalkan.');
        $this->redirect('/ttd/' . $id);
    }

    public function reactivate(string $id): void
    {
        $this->requireAuth();

        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/dashboard');
        }

        Signature::reactivate((int) $id);

        $this->flash('success', 'TTD berhasil diaktifkan kembali.');
        $this->redirect('/ttd/' . $id);
    }

    /**
     * Hapus permanen data TTD (mis. salah input saat pembuatan).
     * Beda dari revoke(): baris di database benar-benar dihapus,
     * bukan sekadar diubah statusnya jadi "dibatalkan".
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/dashboard');
        }

        $signature = Signature::findById((int) $id);
        if (!$signature) {
            $this->redirect('/dashboard');
        }

        // Hapus juga file QR code fisiknya supaya tidak jadi sampah di storage
        if (!empty($signature['kode_unik'])) {
            $qrPath = dirname(__DIR__, 2) . '/storage/qrcodes/' . $signature['kode_unik'] . '.png';
            if (file_exists($qrPath)) {
                @unlink($qrPath);
            }
        }

        Signature::delete((int) $id);

        $this->flash('success', 'TTD berhasil dihapus permanen.');
        $this->redirect('/dashboard');
    }

    /**
     * Stream file QR code dari storage (di luar webroot) supaya tetap
     * bisa diakses lewat <img src> tanpa membuat storage/ jadi public.
     */
    public function qrImage(string $kode): void
    {
        // Hanya izinkan pola kode yang valid untuk mencegah path traversal
        if (!preg_match('/^TTD-[A-F0-9]{8}$/', $kode)) {
            http_response_code(404);
            exit;
        }

        $path = dirname(__DIR__, 2) . '/storage/qrcodes/' . $kode . '.png';
        if (!file_exists($path)) {
            http_response_code(404);
            exit;
        }

        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($path);
        exit;
    }

    /**
     * Generate QR code (PNG) berisi URL verifikasi, dengan logo COM di tengah.
     * Error correction level High dipakai supaya QR tetap ke-scan walau tertutup logo.
     *
     * CATATAN PENTING soal logo transparan:
     * Endroid\QrCode\Logo dengan punchoutBackground:true hanya benar-benar
     * memotong lubang transparan kalau backend-nya Imagick. Kalau server cuma
     * punya GD (umum di banyak hosting/Docker image PHP standar), fitur itu
     * jatuh ke fallback yang malah menaruh kotak/lingkaran background solid
     * di belakang logo — makanya logo yang aslinya transparan jadi kelihatan
     * ada bg-nya.
     *
     * Di sini logo di-composite manual pakai GD (imagecopyresampled +
     * alpha channel dijaga eksplisit), jadi transparansi asli file PNG logo
     * benar-benar dipertahankan apa adanya, tanpa kotak/warna tambahan apa pun.
     * Mengembalikan path relatif (untuk disimpan di DB & dipakai <img src>).
     */
    private function generateQrCode(string $kode): string
    {
        $verifyUrl = url('/verify/' . $kode);
        $logoPath = dirname(__DIR__, 2) . '/public/assets/images/logo-com.png';

        $qrCode = new QrCode(
            data: $verifyUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 600,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(15, 23, 42),
            backgroundColor: new Color(255, 255, 255),
        );

        // Generate QR polos dulu (tanpa logo bawaan Endroid) sebagai base image
        $result = (new PngWriter())->write($qrCode);

        $filename = $kode . '.png';
        $storageDir = dirname(__DIR__, 2) . '/storage/qrcodes';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0775, true);
        }
        $outputPath = $storageDir . '/' . $filename;

        if (!file_exists($logoPath)) {
            // Tidak ada logo — simpan QR polos saja
            $result->saveToFile($outputPath);
            return $filename;
        }

        $this->compositeLogo($result->getString(), $logoPath, $outputPath);

        return $filename;
    }

    /**
     * Tempelkan logo PNG transparan ke tengah QR code memakai GD,
     * dengan alpha channel dijaga manual di setiap tahap supaya tidak
     * ada background yang ikut ditambahkan oleh proses resize/copy.
     *
     * FIX: sebelum ini, kalau file logo tersimpan sebagai PNG paletted
     * (PNG-8 dengan 1 warna index transparan, bukan PNG-32 truecolor+alpha
     * penuh — banyak tool ekspor logo/ikon default ke format ini), GD gagal
     * memetakan transparansi index itu ke kanvas truecolor-alpha saat
     * di-resample, dan area transparan itu malah jadi hitam solid.
     * imagepalettetotruecolor() dipanggil dulu di sini supaya logo apa pun
     * (paletted atau truecolor) diproses dengan alpha channel penuh 0–255.
     */
    private function compositeLogo(string $qrPngData, string $logoPath, string $outputPath): void
    {
        $qrImage = imagecreatefromstring($qrPngData);
        $logoSource = imagecreatefrompng($logoPath);

        if ($qrImage === false || $logoSource === false) {
            // Gagal load salah satu gambar — simpan QR polos saja daripada gagal total
            file_put_contents($outputPath, $qrPngData);
            return;
        }

        // Paksa truecolor dulu SEBELUM diapa-apain — ini kunci fix-nya.
        // Kalau logo (atau QR base) ternyata paletted PNG, transparansinya
        // cuma berupa 1 index warna, bukan skala alpha penuh, dan itu yang
        // bikin GD salah render jadi kotak hitam solid saat di-resample.
        if (!imageistruecolor($logoSource)) {
            imagepalettetotruecolor($logoSource);
        }
        if (!imageistruecolor($qrImage)) {
            imagepalettetotruecolor($qrImage);
        }

        // Jaga alpha channel logo sumber apa adanya (jangan di-blend ke putih)
        imagealphablending($logoSource, false);
        imagesavealpha($logoSource, true);

        $qrSize = imagesx($qrImage);
        $targetLogoSize = (int) round($qrSize * 0.22); // ~22% lebar QR, aman untuk error correction High

        $srcW = imagesx($logoSource);
        $srcH = imagesy($logoSource);
        $ratio = min($targetLogoSize / $srcW, $targetLogoSize / $srcH);
        $dstW = max(1, (int) round($srcW * $ratio));
        $dstH = max(1, (int) round($srcH * $ratio));

        // Kanvas resize logo dengan alpha transparan penuh sebagai latar,
        // supaya sudut/tepi logo yang transparan tetap transparan setelah resize
        $resizedLogo = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);
        $transparent = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
        imagefill($resizedLogo, 0, 0, $transparent);
        imagealphablending($resizedLogo, false);
        imagecopyresampled($resizedLogo, $logoSource, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

        // Tempel ke tengah QR dengan alpha blending aktif di kanvas tujuan,
        // supaya bagian transparan logo menampilkan pola QR di baliknya
        // (bukan kotak putih/berwarna)
        imagealphablending($qrImage, true);
        imagesavealpha($qrImage, true);
        $destX = (int) round(($qrSize - $dstW) / 2);
        $destY = (int) round(($qrSize - $dstH) / 2);
        imagecopy($qrImage, $resizedLogo, $destX, $destY, 0, 0, $dstW, $dstH);

        imagepng($qrImage, $outputPath);

        imagedestroy($qrImage);
        imagedestroy($logoSource);
        imagedestroy($resizedLogo);
    }
}