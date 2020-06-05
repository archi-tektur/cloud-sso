<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use InvalidArgumentException;

class EmailAlreadyExistException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Email already registered.');
    }
}