<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Tests\Tools;

use Gpupo\PackSymfonyCommon\Tests\TestCaseAbstract;
use Gpupo\PackSymfonyCommon\Tools\DebugParameters;

/**
 * @coversNothing
 */
class DebugParametersTest extends TestCaseAbstract
{
    public function testGetParameters()
    {
        $parameters = DebugParameters::getParameters();
        $this->assertSame(PHP_VERSION, $parameters['phpversion']);
    }
}
