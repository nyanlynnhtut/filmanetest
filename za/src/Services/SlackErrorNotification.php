<?php

namespace Za\Support\Services;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Traits\Macroable;

class SlackErrorNotification
{
    use Macroable;

    protected $client;

    protected $botName;

    protected $botIcon;

    protected $appName = 'Web Application';

    public static function captureError(
        SlackErrorNotification $service,
        Exception | \Throwable $exception,
        Closure $failingHandler = null
    ) {
        try {
            $msg = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $service->error($msg, $file, $line);
        } catch (Exception $e) {
            if ($failingHandler) {
                $failingHandler($e);
            }
        }
    }

    public function __construct($botName, $botIcon = null)
    {
        $this->botName = $botName;
        $this->botIcon = $botIcon;

        $this->client = new Client(['base_uri' => 'https://hooks.slack.com/services/', 'headers' => [
            'Content-Type' => 'application/json',
        ]]);
    }

    public function appName($name)
    {
        $this->appName = $name;

        return $this;
    }

    public function error($message, $file, $line)
    {
        $uri = config('services.slack.error_channel');
        if (empty($uri)) {
            return;
        }

        $this->sendMessage(
            $uri,
            $this->buildErrorMessage($message, $file, $line)
        );
    }

    public function message($message, $attachments = [], $channel = null)
    {
        // If custom channel is not set, send to error channel
        $channel = $channel ? config("services.slack.$channel") : config('services.slack.error_channel');
        $payload = [
            'json' => [
                'text' => $message,
                'username' => $this->botName,
                'attachments' => [$attachments],
            ],
        ];

        $this->sendMessage($channel, $payload);
    }

    public function messageWithoutErrorLog($message, $attachments = [], $channel = null)
    {
        // If custom channel is not set, send to error channel
        $channel = $channel ? config("services.slack.$channel") : config('services.slack.error_channel');
        $payload = [
            'json' => [
                'text' => $message,
                'username' => $this->botName,
                'attachments' => [$attachments],
            ],
        ];

        $this->sendMessageWithoutErrorLog($channel, $payload);
    }

    public function sendMessage($uri, $payload)
    {
        try {
            $this->client->post($uri, $payload);
        } catch (ServerException $e) {
            $this->logException($e);
        } catch (ClientException $e) {
            $this->logException($e);
        }
    }

    public function sendMessageWithoutErrorLog($uri, $payload)
    {
        try {
            $this->client->post($uri, $payload);
        } catch (ServerException $e) {
            // $this->logException($e);
        } catch (ClientException $e) {
            // $this->logException($e);
        } catch (Exception $e) {
        }
    }

    public function test($message = 'Hello Testing', $channel = null)
    {
        $channel = $channel ? config("services.slack.$channel") : config('services.slack.error_channel');
        $this->sendMessage($channel, [
            'json' => [
                'text' => $message,
            ],
        ]);
    }

    protected function buildErrorMessage($message, $file, $line)
    {
        $request = request();
        $inputs = json_encode($request->all());
        $appName = request()->header('X-APP', $this->appName);
        $appVersion = request()->header('X-APP-VERSION', 'Unknown');

        return [
            'json' => [
                'text' => $message,
                'username' => $this->botName,
                'icon_emoji' => $this->botIcon ?? ':boom:',
                'attachments' => [
                    [
                        'color' => '#f4645f',
                        'fields' => [
                            [
                                'title' => 'File',
                                'value' => $file,
                                'short' => false,
                            ],
                            [
                                'title' => 'Line Number',
                                'value' => $line,
                                'short' => true,
                            ],
                            [
                                'title' => 'Request App',
                                'value' => $appName . " ($appVersion)",
                                'short' => true,
                            ],
                            [
                                'title' => 'URL (' . $request->method() . ')',
                                'value' => $request->url(),
                                'short' => false,
                            ],
                            [
                                'title' => 'Referer URL',
                                'value' => $request->header('Referer'),
                                'short' => false,
                            ],

                            [
                                'title' => 'Request Data',
                                'value' => $inputs,
                                'short' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function logException($e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $error = [
                'body' => $response->getBody()->getContents(),
                'status' => $response->getStatusCode(),
            ];
            \Log::error('SLACK ERROR: ' . json_encode($error));
        }
    }
}
