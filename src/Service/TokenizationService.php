<?php

namespace App\Service;

class TokenizationService
{
    private array $tokenMap = [];

    public function tokenize(array $data): array
    {
        foreach ($data as $field => $value) {
            $token = bin2hex(random_bytes(8));
            $tokenizedData[$field] = $token;
            $this->tokenMap[$token] = $value;
        }
        return $tokenizedData;
    }

    public function detokenize(array $data): array
    {
        $detokenizedData = [];
        foreach ($data as $field => $token) {
            if (isset($this->tokenMap[$token])) {
                $detokenizedData[$field] = [
                    'found' => true,
                    'value' => $this->tokenMap[$token],
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
