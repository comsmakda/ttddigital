<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login', [], 'layouts/auth');
    }

    public function login(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/login');
        }

        $username = $this->input('username', '');
        $password = $this->input('password', '');

        if ($username === '' || $password === '') {
            $this->flash('error', 'Username dan password wajib diisi.');
            $this->redirect('/login');
        }

        if (Auth::attempt($username, $password)) {
            $this->redirect('/dashboard');
        }

        $this->flash('error', 'Username atau password salah.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
