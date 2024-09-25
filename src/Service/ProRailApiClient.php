<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ProRailApiClient
{
    private HttpClientInterface $client;
    private string $baseUrl;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, string $baseUrl, string $apiKey, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }


    private function sendRequest(string $method, string $endpoint, array $options = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $defaultOptions = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->apiKey,
            ]
        ];

        $options = array_merge($defaultOptions, $options);

        $this->logger->info('Requesting URL: ' . $url);

        $response = $this->client->request($method, $url, $options);

        $statusCode = $response->getStatusCode();
        $this->logger->info('Response Status Code: ' . $statusCode);

        if ($statusCode !== 200) {
            $this->logger->error('Error during request: ' . $url . ' Status: ' . $statusCode);
            throw new \Exception('Error fetching data from ProRail API. Status Code: ' . $statusCode);
        }

        return $response->toArray();
    }


    public function getConceptList(): array
    {
        return $this->sendRequest('GET', '/');
    }


    public function getConceptDetails(string $identifier): array
    {
        $encodedIdentifier = rawurlencode('https://otl.prorail.nl/concept/' . $identifier);
        return $this->sendRequest('GET', $encodedIdentifier);
    }
}
