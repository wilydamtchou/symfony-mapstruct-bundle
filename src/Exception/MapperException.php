<?php

namespace Willydamtchou\SymfonyMapstruct\Exception;

use Willydamtchou\SymfonyUtilities\Exception\GeneralException;
use Willydamtchou\SymfonyMapstruct\Model\AppMessage;

class MapperException extends GeneralException
{
    protected string $exceptionCode = '15';
    protected ?string $userMessage = 'System data configuration error';

    /**
     * @param string $variableClass
     * @param string $mapperClass
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $variableClass,
        string $mapperClass,
        string $message = AppMessage::MAPPER_FAILURE[self::MESSAGE],
        int $code = AppMessage::MAPPER_FAILURE[self::CODE],
        \Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                $message,
                $variableClass,
                $mapperClass
            ),
            $code,
            $previous
        );
    }
}
