<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Signature;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int) $this->input('page', 1));
        $search = $this->input('q', '');
        $status = $this->input('status', '');
        $jenis = $this->input('jenis', '');

        $result = Signature::paginate($page, 10, $search, $status, $jenis);
        $stats = Signature::stats();

        $this->view('dashboard/index', [
            'result' => $result,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
            'jenis' => $jenis,
        ]);
    }

    public function revoke(int $id): void
    {
        $this->requireAuth();

        $signature = Signature::findById($id);

        if (!$signature) {
            $this->redirect('/dashboard');
            return;
        }

        if ($signature['status'] === 'aktif') {
            $keterangan = trim((string) $this->input('keterangan', ''));
            Signature::revoke($id, $keterangan);
        }

        $this->redirect('/dashboard');
    }

    /**
     * Hapus permanen data TTD (mis. salah input saat pembuatan).
     * Berbeda dari revoke(): ini benar-benar menghapus baris dari database,
     * bukan sekadar mengubah status jadi "dibatalkan".
     */
    public function delete(int $id): void
    {
        $this->requireAuth();

        $signature = Signature::findById($id);

        if (!$signature) {
            $this->redirect('/dashboard');
            return;
        }

        Signature::delete($id);

        if (function_exists('flash_set')) {
            flash_set('success', 'TTD "' . ($signature['kode_unik'] ?? '') . '" berhasil dihapus permanen.');
        }

        $this->redirect('/dashboard');
    }
}