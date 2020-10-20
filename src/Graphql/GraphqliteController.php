<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use function array_map;
use Gpupo\Common\Traits\LoggerAwareTrait;
use GraphQL\Error\ClientAware;
use GraphQL\Error\Debug;
use GraphQL\Error\Error;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Promise;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Upload\UploadMiddleware;
use function json_decode;
use function max;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use TheCodingMachine\Graphqlite\Bundle\Context\SymfonyGraphQLContext;
use TheCodingMachine\Graphqlite\Bundle\Controller\GraphqliteController as Core;

class GraphqliteController
{
    use LoggerAwareTrait;

    protected HttpMessageFactoryInterface $httpMessageFactory;

    /** @var bool|int */
    protected $debug;

    protected ServerConfig $serverConfig;

    public function __construct(ServerConfig $serverConfig, HttpMessageFactoryInterface $httpMessageFactory, ?int $debug = Debug::RETHROW_UNSAFE_EXCEPTIONS, LoggerInterface $logger = null)
    {
        $this->initLogger($logger, 'graphqlite');
        $this->getLogger() && $this->getLogger()->debug('controller', [
            'debug' => $debug,
        ]);
        $this->serverConfig = $serverConfig;
        $this->httpMessageFactory = $httpMessageFactory;
        $this->debug = $debug ?? false;
    }

    /**
     * Decides the HTTP status code based on the answer.
     *
     * @see https://github.com/APIs-guru/graphql-over-http#status-codes
     */
    public function decideHttpStatusCode(ExecutionResult $result): int
    {
        // If the data entry in the response has any value other than null (when the operation has successfully executed without error) then the response should use the 200 (OK) status code.
        if (null !== $result->data && empty($result->errors)) {
            return 200;
        }

        $status = 0;
        // There might be many errors. Let's return the highest code we encounter.
        foreach ($result->errors as $error) {
            $wrappedException = $error->getPrevious();
            if (null !== $wrappedException) {
                $code = $wrappedException->getCode();
                if ($code < 400 || $code >= 600) {
                    if (!($wrappedException instanceof ClientAware) || true !== $wrappedException->isClientSafe()) {
                        // The exception code is not a valid HTTP code. Let's ignore it
                        continue;
                    }

                    // A "client aware" exception is almost certainly targeting the client (there is
                    // no need to pass a server exception error message to the client).
                    // So a ClientAware exception is almost certainly a HTTP 400 code
                    $code = 400;
                }
            } else {
                $code = 400;
            }
            $status = max($status, $code);
        }

        // If exceptions have been thrown and they have not a "HTTP like code", let's throw a 500.
        if ($status < 200) {
            $status = 500;
        }

        return $status;
    }

    public function handleRequest(Request $request): Response
    {
        $this->getLogger() && $this->getLogger()->debug('init of handle with Request');

        try {
            $response = $this->processRequest($request);

            return $response;
        } catch (\Exception $exception) {
            $this->getLogger() && $this->getLogger()->error('processRequest', [
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

    public function loadRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        // prepare a new route
        $path = '/graphql';
        $defaults = [
            '_controller' => Core::class.'::handleRequest',
        ];
        $route = new Route($path, $defaults);

        // add the new route to the route collection
        $routeName = 'graphqliteRoute';
        $routes->add($routeName, $route);

        return $routes;
    }

    protected function processRequest(Request $request): Response
    {
        $this->getLogger() && $this->getLogger()->debug('Create Request');
        $psr7Request = $this->httpMessageFactory->createRequest($request);

        if ('POST' === mb_strtoupper($request->getMethod()) && empty($psr7Request->getParsedBody())) {
            $this->getLogger() && $this->getLogger()->debug('Deal with POST');
            $content = $psr7Request->getBody()->getContents();
            $this->getLogger() && $this->getLogger()->debug('Parse content');
            $parsedBody = json_decode($content, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                $message = 'Invalid JSON received in POST body: '.json_last_error_msg();
                $this->getLogger() && $this->getLogger()->error('Stop', ['message' => $message]);

                throw new \RuntimeException($message);
            }
            $this->getLogger() && $this->getLogger()->debug('Make PSR7 Request');
            $psr7Request = $psr7Request->withParsedBody($parsedBody);
        }

        // Let's parse the request and adapt it for file uploads.
        $this->getLogger() && $this->getLogger()->debug('Deal with file upload');
        $uploadMiddleware = new UploadMiddleware();
        $psr7Request = $uploadMiddleware->processRequest($psr7Request);

        return $this->handlePsr7Request($psr7Request, $request);
    }

    protected function handlePsr7Request(ServerRequestInterface $request, Request $symfonyRequest): JsonResponse
    {
        $this->getLogger() && $this->getLogger()->debug('put the request in the context');
        $serverConfig = clone $this->serverConfig;
        $serverConfig->setContext(new SymfonyGraphQLContext($symfonyRequest));

        $this->getLogger() && $this->getLogger()->debug('Process result');
        $standardService = new StandardServer($serverConfig);
        $result = $standardService->executePsrRequest($request);

        if ($result instanceof ExecutionResult) {
            $this->getLogger() && $this->getLogger()->debug('Decide Http Code');
            $httpCode = $this->decideHttpStatusCode($result);
            $this->getLogger() && $this->getLogger()->debug('Return Json', [
                'httpCode' => $httpCode,
            ]);

            return new JsonResponse($result->toArray($this->debug), $httpCode);
        }
        $this->getLogger() && $this->getLogger()->debug('Deal With array result');
        if (\is_array($result)) {
            $finalResult = array_map(function (ExecutionResult $executionResult) {
                return $executionResult->toArray($this->debug);
            }, $result);
            // Let's return the highest result.
            $statuses = array_map([$this, 'decideHttpStatusCode'], $result);
            $status = max($statuses);

            return new JsonResponse($finalResult, $status);
        }
        if ($result instanceof Promise) {
            throw new RuntimeException('Only SyncPromiseAdapter is supported');
        }

        throw new RuntimeException('Unexpected response from StandardServer::executePsrRequest'); // @codeCoverageIgnore
    }
}
