<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-env', name: 'test_env')]
    public function testEnv(): Response
    {
        $baseUrl = $_ENV['BASE_URL'];
        $apiKey = $_ENV['API_KEY'];

        return new Response("Base URL: $baseUrl, API Key: $apiKey");
    }
}
