<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace Gpupo\PackSymfonyCommon\GraphqlController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;
use Gpupo\PackSymfonyCommon\HttpClient\ApiClientAwareTrait;
use Gpupo\Common\Tools\Doctrine\DoctrineManagerAwareTrait;
use Gpupo\PackSymfonyCommon\Service\Remote\RemoteServiceAwareTrait;
use Gpupo\PackSymfonyCommon\Validator\ValidatorAwareTrait;

abstract class AbstractController
{
    use RemoteServiceAwareTrait;
    use DoctrineManagerAwareTrait;
    use ValidatorAwareTrait;
}