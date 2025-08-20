<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index(): string
    {
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $model->where('username', $username)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Nom d\'utilisateur ou mot de passe incorrect.');
            return redirect()->to('/auth');
        }

        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Nom d\'utilisateur ou mot de passe incorrect.');
            return redirect()->to('/auth');
        }

        // Connexion réussie - créer la session
        $ses_data = [
            'id'        => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'isLoggedIn' => TRUE
        ];
        $session->set($ses_data);

        // Rediriger selon le rôle
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/users');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth');
    }
}
