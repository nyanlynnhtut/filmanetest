<?php

namespace Za\Support\Services;

use Illuminate\Support\Facades\Http;

class ZaClient
{
    private $token;

    private $url = 'https://backend.zacompany.dev/api/';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function get($uri, array $query = [])
    {
        return Http::acceptJson()->withHeaders(['X-API-TOKEN' => $this->token])
                ->get($this->url.$uri, $query);
    }

    public function post($uri, array $inputs = [])
    {
        return Http::acceptJson()->withHeaders(['X-API-TOKEN' => $this->token])
                ->post($this->url.$uri, $inputs);
    }

    public function put($uri, array $inputs = [])
    {
        return Http::acceptJson()->withHeaders(['X-API-TOKEN' => $this->token])
                ->put($this->url.$uri, $inputs);
    }

    public function delete($uri)
    {
        return Http::acceptJson()->withHeaders(['X-API-TOKEN' => $this->token])
                ->delete($this->url.$uri);
    }
}
