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

namespace Gpupo\PackSymfonyCommon\Amqp;

use Monolog\Formatter\JsonFormatter;
use Monolog\DateTimeImmutable;
use Gpupo\PackSymfonyCommon\Tools\DebugParameters;

class Formatter extends JsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $data = $this->normalize($record);
        if (isset($data['context']) && $data['context'] === []) {
            $data['context'] = new \stdClass;
        }
        if (isset($data['extra']) && $data['extra'] === []) {
            $data['extra'] = new \stdClass;
        }

        $data['datetime'] = $this->factoryDatetimeArray($data);
        $data['debugParameters'] = DebugParameters::getParameters();

        return $this->toJson($data, true) . ($this->appendNewline ? "\n" : '');
    }


    protected function factoryDatetimeArray($data): array
    {
        if (!array_key_exists('datetime', $data) || !$data['datetime'] instanceof DateTimeImmutable) {
            $data['datetime'] = new DateTimeImmutable(true);
        }
        $array = (array) $data['datetime']->getTimezone();
        $array['date'] = $data['datetime']->format("Y-m-d H:i:s.u");

        return $array;
    }
}
