<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    use ResponseTrait;

    public function register()
    {
        $userModel = new UserModel();
        
        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => $userModel->hashPassword($this->request->getVar('password'))
        ];

        if (!$userModel->save($data)) {
            return $this->fail($userModel->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => ['id' => $userModel->getInsertID()]
        ]);
    }

    public function login()
    {
        try {
            $userModel = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        if (!$userModel->verifyPassword($password, $user['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        // Generate JWT
        $key = config('JWT')->secretKey;
        $payload = [
            'iat' => time(),
            'exp' => time() + config('JWT')->expiration,
            'uid' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        $token = JWT::encode($payload, $key, config('JWT')->algorithm);

        // Set session (for session-based auth)
        session()->set([
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'logged_in' => true
        ]);

        return $this->respond([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]);
            
        }catch (\Exception $e) {
            dd($e);
            return $this->fail($e->getMessage());
        }
        
    }

    public function logout()
    {
        session()->destroy();
        return $this->respond(['status' => 'success', 'message' => 'Logged out']);
    }
}