<?php

namespace Za\Support\Services;

use GuzzleHttp\Client;

class Firebase
{
    protected static $serverKey;

    protected static $senderId;

    public static function configure($serverKey, $senderId)
    {
        static::$serverKey = $serverKey;
        static::$senderId = $senderId;
    }

    public static function send($token, $title, $body, $serverKey = null, $senderId = null)
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        static::$serverKey = $serverKey ? $serverKey : static::$serverKey;
        static::$senderId = $senderId ? $senderId : static::$senderId;

        $downstreamResponse = static::request($token, $notification);

        return [
            'success' => $downstreamResponse['success'],
            'fail' => $downstreamResponse['failure'],
        ];
    }

    public static function sendData($token, $title, $body, $payloadData = [], $serverKey = null, $senderId = null)
    {
        $notification = ['title' => null, 'body' => null];
        $fixData = [
            'title' => $title,
            'body' => $body,
        ];
        $data = array_merge($fixData, $payloadData);

        static::$serverKey = $serverKey ? $serverKey : static::$serverKey;
        static::$senderId = $senderId ? $senderId : static::$senderId;

        $downstreamResponse = static::request($token, $notification, $data);

        return [
            'success' => $downstreamResponse['success'],
            'fail' => $downstreamResponse['failure'],
        ];
    }

    protected static function request($token, $notification, $data = null)
    {
        $tokenKey = is_array($token) ? 'registration_ids' : 'to';
        $response = (new Client)->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key='.static::$serverKey,
                'Content-Type' => 'application/json',
                'project_id' => static::$senderId,
            ],
            'json' => [
                $tokenKey => $token,
                'notification' => $notification,
                'data' => $data,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
