<?php

namespace App\Service;

use App\Entity\Data;
use App\Repository\DataRepository;
use Doctrine\ORM\EntityManagerInterface;

class TokenizationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EncryptionService $encryptionService,
        private readonly DataRepository $dataRepository
    ) {}

    public function tokenize(array $data): array
    {
        foreach ($data as $field => $value) {
            $token = bin2hex(random_bytes(8));
            $encryptedValue = $this->encryptionService->encrypt($value);
            $this->entityManager->persist((new Data())
                    ->setToken($token)
                    ->setEncryptedValue($encryptedValue)
            );
            $tokenizedData[$field] = $token;
        }
        $this->entityManager->flush();
        return $tokenizedData;
    }

    public function detokenize(array $data): array
    {
        $detokenizedData = [];
        foreach ($data as $field => $token) {
            $data = $this->dataRepository->findOneBy(['token' => $token]);
            if ($data) {
                $decryptedValue = $this->encryptionService->decrypt($data->getEncryptedValue());
                $detokenizedData[$field] = [
                    'found' => true,
                    'value' => $decryptedValue,
                ];
            } else {
                $detokenizedData[$field] = [
                    'found' => false,
                    'value' => '',
                ];
            }
        }
        return $detokenizedData;
    }
}
