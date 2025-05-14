<?php

namespace Wontonee\Shiprocket\Sdk\Resources;
use Illuminate\Support\Facades\Http;
use Wontonee\Shiprocket\Sdk\Config\Config;


class Orders
{
    protected string $token;
    protected string $apiUrl = Config::API_URL;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Fetch all orders
     * @return array all the order data
     */
    public function fetchorder(): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'orders');
            
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
     * Fetch custom order
     * @param int $orderId order id
     * @return array selected order details or empty
     */
    public function fetchcustomorder(int $orderId): array
    {
        $this->validate('Token', '');
        $this->validate('OrderId', $orderId);
        
        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'orders/show/' . $orderId);
            
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
     * Create an adhoc order
     *
     * @param array $orderData The data for the adhoc order
     * @return array The response data
     */
    public function createadhocorder(array $orderData): array
    {
        $this->validate('Token', '');

        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/create/adhoc', $orderData);
            
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
     * create a new order
     * @param array $orderData order data
     * @return array response data
     */
    public function create(array $orderData): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/create', $orderData);
            
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
     * update a order details
     * @param array $orderData order data
     * @return array response data
     */
    public function update(array $orderData): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/address/update', $orderData);
            
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
     * update pick up details
     * @param array $orderData order data
     * @return array response data
     */
    public function updatepickup(array $orderData): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->patch($this->apiUrl . 'orders/address/pickup', $orderData);
            
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
     * update deliver information
     * @param array $orderData order data
     * @return array response data
     */
    public function updatedelivery(array $orderData): array
    {
        $this->validate('Token', '');
        
        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/address/update', $orderData);
            
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
     * Cancel order
     * @param array $orderId to cancel the order
     * @return array The response data
     */
    public function cancelorder(array $orderId): array
    {
        $this->validate('Token', '');
        $this->validate('OrderId', $orderId);

        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl . 'orders/cancel', [
                    'ids' => $orderId
                ]);
            
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