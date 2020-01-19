<?php

namespace Wabue\Membership;

use Elgg\BadRequestException;
use Psr\Log\LogLevel;
use Wabue\Membership\Entities\ParticipationObject;

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

    public static function participationList(Array $participations) : string {
        $content = '';
        if (count($participations) == 0) {
            $content .= elgg_echo('membership:participations:none');
        } else {
            $participation_lists = '';
            $participation = $participations[0];
            foreach ($participation->getParticipationTypes() as $key => $label) {
                $participation_lists .= elgg_format_element('li', [], elgg_view_icon('check').$label);
            }
            $content .= elgg_format_element('ul', [], $participation_lists);
        }
        return $content;
    }
}

