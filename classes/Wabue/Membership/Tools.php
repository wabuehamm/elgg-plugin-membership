<?php

namespace Wabue\Membership;

use Elgg\BadRequestException;
use Psr\Log\LogLevel;

class Tools
{
    /**
     * Validate a given assertion and throw a BadRequestException if it's not
     * valid. Used for basic sanity and security checks, which should be valid
     * on normal circumstances
     * @param bool $assertion The assertion to test
     * @param string $message An optional error message
     * @param int $code An optional error code
     * @throws BadRequestException thrown if the assertion is not valid, handled
     *   by Elgg's view processor
     */
    public static function assert(bool $assertion, string $message = '', int $code = 400)
    {
        if (!$assertion) {
            $tmpException = new BadRequestException($message, $code);
            elgg_log("BadRequestException: Assertion failed.".$tmpException->getTraceAsString(), LogLevel::ERROR);
            throw $tmpException;
        }
    }
}

