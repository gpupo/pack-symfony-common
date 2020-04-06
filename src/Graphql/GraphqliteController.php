<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use Gpupo\Common\Traits\LoggerAwareTrait;
use GraphQL\Error\Debug;
use GraphQL\Server\ServerConfig;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TheCodingMachine\Graphqlite\Bundle\Controller\GraphqliteController as Core;

class GraphqliteController extends Core
{
    use LoggerAwareTrait;

    public function __construct(ServerConfig $serverConfig, HttpMessageFactoryInterface $httpMessageFactory = null, ?int $debug = Debug::RETHROW_UNSAFE_EXCEPTIONS, LoggerInterface $logger = null)
    {
        $this->initLogger($logger, 'graphqlite');
        $this->getLogger() && $this->getLogger()->debug('controller', [
            'debug' => $debug,
        ]);

        parent::__construct($serverConfig, $httpMessageFactory, $debug);
    }

    public function handleRequest(Request $request): Response
    {
        try {
            $response = parent::handleRequest($request);
            $this->getLogger() && $this->getLogger()->debug('response', [
                'request' => $request,
                'response' => $response,
                ]);

            return $response;
        } catch (\Exception $exception) {
            $this->getLogger() && $this->getLogger()->error('handleRequest', [
                'exception' => $exception,
                ]);

            return new JsonResponse(
                [
                    'error' => [
                        'code' => 'internal error',
                        'message' => $exception->getMessage(),
                    ],
                ],
                500
            );
        }
    }
}
