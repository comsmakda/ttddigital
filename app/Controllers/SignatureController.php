<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Signature;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
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
     * Logo memakai PNG transparan (bukan kotak putih), jadi punchoutBackground
     * otomatis mengikuti bentuk shield asli — tanpa background/shadow tambahan.
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

        $writer = new PngWriter();

        $logo = null;
        if (file_exists($logoPath)) {
            $logo = new Logo(
                path: $logoPath,
                resizeToWidth: 120,
                punchoutBackground: true,
            );
        }

        $result = $writer->write($qrCode, $logo);

        $filename = $kode . '.png';
        $storageDir = dirname(__DIR__, 2) . '/storage/qrcodes';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0775, true);
        }

        $result->saveToFile($storageDir . '/' . $filename);

        return $filename;
    }
}