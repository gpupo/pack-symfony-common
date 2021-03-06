<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\HttpClient;

use Gpupo\Common\Traits\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpClientKernel;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class CachingHttpClient implements HttpClientInterface
{
    use HttpClientAwareTrait;
    use HttpClientTrait;
    use LoggerAwareTrait;

    protected $cache;
    protected $defaultOptions = self::OPTIONS_DEFAULTS;

    public function __construct(HttpClientInterface $httpClient, StoreInterface $store, array $defaultOptions = [], LoggerInterface $logger = null)
    {
        if (!class_exists(HttpClientKernel::class)) {
            throw new \LogicException(sprintf('Using "%s" requires that the HttpKernel component version 4.3 or higher is installed, try running "composer require symfony/http-kernel:^4.3".', __CLASS__));
        }

        $this->initLogger($logger, 'caching-http');
        $this->setHttpCLient($httpClient);
        $this->initCache($store, $defaultOptions, true);

        unset($defaultOptions['debug'], $defaultOptions['default_ttl'], $defaultOptions['private_headers'], $defaultOptions['allow_reload'], $defaultOptions['allow_revalidate'], $defaultOptions['stale_while_revalidate'], $defaultOptions['stale_if_error'], $defaultOptions['trace_level'], $defaultOptions['trace_header']);

        if ($defaultOptions) {
            list(, $this->defaultOptions) = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $this->getLogger() && $this->getLogger()->debug('init');

        list($url, $options) = $this->prepareRequest($method, $url, $options, $this->defaultOptions, true);
        $url = implode('', $url);

        $this->getLogger() && $this->getLogger()->debug('requesting', [
            'endpoint' => $url,
            'method' => $method,
        ]);

        if (!$this->cache || !empty($options['body']) || !empty($options['extra']['no_cache']) || !\in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            try {
                $this->getLogger() && $this->getLogger()->debug('Bypass');
                $response = $this->getHttpClient()->request($method, $url, $options);

                return $response;
            } catch (\Exception $exception) {
                $this->getLogger() && $this->getLogger()->error('request error', [
                    'method' => $method,
                    'endpoint' => $url,
                    'exception' => $exception->getMessage(),
                ]);

                throw $exception;
            }
        }
        $request = Request::create($url, $method);
        $request->attributes->set('http_client_options', $options);

        foreach ($options['normalized_headers'] as $name => $values) {
            if ('cookie' !== $name) {
                foreach ($values as $value) {
                    $request->headers->set($name, mb_substr($value, 2 + \mb_strlen($name)), false);
                }

                continue;
            }

            foreach ($values as $cookies) {
                foreach (explode('; ', mb_substr($cookies, \mb_strlen('Cookie: '))) as $cookie) {
                    if ('' !== $cookie) {
                        $cookie = explode('=', $cookie, 2);
                        $request->cookies->set($cookie[0], $cookie[1] ?? '');
                    }
                }
            }
        }

        $response = $this->cache->handle($request);
        $this->getLogger() && $this->getLogger()->debug('create mock response');
        $response = new MockResponse($response->getContent(), [
            'http_code' => $response->getStatusCode(),
            'response_headers' => $response->headers->allPreserveCase(),
        ]);

        $this->getLogger() && $this->getLogger()->debug('return Response');

        return MockResponse::fromRequest($method, $url, $options, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof ResponseInterface) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new \TypeError(sprintf('%s() expects parameter 1 to be an iterable of ResponseInterface objects, %s given.', __METHOD__, \is_object($responses) ? \get_class($responses) : \gettype($responses)));
        }

        $this->getLogger() && $this->getLogger()->debug('stream init');

        $mockResponses = [];
        $clientResponses = [];

        foreach ($responses as $response) {
            if ($response instanceof MockResponse) {
                $mockResponses[] = $response;
            } else {
                $clientResponses[] = $response;
            }
        }

        if (!$mockResponses) {
            return $this->getHttpClient()->stream($clientResponses, $timeout);
        }

        if (!$clientResponses) {
            return new ResponseStream(MockResponse::stream($mockResponses, $timeout));
        }

        $this->getLogger() && $this->getLogger()->debug('returning stream');

        return new ResponseStream((function () use ($mockResponses, $clientResponses, $timeout) {
            yield from MockResponse::stream($mockResponses, $timeout);
            yield $this->getHttpClient()->stream($clientResponses, $timeout);
        })());
    }

    protected function initCache(StoreInterface $store, array $defaultOptions, bool $cacheEnabled): void
    {
        if ($cacheEnabled) {
            $this->getLogger() && $this->getLogger()->debug('init http cache');

            $kernel = new HttpClientKernel($this->getHttpClient());
            $this->cache = new HttpCache($kernel, $store, null, $defaultOptions);
        }
    }
}
