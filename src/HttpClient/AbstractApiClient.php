<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace App\HttpClient;

use Gpupo\Common\Traits\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiClient
{
    use HttpClientTrait;

    use LoggerAwareTrait;

    private HttpClientInterface $httpClient;

    private array $options;

    final public function __construct(array $options = [], HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->initLogger($logger, 'http-client');
        $this->setOptions($options);
        $this->httpClient = $httpClient;
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    protected function setOptions(array $options): void
    {
        $this->options = $options;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    protected function request(string $mode, string $endpoint, array $parameters): ResponseInterface
    {
        $response = $this->getHttpClient()->request($mode, $endpoint, $parameters);

        return $response;
    }

    public function getRequest(string $path): ResponseInterface
    {
        return $this->request('GET', $path);
    }

    public function payloadRequest(string $path, array $payload, string $method): ResponseInterface
    {
        return $this->request($method, $path, [
            'json' => $payloads,
        ]);
    }

    public function postRequest(string $path, array $payload): ResponseInterface
    {
        return $this->payloadRequest($path, $payload, 'POST');
    }

    public function putRequest(string $path, array $payload): ResponseInterface
    {
        return $this->payloadRequest($path, $payload, 'PUT');
    }
}
