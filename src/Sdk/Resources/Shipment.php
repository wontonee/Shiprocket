<?php

namespace Wontonee\Shiprocket\Sdk\Resources;
use Illuminate\Support\Facades\Http;
use Wontonee\Shiprocket\Sdk\Config\Config;

class Shipment
{
    protected string $token;
    protected string $apiUrl = Config::API_URL;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * get all shipmentent details
     * @return array response data
     */
    public function fetchshipment(): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'shipments');
            
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
     * fetch shipment by id
     * @param int $shipmentId shipment id
     * @return array response data
     */
    public function fetchshipmentbyid(int $shipmentId): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'shipments/' . $shipmentId);
            
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
     * cancel shipment
     * @param array $awbs awbs id
     * @return array response data
     */
    public function cancel(array $awbs): array
    {
        $this->validate('Token', '');

        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/cancel/shipment/awbs', $awbs);
            
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
            case 'OrderId':
                if (empty($value)) {
                    throw new \Exception("Order ID is missing");
                }
                break;
        }
    }
}


