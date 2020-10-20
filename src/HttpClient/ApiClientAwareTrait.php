<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\HttpClient;

trait ApiClientAwareTrait
{
    protected ApiClientInterface $apiClient;

    public function setApiClient(ApiClientInterface $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    public function getApiClient(): ?ApiClientInterface
    {
        return $this->apiClient;
    }
}
