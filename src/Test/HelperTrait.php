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

namespace Gpupo\PackSymfonyCommon\Test;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

use Gpupo\Common\Tools\Reflected;

trait HelperTrait
{
    use VarDumperTestTrait;
    
    public function getPathResourcesDir()
    {
        if (\method_exists($this, 'bootKernel')) {
            return self::bootKernel()->getProjectDir().'/Resources/';
        }

        return 'Resources/';
    }

    protected function proxy($object)
    {
        return new Reflected($object);
    }
}
