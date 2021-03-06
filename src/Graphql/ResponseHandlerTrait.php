<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use Gpupo\PackSymfonyCommon\HttpClient\RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait ResponseHandlerTrait
{
    protected function throwException(string $message, int $code = 0, $previous = null, string $category = '')
    {
        throw new Exception($message, $code, $previous, $category ?: __CLASS__);
    }

    protected function checkStatusCode(ResponseInterface $response): void
    {
        $this->getLogger() && $this->getLogger()->debug('checking http status code');

        $statusCode = $response->getStatusCode();

        if (199 < $statusCode && 300 > $statusCode) {
            return;
        }

        if (399 < $statusCode && 500 > $statusCode) {
            $this->throwException(sprintf('Invalid Request (%d)[%s]', $statusCode, trim($response->getContent(false))), $statusCode);
        }

        $this->throwException('Empty Results');
    }

    protected function factoryFromResponse(ResponseInterface $response, callable $factory)
    {
        $this->getLogger() && $this->getLogger()->debug('factoring from response');

        $this->checkStatusCode($response);

        return $factory($response);
    }

    protected function checkViolations($violations): void
    {
        if (0 !== \count($violations)) {
            $message = 'Invalid data. Violations:';
            // there are errors, now you can show them
            foreach ($violations as $k => $violation) {
                $message .= sprintf(' #%s $%s: %s | ', $k + 1, $violation->getPropertyPath(), $violation->getMessage());
            }
            $this->throwException($message, 400);
        }
    }

    protected function responseToEntity(ResponseInterface $response)
    {
        return $this->factoryFromResponse($response, function (ResponseInterface $response) {
            return $this->factoryEntity($this->responseEntityToData($response));
        });
    }

    protected function factoryEntity(array $data): TypeAnnotatedGeneratorInterface
    {
        $this->throwException('::factoryEntity must be implemented in the final class', 500);
    }

    protected function convertResponseToArray(ResponseInterface $response): array
    {
        $this->getLogger() && $this->getLogger()->debug('convert response into array');

        try {
            return $response->toArray(false);
        } catch (\Exception $e) {
            $this->getLogger() && $this->getLogger()->error('get content', [
                'throw' => $e->getMessage(),
                // 'content' => $response->getContent(false),
            ]);

            throw new RuntimeException('Unprocessable response', 500, $e);
        }
    }

    protected function responseColletionToData(ResponseInterface $response): array
    {
        return $this->convertResponseToArray($response);
    }

    protected function responseEntityToData(ResponseInterface $response): array
    {
        return $this->convertResponseToArray($response);
    }

    protected function responseToCollection(ResponseInterface $response): array
    {
        $list = [];
        foreach ($this->responseColletionToData($response) as $item) {
            $list[] = $this->factoryEntity($item);
        }

        return $list;
    }
}
