<?php

namespace Polopolaw\ProxyManager;

/**
 * Исключение для Proxy Manager Client
 */
class ProxyManagerException extends \Exception
{
    private $errorCode;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $code;
    }

    /**
     * Возвращает код ошибки
     * 
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
