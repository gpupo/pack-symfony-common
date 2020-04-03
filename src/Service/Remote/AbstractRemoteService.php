<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Service\Remote;

use Gpupo\PackSymfonyCommon\Graphql\ResponseHandlerTrait;
use Gpupo\PackSymfonyCommon\Graphql\TypeAnnotatedGeneratorInterface;
use Gpupo\PackSymfonyCommon\HttpClient\ApiClientAwareTrait;
use Gpupo\PackSymfonyCommon\HttpClient\ApiClientInterface;
use Gpupo\PackSymfonyCommon\Service\AbstractService;
use Gpupo\PackSymfonyCommon\Validator\ValidatorAwareTrait;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractRemoteService extends AbstractService
{
    use ApiClientAwareTrait;
    use ValidatorAwareTrait;
    use ResponseHandlerTrait;

    protected string $domain;

    public function __construct(ApiClientInterface $apiClient, ValidatorInterface $validator)
    {
        $this->setApiClient($apiClient);
        $this->setValidator($validator);
    }

    public function findById(string $id): ?TypeAnnotatedGeneratorInterface
    {
        $this->checkViolations(
            $this->getValidator()
                ->validate($id, [
                new Length(['min' => 18]),
                new NotBlank(),
            ])
        );

        return $this->findByPath(sprintf('/%s/%s', $this->getDomain(), $id));
    }

    public function findByPath(string $path): ?TypeAnnotatedGeneratorInterface
    {
        return $this->responseToEntity($this->getApiClient()->getRequest($path));
    }

    public function findAll(): array
    {
        $path = sprintf('/%s', $this->getDomain());
        $response = $this->getApiClient()->getRequest($path);

        return $this->responseToCollection($response, function (ResponseInterface $response) {
            return $this->responseToCollection($response);
        });
    }

    protected function getDomain(): string
    {
        return $this->domain;
    }
}
