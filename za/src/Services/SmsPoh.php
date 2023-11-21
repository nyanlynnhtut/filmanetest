<?php

namespace Za\Support\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

class SmsPoh
{
    private $key;

    private $sender;

    public function __construct($key, $sender)
    {
        $this->key = $key;
        $this->sender = $sender;
    }

    public function poh($number, $message, $clientReference = null, $testMode = false, ?string $sender = null)
    {
        $response = (new Client())->post('https://smspoh.com/api/v2/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->key,
            ],
            'json' => [
                'to' => $number,
                'message' => $message,
                'clientReference' => $clientReference,
                'test' => $testMode,
                'sender' => $sender ?: $this->sender,
            ],
        ]);

        $response = json_decode((string) $response->getBody(), true);

        if ($response['status']) {
            return $response['data'];
        }

        return false;
    }

    public function messages($page = 1)
    {
        $response = (new Client())->get('https://smspoh.com/api/v2/messages?is_delivered=false&page=' . $page, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->key,
            ],
        ]);

        $response = json_decode((string) $response->getBody(), true);

        if ($response['status']) {
            return $response['data'];
        }

        return false;
    }

    public function status($smsPohId)
    {
        try {
            $response = (new Client())->get('https://smspoh.com/api/v2/messages/status?id=' . $smsPohId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->key,
                ],
            ]);

            $response = json_decode((string) $response->getBody(), true);

            if ($response['status']) {
                return $response['data'];
            }
        } catch (ServerException $e) {
            info('SMSPoh Fail : ' . $smsPohId);
        } catch (RequestException $e) {
            info('SMSPoh Fail : ' . $smsPohId);
        }

        return false;
    }
}
