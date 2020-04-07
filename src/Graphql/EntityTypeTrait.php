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
    public function toPayload(): array
    {
        $list = [];

        foreach ($this->all() as $key => $value) {
            if ($value instanceof TypeInterface) {
                $list[$key] = $value->toPayload();
            } else {
                $list[$key] = $this->normalizeToPayload($key, $value);
            }
        }

        return $list;
    }

    protected function dateTimeGetter(string $key): ?DateTimeImmutable
    {
        return $this->elementEmpty($key) ? null : new DateTimeImmutable($this->get($key));
    }

    protected function jsonEncodedGetter(string $key): ?string
    {
        return $this->elementEmpty($key) ? null : json_encode($this->get($key));
    }

    protected function entityFactoryGetter(string $key, string $className)
    {
        if ($this->elementEmpty($key)) {
            return null;
        }

        $value = $this->get($key);

        if ($value instanceof $className) {
            return $value;
        }

        return new $className($value);
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
