<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Signature;

class VerifyController extends Controller
{
    public function show(string $kode): void
    {
        $signature = Signature::findByKode($kode);

        $this->view('verify/show', [
            'signature' => $signature,
            'kode' => $kode,
        ], 'layouts/public');
    }

    public function index(): void
    {
        $this->view('verify/index', [], 'layouts/public');
    }
}
