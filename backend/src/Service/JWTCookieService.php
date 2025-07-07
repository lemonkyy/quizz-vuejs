<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;

class JWTCookieService
{
    private int $lifetime;
    private string $path;
    private ?string $domain;
    private bool $secure;
    private bool $httpOnlyHp;
    private bool $httpOnlyS;
    private string $sameSite;

    public function __construct(ParameterBagInterface $params)
    {
        $this->path = $params->get('jwt_cookie_path');
        $this->domain = $params->get('jwt_cookie_domain');
        $this->secure = $params->get('jwt_cookie_secure');
        $this->httpOnlyHp = $params->get('jwt_cookie_http_only_hp');
        $this->httpOnlyS = $params->get('jwt_cookie_http_only_s');
        $this->sameSite = $params->get('jwt_cookie_same_site');
        $this->lifetime = $params->get('jwt_cookie_lifetime');
    }

    public function createCookies(string $jwtToken): array
    {
        list($header, $payload, $signature) = explode('.', $jwtToken);

        $cookieHp = Cookie::create('jwt_hp')
            ->withValue($header . '.' . $payload)
            ->withExpires(time() + $this->lifetime)
            ->withPath($this->path)
            ->withDomain($this->domain)
            ->withSecure($this->secure)
            ->withHttpOnly($this->httpOnlyHp)
            ->withSameSite($this->sameSite);

        $cookieS = Cookie::create('jwt_s')
            ->withValue($signature)
            ->withExpires(time() + $this->lifetime)
            ->withPath($this->path)
            ->withDomain($this->domain)
            ->withSecure($this->secure)
            ->withHttpOnly($this->httpOnlyS)
            ->withSameSite($this->sameSite);

        return [$cookieHp, $cookieS];
    }
}
