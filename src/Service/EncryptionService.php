<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class EncryptionService
{
    private string $key;
    private string $cipher = 'aes-256-gcm';

    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->key = getenv('ENCRYPTION_KEY');
    }

    public function encrypt(string $plaintext): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $tag = '';
        $encrypted = openssl_encrypt($plaintext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        $combined = $iv . $tag . $encrypted;
        return base64_encode($combined);
    }

    public function decrypt(string $ciphertextEncoded): ?string
    {
        $combined = base64_decode($ciphertextEncoded);
        if ($combined === false) {
            $this->logger->error('Base64 decode failed', ['ciphertextEncoded' => $ciphertextEncoded]);
            return null;
        }
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $tagLength = 16;
        if (strlen($combined) < ($ivLength + $tagLength)) {
            $this->logger->error('Ciphertext too short', ['combined_length' => strlen($combined)]);
            return null;
        }
        $iv = substr($combined, 0, $ivLength);
        $tag = substr($combined, $ivLength, $tagLength);
        $ciphertext = substr($combined, $ivLength + $tagLength);
        $decrypted = openssl_decrypt($ciphertext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($decrypted === false) {
            $this->logger->error('Decryption failed', ['ciphertext' => $ciphertextEncoded]);
            return null;
        }
        return $decrypted !== false ? $decrypted : null;
    }
}