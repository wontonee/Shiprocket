<?php

namespace Wontonee\Shiprocket\Sdk\Resources;
use Illuminate\Support\Facades\Http;
use Wontonee\Shiprocket\Sdk\Config\Config;

class Channel
{
    protected string $token;
    protected string $apiUrl = Config::API_URL;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Fetch all channels
     * @return array all the channel data
     */
    public function fetchChannels(): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'channels');
            
            if ($response->successful()) {
                return $response->json();
            } else {
                return $response->json() ?: ['error' => $response->status(), 'message' => $response->body()];
            }
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

     /**
     * validate token and other parameters
     * @param string $validateType type of validation
     * @param mixed $value value to validate
     * @throws \Exception if any error arrive
     */
    public function validate(string $validateType, $value): void
    {
        switch ($validateType) {
            case 'Token':
                if (!$this->token) {
                    throw new \Exception("Authentication token is missing. Please authenticate first.");
                }
                break;
          
        }
    }
}