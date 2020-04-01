<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Test;

use Gpupo\PackSymfonyCommon\HttpClient\ApiClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiClientTestCase extends AbstractTestCase
{
    protected function factoryMockResponse(string $content, int $statusCode = 200, array $info = []): ResponseInterface
    {
        return new MockResponse($content, ['http_code' => $statusCode] + $info);
    }

    protected function factoryMockClient(ResponseInterface $response): HttpClientInterface
    {
        return new MockHttpClient($response);
    }

    abstract protected function factoryApiClient(array $options, HttpClientInterface $client): ApiClientInterface;

    protected function readMockupContent(string $path): string
    {
        $filename = sprintf('%s/mockup/%s.json', $this->getPathResourcesDir(), $path);

        return $this->resourceReadFile($filename);
    }
}
