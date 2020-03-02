<?php

namespace Wabue\Membership;

use Elgg\BadRequestException;
use ElggUser;
use Psr\Log\LogLevel;
use Wabue\Membership\Entities\Participation;
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
            elgg_log("BadRequestException: Assertion failed." . $tmpException->getTraceAsString(), LogLevel::ERROR);
            throw $tmpException;
        }
    }

    public static function participationList(Array $participationTypes, Array $participations, callable $linkGenerator = null): string
    {
        $content = '';
        if (count($participations) == 0) {
            $content .= elgg_echo('membership:participations:none');
        } else {
            $participation_lists = '';
            $participation = $participations[0];
            foreach ($participation->getParticipationTypes() as $key) {
                $label = $participationTypes[$key];
                $link = null;
                if ($linkGenerator) {
                    $link = call_user_func($linkGenerator, $key);
                }
                if ($link) {
                    $participation_lists .= elgg_format_element(
                        'a',
                        [
                            'href' => $link,
                        ],
                        elgg_format_element(
                            'li',
                            [],
                            elgg_view_icon('check') . ' ' . $label
                        )
                    );
                } else {
                    $participation_lists .= elgg_format_element('li', [], elgg_view_icon('check') . ' ' . $label);
                }

            }
            $content .= elgg_format_element(
                'ul',
                ['class' => 'elgg-input-checkboxes elgg-horizontal'],
                $participation_lists
            );
        }
        return $content;
    }

    public static function participationUpdate(string $part, Array $participationTypes, Array $participations): string
    {
        return elgg_view(
            'input/checkboxes',
            [
                'name' => $part,
                'options' => $participationTypes,
                'value' => $participations,
                'align' => 'horizontal',
            ]
        );
    }

    /**
     * Generates a multidimensional report array like this:
     *
     * username => [
     *   _userInfo => Array with relevant user profile fields,
     *   participationObject => Array of participation keys the user participated in for the participation object
     * ]
     * @param ParticipationObject[] $participationObjects
     * @param string[] $participationTypes
     * @return array The report
     * @throws BadRequestException Wrong data structure
     */
    public static function generateReport(array $participationObjects, array $participationTypes): array
    {
        $report = [];
        $reportProfileFields = elgg_get_plugin_setting("reportProfileFields", "membership", []);

        foreach ($participationObjects as $participationObject) {
            /** @var Participation[] $participations */
            $participations = $participationObject->getParticipations();
            foreach ($participations as $participation) {
                $reportParticipations = [];

                foreach ($participationTypes as $filterParticipationType) {
                    if (in_array($filterParticipationType, array_keys($participation->getParticipationTypes()))) {
                        array_push($reportParticipations, $filterParticipationType);
                    }
                }

                if (count($reportParticipations) > 0) {
                    /** @var ElggUser $owner */
                    $owner = $participation->getOwnerEntity();
                    self::assert(!is_null($owner));
                    self::assert($owner instanceof ElggUser);
                    if (!array_key_exists($owner->username, $report)) {
                        $userInfo = [
                            "name" => $owner->getDisplayName(),
                            "username" => $owner->username,
                            "email" => $owner->email,
                        ];

                        foreach ($reportProfileFields as $reportProfileField) {
                            $userInfo[$reportProfileField] = $owner->getProfileData($reportProfileField);
                        }

                        $report[$owner->username] = [
                            "_userInfo" => $userInfo
                        ];
                    }

                    $report[$owner->username][$participationObject->getDisplayName()] = $reportParticipations;
                }
            }
        }

        return $report;
    }
}

