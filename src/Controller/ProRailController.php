<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProRailApiClient;

class ProRailController extends AbstractController
{
    private ProRailApiClient $proRailApiClient;

    public function __construct(ProRailApiClient $proRailApiClient)
    {
        $this->proRailApiClient = $proRailApiClient;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        try {
            $content = $this->proRailApiClient->getConceptList();
        } catch (\Exception $e) {
            return new Response('Error fetching concept list', 500);
        }

        // Render the template with the fetched data
        return $this->render('base.html.twig', [
            'data' => $content['data']
        ]);
    }

    #[Route('/concept/{identifier}', name: 'concept_detail', requirements: ['identifier' => '.+'])]
    public function conceptDetail(string $identifier): Response
    {
        try {
            $data = $this->proRailApiClient->getConceptDetails($identifier);
        } catch (\Exception $e) {
            return new Response('Error fetching concept details', 500);
        }

        // Render the template with the fetched concept details
        return $this->render('details.html.twig', [
            'concept' => $data['data']
        ]);
    }
}
