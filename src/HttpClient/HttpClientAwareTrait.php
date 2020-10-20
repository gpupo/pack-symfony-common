<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

trait HttpClientAwareTrait
{
    protected HttpClientInterface $httpClient;

    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function getHttpClient(): ?HttpClientInterface
    {
        return $this->httpClient;
    }
}
