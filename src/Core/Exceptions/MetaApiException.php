<?php

namespace Vendor\LaravelMeta\Core\Exceptions;

use Exception;

class MetaApiException extends Exception
{
    protected array $metaErrorData;

    public function __construct(string $message, int $code, array $metaErrorData = [])
    {
        parent::__construct($message, $code);
        $this->metaErrorData = $metaErrorData;
    }

    public function getMetaErrorData(): array
    {
        return $this->metaErrorData;
    }
}
