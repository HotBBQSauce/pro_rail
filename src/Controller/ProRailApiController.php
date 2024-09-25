<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Service\ProRailApiClient;

class ProRailApiController extends AbstractController
{
    private ProRailApiClient $proRailApiClient;
    private LoggerInterface $logger;

    public function __construct(ProRailApiClient $proRailApiClient, LoggerInterface $logger)
    {
        $this->proRailApiClient = $proRailApiClient;
        $this->logger = $logger;
    }

    #[Route('/api/pro-rail', methods: ['GET'])]
    public function getData(): Response
    {
        try {
            // Fetch data from the ProRail API using the service
            $content = $this->proRailApiClient->getConceptList();

            // Return the data as JSON
            return $this->json($content);
        } catch (\Exception $e) {
            // Log any exceptions that occur
            $this->logger->error('Exception occurred while fetching data: ' . $e->getMessage());
            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/pro-rail/{identifier}', methods: ['GET'])]
    public function getConceptDetails(string $identifier): Response
    {
        try {
            // Fetch specific concept details using the service
            $content = $this->proRailApiClient->getConceptDetails($identifier);

            // Return the data as JSON
            return $this->json($content);
        } catch (\Exception $e) {
            // Log any exceptions that occur
            $this->logger->error('Exception occurred while fetching concept details: ' . $e->getMessage());
            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
