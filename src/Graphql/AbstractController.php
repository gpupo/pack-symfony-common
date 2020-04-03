<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use Gpupo\Common\Tools\Doctrine\DoctrineManagerAwareTrait;
use Gpupo\PackSymfonyCommon\Service\Remote\RemoteServiceAwareTrait;
use Gpupo\PackSymfonyCommon\Validator\ValidatorAwareTrait;

abstract class AbstractController
{
    use RemoteServiceAwareTrait;
    use DoctrineManagerAwareTrait;
    use ValidatorAwareTrait;
    use ResponseHandlerTrait;
}
