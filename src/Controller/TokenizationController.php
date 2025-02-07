<?php

namespace App\Controller;

use App\Service\TokenizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TokenizationController extends AbstractController
{
    public function __construct(private readonly TokenizationService $tokenizationService) {}

    #[Route('/tokenize', name: 'app_tokenize', methods: ['POST'])]
    public function tokenize(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!$this->validateInput($content)) {
            return new JsonResponse(['error' => 'Invalid input. Required fields: id and data.'], 400);
        }

        $tokenizedData = $this->tokenizationService->tokenize($content['data']);
        $responseData = [
            'id' => $content['id'],
            'data' => $tokenizedData,
        ];

        return new JsonResponse($responseData, 201);
    }

    #[Route('/detokenize', name: 'app_detokenize', methods: ['POST'])]
    public function detokenize(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!$this->validateInput($content)) {
            return new JsonResponse(['error' => 'Invalid input. Required fields: id and data.'], 400);
        }

        $detokenizedData = $this->tokenizationService->detokenize($content['data']);
        $responseData = [
            'id' => $content['id'],
            'data' => $detokenizedData,
        ];
        return new JsonResponse($responseData, 200);
    }

    private function validateInput(array $content): bool
    {
        return isset($content['id']) && isset($content['data']) && is_array($content['data']);
    }
}
