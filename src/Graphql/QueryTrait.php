<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

trait QueryTrait
{
    public function decorateToOnelineQuery(string $string): string
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    public function getRawGql(): string
    {
        return $this->decorateToOnelineQuery($this->__toString());
    }

    public function decorateToMultilineQuery(string $string): string
    {
        $vetor = [
            '{' => "{\n",
            '}' => "\n}",
            '(' => "(\n",
            // ')' => "\n)\n\r\t",
            ', ' => ",\n",
        ];

        return str_replace(array_keys($vetor), array_values($vetor), $this->decorateToOnelineQuery($string));
    }

    protected function factoryOperationName(string $prefix, string $fieldName): string
    {
        $name = ucfirst($fieldName);

        foreach (['_', '('] as $char) {
            if (false !== mb_strpos($name, $char)) {
                $name = implode('', array_map('ucfirst', explode($char, $name)));
            }
        }

        return sprintf('%s%s', $prefix, $name);
    }
}
