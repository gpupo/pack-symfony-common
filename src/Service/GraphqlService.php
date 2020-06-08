<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Service;

use Gpupo\PackSymfonyCommon\Graphql\Mutation;
use Gpupo\PackSymfonyCommon\Graphql\Query;
use GraphQL\Client;
use GraphQL\RawObject;
use GraphQL\Results;
use GraphQL\Variable;
use InvalidArgumentException;

class GraphqlService extends AbstractService
{
    protected Client $client;

    public function __construct(string $endpoint, string $jwt = null)
    {
        $authorizationHeaders = [];

        if (!empty($jwt)) {
            $authorizationHeaders['Authorization'] = sprintf('Bearer %s', $jwt);
        }

        $this->client = new Client($endpoint, $authorizationHeaders);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function query(string $query, bool $resultsAsArray = true, array $variables = []): Results
    {
        return $this->getClient()->runRawQuery($query, $resultsAsArray, $variables);
    }

    public function factoryQuery(string $fieldName): Query
    {
        $query = new Query($fieldName);

        return $query;
    }

    public function factoryRawObject(string $objectString): RawObject
    {
        return new RawObject($objectString);
    }

    public function factoryVariable(string $name, string $type, bool $isRequired = false, $defaultValue = null): Variable
    {
        return new Variable($name, $type, $isRequired, $defaultValue);
    }

    public function runQuery($query, bool $resultsAsArray = false, array $variables = []): Results
    {
        return $this->getClient()->runQuery($query, $resultsAsArray, $variables);
    }

    public function runOneMutation($query, array $variables = []): array
    {
        $results = $this->runQuery($query, true, $variables);
        $data = $results->getData();

        return current($data);
    }

    public function factoryMutation(string $fieldName): Mutation
    {
        $mutation = new Mutation($fieldName);

        return $mutation;
    }

    public function runQueryFromFile(string $filename, array $variables = [], bool $resultsAsArray = true): Results
    {
        return $this->query($this->readQueryFile($filename), $resultsAsArray, $variables);
    }

    protected function readQueryFile(string $filename): string
    {
        if (false === mb_strpos($filename, '.graphql')) {
            throw new InvalidArgumentException('A extensão do arquivo precisa ser .graphql');
        }

        return file_get_contents(sprintf('%s/queries/%s', APP_SRC_DIRECTORY, $filename));
    }
}
