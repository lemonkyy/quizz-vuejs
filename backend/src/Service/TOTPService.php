<?php

namespace App\Service;

class TOTPService
{
    private string $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecretKey(): string
    {
        return $this->base32Encode(random_bytes(16));
    }

    public function generateTOTP(string $secretKey): string
    {
        //take the current time, divide by 30 to get the time step
        $time = floor(time() / 30);
        //pack the time into a binary string
        $time = pack('J', $time);
        //decode the base32 secret key
        $key = $this->base32Decode($secretKey);
        //create a hash using HMAC with the secret key and the time
        $hash = hash_hmac('sha1', $time, $key, true);
        //truncate the hash to get a 6-digit code
        $offset = ord($hash[19]) & 0x0F;
        //extract 4 bytes from the hash starting at the offset
        $truncatedHash = substr($hash, $offset, 4);
        //convert the 4 bytes to a number and apply a mask to get a positive integer
        $code = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;
        //reduce the number to a 6-digit code
        $code = $code % 1000000;

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    public function verifyTOTP(string $secretKey, string $userCode): bool
    {
        $currentCode = $this->generateTOTP($secretKey);

        return hash_equals($currentCode, $userCode);
    }

    private function base32Encode(string $input): string {
        $binary = '';

        //convert each character to binary
        foreach (str_split($input) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';
        //spit binary into chunks of 5 bits
        $chunks = str_split($binary, 5);

        //convert each chunk into a base32 character
        foreach ($chunks as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $index = bindec($chunk);
            $encoded .= $this->alphabet[$index];
        }

        return $encoded;
    }

    private function base32Decode(string $encoded): string
    {
        $binary = '';

        //convert each Base32 character to its 5 bits binary representation
        foreach (str_split($encoded) as $char) {
            $index = strpos($this->alphabet, $char);
            if ($index === false) {
                throw new \InvalidArgumentException("Invalid Base32 character: $char");
            }
            $binary .= str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }

        //split binary into chunks of 8 bits
        $chunks = str_split($binary, 8);

        $decoded = '';
        //convert each 8 bits chunk into a character
        foreach ($chunks as $chunk) {
            if (strlen($chunk) == 8) {
                $decoded .= chr(bindec($chunk));
            }
        }

        return $decoded;
    }

}
