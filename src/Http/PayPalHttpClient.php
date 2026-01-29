<?php

namespace Zaenalrfn\LaravelPayPal\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;
use Zaenalrfn\LaravelPayPal\Support\PayPalLogger;

class PayPalHttpClient
{
    protected Client $client;
    protected array $config;
    protected ?string $accessToken = null;

    public function __construct()
    {
        $mode = config('paypal.mode');

        $this->config = config("paypal.$mode");

        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout' => config('paypal.http.timeout', 30),
        ]);
    }

    /**
     * Get OAuth access token
     */
    protected function authenticate(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = $this->client->post('/v1/oauth2/token', [
                'auth' => [
                    $this->config['client_id'],
                    $this->config['client_secret'],
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            PayPalLogger::info('PayPal OAuth success');

            return $this->accessToken = $data['access_token'];
        } catch (GuzzleException $e) {
            PayPalLogger::error('PayPal OAuth failed', [
                'error' => $e->getMessage(),
            ]);

            throw (new PayPalException('PayPal authentication failed'))
                ->withContext(['stage' => 'oauth']);
        }
    }

    /**
     * Send request to PayPal APIx
     */
    public function request(string $method, string $uri, array $payload = []): array
    {
        try {
            $token = $this->authenticate();

            PayPalLogger::info('PayPal request', [
                'method' => $method,
                'uri' => $uri,
            ]);

            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
            ];

            if (!empty($payload)) {
                $options['json'] = $payload;
            }

            $response = $this->client->request($method, $uri, $options);

            $result = json_decode($response->getBody()->getContents(), true);

            PayPalLogger::info('PayPal response', [
                'uri' => $uri,
                'status' => $result['status'] ?? null,
                'id' => $result['id'] ?? null,
            ]);

            return $result;
        } catch (GuzzleException $e) {
            PayPalLogger::error('PayPal API error', [
                'method' => $method,
                'uri' => $uri,
                'message' => $e->getMessage(),
            ]);

            throw (new PayPalException('PayPal API request failed'))
                ->withContext([
                    'method' => $method,
                    'uri' => $uri,
                ]);
        }
    }
}
