<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ProRailController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(LoggerInterface $logger): Response
    {
        $client = HttpClient::create();
        $apiKey = $_SERVER['API_KEY'];
        $base_url = $_SERVER['BASE_URL'];
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

        // Log de statuscode voor debugging
        $logger->info('Status code for concept list: ' . $statusCode);

        if ($statusCode !== 200) {
            $logger->error('Error fetching data from ProRail API.');
            return new Response('Error fetching', $statusCode);
        }

        $content = $response->toArray();

        // Render de template en geef de opgehaalde data door
        return $this->render('base.html.twig', [
            'data' => $content['data']
        ]);
    }

    #[Route('/concept/{identifier}', name: 'concept_detail', requirements: ['identifier' => '.+'])]
    public function conceptDetail(string $identifier, LoggerInterface $logger): Response
    {
        $client = HttpClient::create();
        $apiKey = $_SERVER['API_KEY'];
        $base_url = $_SERVER['BASE_URL'];
        // Constructeer de volledige IRI met de base URL en de ontvangen identifier
        $request_url = $base_url  . rawurlencode('https://otl.prorail.nl/concept/' . $identifier);

        // Log de geconstrueerde URL voor debugging
        $logger->info('Request URL for concept details: ' . $request_url);


        // Maak de request
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

        // Log de statuscode voor debugging
        $statusCode = $response->getStatusCode();
        $logger->info('Status code for concept details: ' . $statusCode);

        // Verwerk het antwoord
        if ($statusCode !== 200) {
            $logger->error('Error fetching concept details for identifier: ' . $identifier);
            return new Response('Error fetching concept details', $statusCode);
        }

        // Zet de response om naar array
        $data = $response->toArray();

        // Render de details template en geef de opgehaalde data door
        return $this->render('details.html.twig', [
            'concept' => $data['data']
        ]);
    }


}
