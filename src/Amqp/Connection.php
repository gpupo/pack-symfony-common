<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Amqp;

use PhpAmqpLib\Connection\AMQPConnection;

class Connection extends AMQPConnection
{
    /**
     * @param string $data
     */
    public function write($data)
    {
        if (\defined('AMQP_LOG_OUTPUT')) {
            file_put_contents(AMQP_LOG_OUTPUT, $data."\n\n", FILE_APPEND | LOCK_EX);
        }

        parent::write($data);
    }
}
