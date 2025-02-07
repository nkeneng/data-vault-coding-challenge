<?php

namespace App\Service;

class EncryptionService
{
    private string $key;
    private string $cipher = 'aes-256-gcm';

    public function __construct()
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
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $tagLength = 16;
        $iv = substr($combined, 0, $ivLength);
        $tag = substr($combined, $ivLength, $tagLength);
        $ciphertext = substr($combined, $ivLength + $tagLength);

        $decrypted = openssl_decrypt($ciphertext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        return $decrypted !== false ? $decrypted : null;
    }
}