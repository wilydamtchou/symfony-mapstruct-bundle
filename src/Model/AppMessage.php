<?php

namespace Willydamtchou\SymfonyMapstruct\Model;

use Afrikpay\SymfonyUtilities\Model\SystemMessage;

interface AppMessage extends SystemMessage
{
    public const MAPPER_FAILURE = [
        self::CODE => 506,
        self::MESSAGE => 'Cannot assign variable of class %s to class %s',
    ];
}
