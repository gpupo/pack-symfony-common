<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/pack-symfony-common created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\PackSymfonyCommon\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorAwareTrait
{
    protected ValidatorInterface $validator;

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }
}
