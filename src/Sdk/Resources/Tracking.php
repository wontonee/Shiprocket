<?php

namespace Wontonee\Shiprocket\Sdk\Resources;

use Illuminate\Support\Facades\Http;
use Wontonee\Shiprocket\Sdk\Config\Config;


class Tracking
{

    protected string $token;
    protected string $apiUrl = Config::API_URL;

    public function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * Fetch tracking details by AWB number
     *
     * @param string $identifier
     * @return array
     */
    public function fetchByAwb(array $awbnumber): array
    {
        $this->validate('Token', '');

        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'courier/track/awb/' . $awbnumber);

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
     * Fetch tracking details by shipment ID
     *
     * @param int $shipmentId
     * @return array
     */
    public function fetchByShipmentId(int $shipmentId): array
    {
        $this->validate('Token', '');

        try {
            $response = Http::withToken($this->token)
                ->get($this->apiUrl . 'courier/track/shipment/' . $shipmentId);

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
