<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ProRailApiController extends AbstractController
{
    #[Route('/api/pro-rail', methods: ['GET'])]
    public function getData(LoggerInterface $logger): Response
    {
        $client = HttpClient::create();
        $apiKey = $_SERVER['API_KEY'];
        $base_url = $_SERVER['BASE_URL'];

        try {
            $response = $client->request(
                'GET',
                $base_url,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => $apiKey
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            // Log de statuscode voor debugging
            $logger->info('Status code for ProRail API fetch: ' . $statusCode);

            if ($statusCode !== 200) {
                $logger->error('Error fetching data from ProRail API.');
                return new Response('Error fetching data', $statusCode);
            }

            return $this->json($content);
        } catch (\Exception $e) {
            // Log de uitzondering
            $logger->error('Exception occurred while fetching data: ' . $e->getMessage());
            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/pro-rail/{identifier}', methods: ['GET'])]
    public function getConceptDetails(string $identifier, LoggerInterface $logger): Response
    {
        $client = HttpClient::create();
        $apiKey = $_SERVER['API_KEY'];
        $base_url = $_SERVER['BASE_URL'];

        // Constructeer de volledige URL met de base URL en identifier
        $request_url = $base_url . rawurlencode('https://otl.prorail.nl/concept/' . $identifier);

        try {
            $response = $client->request(
                'GET',
                $request_url,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => $apiKey,
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            // Log de statuscode voor debugging
            $logger->info('Status code for concept details fetch: ' . $statusCode);

            if ($statusCode !== 200) {
                $logger->error('Error fetching concept details for identifier: ' . $identifier);
                return new Response('Error fetching concept details', $statusCode);
            }

            return $this->json($content);
        } catch (\Exception $e) {
            // Log de uitzondering
            $logger->error('Exception occurred while fetching concept details: ' . $e->getMessage());
            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
