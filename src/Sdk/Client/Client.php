<?php

namespace Wontonee\Shiprocket\Sdk\Client;

use Wontonee\Shiprocket\Sdk\Resources\Authenticate;
use Wontonee\Shiprocket\Sdk\Resources\Shipment;
use Wontonee\Shiprocket\Sdk\Resources\Orders;
use Wontonee\Shiprocket\Sdk\Resources\Pickup;
use Wontonee\Shiprocket\Sdk\Resources\Channel;
use Wontonee\Shiprocket\Sdk\Resources\Courier;

class Client
{
    protected string $email;
    protected string $password;
    protected string $token;
    public $authenticate;
    public $shipment;
    public $orders;
    public $pickup;
    public $channel;
    public $courier;

    public function __construct($email, $password)
    {
        $this->authenticate = new Authenticate($email, $password);
        // get token after authenticate
        $this->token = $this->authenticate->getToken();
        $this->shipment = new Shipment($this->token);
        $this->orders = new Orders($this->token);
        $this->pickup = new Pickup($this->token);
        $this->channel = new Channel($this->token);
        $this->courier = new Courier($this->token);

    }
}



