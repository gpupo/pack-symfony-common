<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Graphql;

use DateTimeImmutable;

trait EntityTypeTrait
{
    protected function dateTimeGetter(string $key): ?DateTimeImmutable
    {
        return $this->elementEmpty($key) ? null : new DateTimeImmutable($this->get($key));
    }

    protected function jsonEncodedGetter(string $key): ?DateTimeImmutable
    {
        return $this->elementEmpty($key) ? null : json_encode($this->get($key));
    }

    protected function entityFactoryGetter(string $key, string $className)
    {
        return $this->elementEmpty($key) ? null : new $className($this->get($key));
    }

    protected function collectionFactoryGetter(string $key, string $className, callable $lambda = null)
    {
        if ($this->elementEmpty($key)) {
            return null;
        }
        $list = [];
        foreach ($this->get($key) as $k => $v) {
            if ($lambda) {
                $v = $lambda($k, $v);
            }
            $list[] = new $className($v);
        }

        return $list;
    }
}
