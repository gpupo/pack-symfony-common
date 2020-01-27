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

namespace Gpupo\PackSymfonyCommon\Tests\Amqp;

use Gpupo\PackSymfonyCommon\Tests\TestCaseAbstract;
use Gpupo\PackSymfonyCommon\Amqp\Formatter;

/**
 * @coversNothing
 */
class FormatterTest extends TestCaseAbstract
{

    public function getRecord()
    {
        return [
        ];
    }

    public function testConstruct()
    {
        $formatter = new Formatter();
        $this->assertEquals(Formatter::BATCH_MODE_JSON, $formatter->getBatchMode());
        $this->assertEquals(true, $formatter->isAppendingNewlines());
        $formatter = new Formatter(Formatter::BATCH_MODE_NEWLINES, false);
        $this->assertEquals(Formatter::BATCH_MODE_NEWLINES, $formatter->getBatchMode());
        $this->assertEquals(false, $formatter->isAppendingNewlines());
    }

    public function testFormat()
    {
        $formatter = new Formatter();
        $record = $this->getRecord();
        $record['context'] = $record['extra'] = new \stdClass;

        $json = $formatter->format($record);
        $array = json_decode($json , true);

        foreach(['context', 'extra', 'datetime', 'debugParameters'] as $k) {
            $this->assertArrayHasKey($k, $array);
        }
    }

}
