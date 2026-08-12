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
}
