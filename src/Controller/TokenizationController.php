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

    /**
     * Tokenizes and encrypts the provided data.
     *
     * Expected JSON payload:
     * {
     *   "id": "req-123",
     *   "data": {
     *     "field1": "value1",
     *     "field2": "value2",
     *     "fieldn": "valuen"
     *   }
     * }
     *
     * @return JsonResponse JSON with tokenized data.
     */
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

    /**
     * Detokenizes the provided tokens by retrieving and decrypting the associated data.
     *
     * Expected JSON payload:
     * {
     *   "id": "req-33445",
     *   "data": {
     *     "field1": "token1",
     *     "field2": "token2",
     *     "field3": "token3"
     *   }
     * }
     *
     * @return JsonResponse JSON with detokenized data.
     */
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
