<?php

/*
 * This file is not part of the API Platform project.
 * I won't copyright my work.
 */

declare(strict_types=1);

namespace Darkweak\SouinApiPlatformBundle\HttpCache;

use ApiPlatform\Core\HttpCache\PurgerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Purges Souin.
 */
final class SouinRegexPurger implements PurgerInterface
{
    private const MAX_URL_SIZE_PER_BATCH = 1500;
    private const SEPARATOR = '&ykey=';
    private const SOUIN_COOKIE_NAME = 'souin-authorization-token';

    private $logger;

    // Clients to send cache invalidation
    private $clients;

    // User credentials if Souin is protected by JWT
    private $password;
    private $username;

    // Souin relative paths
    private $souinApiAuthenticationPath;
    private $souinApiSouinPath;
    private $souinBaseApiPath;
    private $souinBaseHost;
    private $souinBaseUrl;

    // User token dynamically assigned
    private $token;

    /**
     * @param ClientInterface[] $clients
     * @param string $souinBaseHost
     * @param string $souinBaseUrl
     * @param string $souinBaseApiPath
     * @param string $souinApiSouinPath
     * @param string $souinApiAuthenticationPath
     * @param string $username
     * @param string $password
     * @param LoggerInterface $logger
     */
    public function __construct(
        array $clients,
        string $souinBaseHost,
        string $souinBaseUrl,
        string $souinBaseApiPath,
        string $souinApiSouinPath,
        string $souinApiAuthenticationPath,
        string $username,
        string $password,
        LoggerInterface $logger
    )
    {
        $this->clients = $clients;
        $this->password = $password;
        $this->souinBaseHost = $souinBaseHost;
        $this->souinBaseUrl = $souinBaseUrl;
        $this->souinBaseApiPath = $souinBaseApiPath;
        $this->souinApiSouinPath = $souinApiSouinPath;
        $this->souinApiAuthenticationPath = $souinApiAuthenticationPath;
        $this->username = $username;
        $this->logger = $logger;
    }

    private function getBaseUrl(): string
    {
        return $this->souinBaseUrl . $this->souinBaseApiPath;
    }

    private function getSouinApiUrl(): string
    {
        return $this->getBaseUrl() . $this->souinApiSouinPath;
    }

    private function getSouinAuthenticationUrl(): string
    {
        return $this->getBaseUrl() . $this->souinApiAuthenticationPath;
    }

    private function getParametersFromIris(array $iris): string
    {
        return \sprintf('?ykey=%s', implode(self::SEPARATOR, $iris));
    }

    private function login(): void
    {
        if ($this->username && $this->password) {
            try {
                $response = $this->clients[0]->request(
                    Request::METHOD_POST,
                    $this->getSouinAuthenticationUrl() . '/login',
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Host' => $this->souinBaseHost,
                        ]
                    ]
                );
                $cookies = $response->getHeader('Set-Cookie');
                foreach ($cookies as $cookie) {
                    $splitCookie = str_split($cookie);
                    if ($splitCookie[0] !== self::SOUIN_COOKIE_NAME) {
                        continue;
                    }

                    $this->token = $splitCookie[1] ?? '';
                }
            } catch (GuzzleException $e) {
                $this->logger->warning($e);
            }
        }
    }

    /**
     * @param array|string[] $iris
     * @return string[]
     */

    private function getChunkedRegex(array $iris): array
    {
        $regex = $this->getParametersFromIris($iris);
        $batches = [];

        while (strlen($regex) > self::MAX_URL_SIZE_PER_BATCH) {
            $splitPosition = strrpos(str_split($regex, self::MAX_URL_SIZE_PER_BATCH)[0], self::SEPARATOR);
            if ($splitPosition) {
                [$batches[], $regex] = str_split($regex, $splitPosition);
            }
        }

        return [...$batches, $regex];
    }

    private function banRegex(string $regex): void
    {
        foreach ($this->clients as $client) {
            try {
                $client->request(
                    Request::METHOD_PURGE,
                    $this->getSouinApiUrl() . $regex,
                    ['headers' => \array_merge(
                        ['Host' => $this->souinBaseHost],
                        $this->token ?
                            ['Cookie' => sprintf('%s=%s', self::SOUIN_COOKIE_NAME, $this->token)] :
                            []
                    )]
                );
            } catch (GuzzleException $e) {
                $this->logger->warning($e);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function purge(array $iris)
    {
        if (!$iris || !\count($iris)) {
            return;
        }

        $this->login();
        foreach ($this->getChunkedRegex($iris) as $chunkedRegex) {
            $this->banRegex($chunkedRegex);
        }
    }
}
