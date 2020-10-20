<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\HttpClient;

use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageFactory extends PsrHttpFactory implements HttpMessageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(Request $symfonyRequest)
    {
        try {
            return parent::createRequest($symfonyRequest);
        } catch (\Exception $exception) {
            return RuntimeException('Fail on create request', 0, $exception);
        }
    }
}
