<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function register()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function attemptLogin()
    {
        $model = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'logged_in' => true
            ]);

            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid login');
    }

    public function attemptRegister()
    {
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];

        // Call the API register endpoint
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post(base_url('api/register'), [
                'json' => $data
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['status'] === 'success') {
                // Auto-login after registration
                return $this->attemptLogin();
            } else {
                return redirect()->back()->with('error', json_encode($result['messages'] ?? 'Registration failed'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}
