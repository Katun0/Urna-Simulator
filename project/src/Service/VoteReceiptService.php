<?php

namespace App\Service;

use App\Entity\Voto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class VoteReceiptService
{
    public function __construct(#[Autowire('%env(APP_VOTE_PEPPER)%')] private readonly string $pepper)
    {
        if (strlen($this->pepper) < 32) {
            throw new \InvalidArgumentException('APP_VOTE_PEPPER must contain at least 32 characters.');
        }
    }

    public function issue(Voto $vote): string
    {
        $key = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $salt = bin2hex(random_bytes(16));
        $hash = hash_hmac('sha256', $salt.':'.$key, $this->pepper);

        $vote->setCodigoAutenticacao($hash);
        $vote->setCodigoAutenticacaoSalt($salt);

        return $key;
    }

    public function verify(Voto $vote, string $key): bool
    {
        $salt = $vote->getCodigoAutenticacaoSalt();
        $storedHash = $vote->getCodigoAutenticacao();

        if ($salt === null || $storedHash === null) {
            return false;
        }

        $candidateHash = hash_hmac('sha256', $salt.':'.$key, $this->pepper);

        return hash_equals($storedHash, $candidateHash);
    }
}
