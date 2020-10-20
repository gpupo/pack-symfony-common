<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Tests\Amqp;

use Gpupo\PackSymfonyCommon\Amqp\Formatter;
use Gpupo\PackSymfonyCommon\Tests\TestCaseAbstract;

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
        $this->assertSame(Formatter::BATCH_MODE_JSON, $formatter->getBatchMode());
        $this->assertTrue($formatter->isAppendingNewlines());
        $formatter = new Formatter(Formatter::BATCH_MODE_NEWLINES, false);
        $this->assertSame(Formatter::BATCH_MODE_NEWLINES, $formatter->getBatchMode());
        $this->assertFalse($formatter->isAppendingNewlines());
    }

    public function testFormat()
    {
        $formatter = new Formatter();
        $record = $this->getRecord();
        $record['context'] = $record['extra'] = new \stdClass();

        $json = $formatter->format($record);
        $array = json_decode($json, true);

        foreach (['context', 'extra', 'datetime', 'debugParameters'] as $k) {
            $this->assertArrayHasKey($k, $array);
        }
    }
}
