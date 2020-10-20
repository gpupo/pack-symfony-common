<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Test;

use Gpupo\Common\Tools\Reflected;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

trait HelperTrait
{
    use VarDumperTestTrait;

    public function getPathResourcesDir()
    {
        if (\method_exists($this, 'bootKernel')) {
            return self::bootKernel()->getProjectDir().'/Resources';
        }

        return 'Resources';
    }

    protected function proxy($object)
    {
        return new Reflected($object);
    }
}
