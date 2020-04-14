<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\HttpClient;

use Gpupo\Common\Tools\Cache\SimpleCacheAwareTrait;
use Gpupo\Common\Traits\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiClient implements ApiClientInterface
{
    use HttpClientTrait;
    use HttpClientAwareTrait;
    use LoggerAwareTrait;
    use SimpleCacheAwareTrait;

    private array $options;

    public function __construct(array $options, HttpClientInterface $httpClient, CacheInterface $cache = null, LoggerInterface $logger = null)
    {
        $this->initLogger($logger, 'api-http-client');
        $this->setOptions($options);
        $this->setHttpCLient($httpClient);
        $this->setSimpleCache($cache);
    }

    public function getRequest(string $path, array $options = []): ResponseInterface
    {
        if (!$this->hasSimpleCache()) {
            return $this->request('GET', $path, $options);
        }

        $cacheKey = $this->simpleCacheGenerateId([$path, $options], 'url_');

        return $this->getSimpleCache()->get($cacheKey, function (ItemInterface $item) use ($path, $options) {
            $item->expiresAfter(3600);

            return $this->request('GET', $path, $options);
        });
    }

    public function postRequest(string $path, array $payload, array $options = []): ResponseInterface
    {
        return $this->payloadRequest($path, $payload, 'POST', $options);
    }

    public function putRequest(string $path, array $payload, array $options = []): ResponseInterface
    {
        return $this->payloadRequest($path, $payload, 'PUT', $options);
    }

    protected function setOptions(array $options): void
    {
        $this->options = $options;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    protected function factoryRequestOptions(): array
    {
        return [
            'headers' => [
                'Content-Type' => 'text/json',
            ],
            'extra' => [
                'no_cache' => false,
            ],
        ];
    }

    protected function factoryRequestUrl(string $path): string
    {
        return $path;
    }

    protected function request(string $method, string $path, array $options): ResponseInterface
    {
        $url = $this->factoryRequestUrl($path);
        $options = $this->factoryRequestOptions() + $options;
        $this->getLogger() && $this->getLogger()->debug('request', [
            'client' => \get_class($this->getHttpClient()),
            'method' => $method,
            'endpoint' => $url,
            'options' => $options,
            ]);

        $response = $this->getHttpClient()->request($method, $url, $options);
        if (399 < $response->getStatusCode()) {
            $this->getLogger() && $this->getLogger()
                ->error('content', ['response' => json_decode($response->getContent(false), true)]);
        }

        return $response;
    }

    protected function payloadNormalize(array $payload): array
    {
        return $payload;
    }

    protected function payloadRequest(string $path, array $payload, string $method, array $options): ResponseInterface
    {
        return $this->request($method, $path, [
            'json' => $this->payloadNormalize($payload),
        ] + $options);
    }
}
