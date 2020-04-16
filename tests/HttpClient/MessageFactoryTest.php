<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Tests\HttpClient;

use Gpupo\PackSymfonyCommon\HttpClient\MessageFactory;
use Gpupo\PackSymfonyCommon\Tests\TestCaseAbstract;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversNothing
 */
class MessageFactoryTest extends TestCaseAbstract
{
    public function testCreateRequest()
    {
        $symfonyResponse = new Response('Content');
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new MessageFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrResponse = $psrHttpFactory->createResponse($symfonyResponse);

        $this->assertInstanceof(ResponseInterface::class, $psrResponse);
    }
}
