<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/common-dev
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

namespace Gpupo\CommonDev\Tests\Traits;

use Gpupo\CommonDev\Tests\TestCaseAbstract;

/**
 * @coversNothing
 */
class TestCaseTraitTest extends TestCaseAbstract
{
    public function testGetConstant()
    {
        $this->assertSame('bar', $this->getConstant('FOO'));
        $this->assertFalse($this->getConstant('NOT_EXIST'));
    }

    public function testHasConstant()
    {
        $this->assertTrue($this->hasConstant('FOO'));
        $this->assertFalse($this->hasConstant('NOT_EXIST'));
    }
}
