<?php

namespace Wabue\Membership;

use Elgg\BadRequestException;
use Elgg\Cache\SimpleCache;
use Elgg\Database\QueryBuilder;
use ElggUser;
use Psr\Log\LogLevel;
use Wabue\Membership\Entities\Participation;
use Wabue\Membership\Entities\ParticipationObject;
use Wabue\Membership\Entities\Season;

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

    public static function participationList(array $participationTypes, array $participations, callable $linkGenerator = null): string
    {
        $content = '';
        if (count($participations) == 0) {
            $content .= elgg_echo('membership:participations:none');
        } else {
            $participation_lists = '';
            $all_participations = [];
            foreach ($participations as $participation) {
                foreach ($participation->getParticipationTypes() as $key) {
                    if (!array_key_exists($key, $all_participations)) {
                        $all_participations[$key] = $participationTypes[$key];
                    }
                }
            }
            foreach ($all_participations as $key => $label) {
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

    public static function participationUpdate(string $part, array $participationTypes, array $participations): string
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

                if (count($participationTypes) == 0) {
                    foreach ($participation->getParticipationTypes() as $participationType) {
                        array_push($reportParticipations, $participationType);
                    }
                } else {
                    foreach ($participationTypes as $filterParticipationType) {
                        if (in_array($filterParticipationType, array_values($participation->getParticipationTypes()))) {
                            array_push($reportParticipations, $filterParticipationType);
                        }
                    }
                }

                if (count($reportParticipations) > 0) {
                    /** @var ElggUser $owner */
                    $owner = $participation->getOwnerEntity();
                    self::assert(!is_null($owner));
                    self::assert($owner instanceof ElggUser);
                    if (!array_key_exists($owner->username, $report)) {
                        $username_parts = preg_split("/\./", $owner->username);
                        $name = $username_parts[count($username_parts) - 1];
                        $givenName = join(' ', array_slice($username_parts, 0, count($username_parts) - 1));
                        $userInfo = [
                            "displayname" => $owner->getDisplayName(),
                            "name" => ucfirst($name),
                            "givenname" => ucfirst($givenName),
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

        usort($report, function ($a, $b) {
            return strcmp($a['_userInfo']['name'], $b['_userInfo']['name']);
        });

        return $report;
    }

    /**
     * Get all seasons in the database
     * @return Season[]
     */
    public static function getAllSeasons(): array
    {
        return elgg_get_entities([
            'type' => 'object',
            'subtype' => 'season',
            'limit' => 999,
        ]);
    }

    /**
     * Get a season by the year it took place
     * @param string $year The season's year
     * @return Season
     */
    public static function getSeasonByYear(string $year)
    {
        $seasons = elgg_get_entities([
            'type' => 'object',
            'subtype' => 'season',
            'metadata_name_value_pairs' => [
                'name' => 'year',
                'value' => $year
            ]
        ]);

        if (count($seasons) != 1) {
            return null;
        }

        return $seasons[0];
    }

    /**
     * Return a user by it's display name
     * @param string $displayName The display name to search for
     * @return ElggUser|null User matching the display name
     */
    public static function getUserByDisplayname(string $displayName)
    {
        $users = elgg_get_entities([
            'type' => 'user',
            'metadata_name_value_pairs' => [
                'name' => 'name',
                'value' => $displayName
            ]
        ]);

        if (count($users) == 1) {
            return $users[0];
        }

        return null;
    }

    /**
     * Get a user by a combination of birthday, street and zip
     *
     * @param string $birthday The birthday of the user
     * @param string $street The street address of the user
     * @param string $zip The zip of the user
     * @return ElggUser|null Matching user
     */
    public static function getUserByPrivateData(string $birthday, string $street, string $zip)
    {
        $users = elgg_get_entities([
            'type' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'birthday',
                    'value' => $birthday
                ],
                [
                    'name' => 'street',
                    'value' => $street
                ],
                [
                    'name' => 'zip',
                    'value' => $zip
                ]
            ]
        ]);

        if (count($users) == 1) {
            return $users[0];
        }

        return null;
    }

    /**
     * Calculate the number of years the user hasn't participated in a season
     * @param ElggUser $user User object to calculate for
     * @return int Number of years
     */
    public static function calculateAwayYears($user)
    {
        $startAwayYears = $user->getProfileData('away_years') ? $user->getProfileData('away_years') : 0;

        $numberOfSeasons = elgg_count_entities([
            'type' => 'object',
            'subtype' => 'season',
            'limit' => 999,
            'search_name_value_pairs' => [
                [
                    'name' => 'year',
                    'value' => $user->getProfileData('member_since'),
                    'operand' => '>=',
                    'case_sensitive' => false,
                ],
            ]
        ]);

        $options = [
            'type' => 'object',
            'subtype' => 'participation',
            'limit' => 999,
            'owner_guid' => $user->getGUID(),
            'group_by' => [
                function (QueryBuilder $qb, $main_alias) {
                    return "$main_alias.container_guid";
                }
            ]
        ];

        // Calculate years since the member is considered a senior to substract from the inactive years

        $seniorSince = $user->getProfileData('senior_since');
        $yearsSinceSenior = 0;

        if ($seniorSince != null) {
            $yearsSinceSenior = intval(date("Y")) - intval($seniorSince);
        }

        return $startAwayYears + $numberOfSeasons - count(elgg_get_entities($options)) - $yearsSinceSenior;
    }

    public static function calculateActiveYears($user)
    {
        $since = intval($user->getProfileData('member_since'));
        $away = self::calculateAwayYears($user);
        return intval(date("Y")) - $since - $away + 1;
    }
}

