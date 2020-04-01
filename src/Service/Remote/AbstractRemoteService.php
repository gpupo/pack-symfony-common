<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Service\Remote;

use Gpupo\PackSymfonyCommon\HttpClient\ApiClientAwareTrait;
use Gpupo\PackSymfonyCommon\HttpClient\ApiClientInterface;
use Gpupo\PackSymfonyCommon\Service\AbstractService;

abstract class AbstractRemoteService extends AbstractService
{
    use ApiClientAwareTrait;

    public function __construct(ApiClientInterface $apiClient)
    {
        $this->setApiClient($apiClient);
    }
}
