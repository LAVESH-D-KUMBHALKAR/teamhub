<?php

namespace App\Controllers;

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
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Call the API login endpoint
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post(base_url('api/login'), [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            if ($result['status'] === 'success') {
                // Set session
                session()->set([
                    'user_id' => $result['user']['id'],
                    'name' => $result['user']['name'],
                    'email' => $result['user']['email'],
                    'logged_in' => true
                ]);
                
                return redirect()->to('/dashboard');
            } else {
                return redirect()->back()->with('error', $result['message'] ?? 'Login failed');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Login failed: ' . $e->getMessage());
        }
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