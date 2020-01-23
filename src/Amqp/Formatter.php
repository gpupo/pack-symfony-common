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

class Formatter extends JsonFormatter
{

    /**
     * {@inheritdoc}
     *
     * @suppress PhanTypeComparisonToArray
     */
    public function format(array $record): string
    {
        $normalized = $this->normalize($record);
        if (isset($normalized['context']) && $normalized['context'] === []) {
            $normalized['context'] = new \stdClass;
        }
        if (isset($normalized['extra']) && $normalized['extra'] === []) {
            $normalized['extra'] = new \stdClass;
        }

        $normalized['datetime'] = $this->normalizeDatetime($normalized['datetime']);

        return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
    }

//"datetime":{"date":"2020-01-23 18:20:02.522569","timezone_type":3,"timezone":"America/Sao_Paulo"},
    protected function normalizeDatetime(\DateTimeInterface $date)
    {
        $array = (array) $date->getTimezone();
        $array['date'] = $date->format("Y-m-d H:i:s.u");

        return $array;
    }
}
