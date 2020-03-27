<?php

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

    private array $options; 

    protected HttpClientInterface $httpClient;

    final public function __construct(array $options = [], HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->initLogger($logger, 'http-client');
        
        $this->setOptions($options);

        $this->httpClient = $httpClient;
    }

    protected function setOptions(array $options): void
    {
        $this->options = $options;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    public function getHttpClient() : HttpClientInterface
    {
        return $this->httpClient;
    }

    protected function request(string $mode, string $endpoint, array $parameters): ResponseInterface
    {
        $response = $this->getHttpClient()->request($mode, $endpoint, $parameters);
        return $response;
    }
    
}