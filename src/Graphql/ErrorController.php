<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorController
{
    public function __invoke(\Throwable $exception): JsonResponse
    {
        $response = new JsonResponse([
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ]);
        $response->setStatusCode(503);

        return $response;
    }
}
