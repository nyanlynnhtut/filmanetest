<?php

namespace Za\Support\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Facades\Http;

class Lark
{
    use Macroable;

    protected $client;

    protected $appId;
    protected $appSecret;

    protected $tenantAccessToken;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;

        $this->startHeartBeat();
        
    }

    public function startHeartBeat()
    {
        $this->tenantAccessToken = $this->request('auth/v3/tenant_access_token/internal', [
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
        ])->json()['tenant_access_token'];
    }

    public function groups()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer '.$this->tenantAccessToken
        ])->get('https://open.larksuite.com/open-apis/im/v1/chats')->json();
        
    }

    public function usersFromChat($groupChatId)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer '.$this->tenantAccessToken
        ])->get('https://open.larksuite.com/open-apis/im/v1/chats/'.$groupChatId.'/members')->json();
        
    }

    public function text($text)
    {
        return $this->send(config('services.lark.default_chat'), json_encode(['text' => $text]));
    }

    public function send($chatId, $content, $type = 'text')
    {
        return $this->request('im/v1/messages?receive_id_type=chat_id', [
            'receive_id' => $chatId,
            'msg_type' => $type,
            'content' => $content
        ], ['Authorization' => 'Bearer '.$this->tenantAccessToken])->json();
    }

    public function request($uri, array $payload, array $headers = [])
    {
        return Http::withHeaders(array_merge([
            'Content-Type' => 'application/json; charset=utf-8',
        ], $headers))->post('https://open.larksuite.com/open-apis/'.$uri, $payload);
        
    }

}