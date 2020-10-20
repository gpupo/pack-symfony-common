<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Test;

use Gpupo\CommonSdk\Traits\ResourcesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\VarDumper\Dumper\CliDumper;

abstract class AbstractServiceTestCase extends WebTestCase
{
    use ResourcesTrait;
    use HelperTrait;

    protected $dynamicKernel;

    protected function setUp(): void
    {
        $this->dynamicKernel = self::bootKernel();
        $casters = [
            \DateTimeInterface::class => static function (\DateTimeInterface $date, array $a, Stub $stub): array {
                $stub->class = 'DateTime';

                return ['date' => $date->format('d/m/Y')];
            },
        ];

        $flags = CliDumper::DUMP_LIGHT_ARRAY | CliDumper::DUMP_COMMA_SEPARATOR;

        // this configures the casters & flags to use for all the tests in this class.
        // If you need custom configurations per test rather than for the whole class,
        // call this setUpVarDumper() method from those tests instead.
        $this->setUpVarDumper($casters, $flags);
    }

    public function getKernel()
    {
        return $this->dynamicKernel;
    }

    protected function getSpecialContainer()
    {
        return self::$container;
    }
}
