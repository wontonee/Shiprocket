<?php

namespace Wontonee\Shiprocket\Sdk\Resources;
use Illuminate\Support\Facades\Http;
use Wontonee\Shiprocket\Sdk\Config\Config;


class Authenticate
{
    protected string $email;
    protected string $password;
    protected string $token;
    protected string $apiUrl = Config::API_URL;
   
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getToken()
    {
        if (empty($this->token)) {
            $response = Http::post($this->apiUrl . 'auth/login', [
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $this->token = $response['token'];
            } else {
                throw new \Exception('Authentication failed: ' . $response['message']);
            }
        }

        return $this->token;
    }
}