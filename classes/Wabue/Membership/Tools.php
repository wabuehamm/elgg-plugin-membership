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
use Zend\Mime\Part;

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

    /**
     * Create a list of participations
     * @param array $participationTypes The participation types to display
     * @param array $participations The participations to display
     * @param callable|null $linkGenerator A callable that gets the key of the participation and generates a link from it
     * @param bool $ignore_acl Ignore the ACL (e.g. when not on report views)
     * @return string The list of participations as a HTML UL list
     */
    public static function participationList(array $participationTypes, array $participations, callable $linkGenerator = null, bool $ignore_acl = false): string
    {
        $content = '';
        if (count($participations) == 0 or count($participationTypes) == 0) {
            $content .= elgg_echo('membership:participations:none');
        } else {
            $participation_lists = '';
            $all_participations = [];
            foreach ($participations as $participation) {
                foreach ($participation->getParticipationTypes($ignore_acl) as $key => $label) {
                    if (!array_key_exists($key, $all_participations)) {
                        $all_participations[$key] = $label;
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

    /**
     * Create a checkbox list of participations
     * @param string $part The name of the checkboxes
     * @param array $participationTypes The participation types to display
     * @param array $participations The participations to display
     * @return string The content as a buch of checkboxes
     */
    public static function participationUpdate(string $part, array $participationTypes, array $participations): string
    {
        return elgg_view(
            'input/checkboxes',
            [
                'name' => $part,
                'options' => array_flip($participationTypes),
                'value' => array_flip($participations),
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
     *
     * The calculation is as follows
     *
     * <the fixed number of away years before 2012 in the profile field away_years> + <count of seasons in the database> - <count of seasons the user has no participation in>
     *
     * If the user is a senior (the member is not active anymore, but is honored nonetheless), the years since the user became a senior is added
     * again to the number.
     *
     * @param ElggUser $user User object to calculate for
     * @param int|null $year The year to consider for
     * @return int Number of years
     */
    public static function calculateAwayYears(ElggUser $user, int $year = null)
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
            $yearsSinceSenior = $year ? $year : intval(date("Y")) - intval($seniorSince) + 1;
        }

        return $startAwayYears + $numberOfSeasons - count(elgg_get_entities($options)) - $yearsSinceSenior;
    }

    /**
     * Calculate the years the user was active.
     *
     * Calculation is as follows:
     *
     * Active years = <year to consider> - <year the user entered> - <years the user wasn't active> + 1 <for the first season>
     *
     * @param $user ElggUser user to count for
     * @param int|null $year The year to calculate for
     * @return int the number of years the user was active
     */

    public static function calculateActiveYears(ElggUser $user, int $year = null)
    {
        $since = intval($user->getProfileData('member_since'));
        $away = self::calculateAwayYears($user, $year);
        return ($year ? $year : intval(date("Y"))) - $since - $away + 1;
    }

    /**
     * Calculate the rows of a jubilees report.
     *
     * @param int $year The year to generate the report for
     * @return array The jubilees report
     */
    public static function generateJubileesReport(int $year)
    {
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        $report = [];
        foreach ($allUnbannedUsers as $user) {
            $activeYears = self::calculateActiveYears($user, $year);
            $report[$user->getDisplayName()] = [
                'member_since' => $user->getProfileData('member_since'),
                'away_years' => self::calculateAwayYears($user),
                'active_years' => $activeYears
            ];
        }
        uasort($report, function ($a, $b) {
            $aActive = $a['active_years'];
            $bActive = $b['active_years'];
            return $bActive - $aActive;
        });
        return $report;
    }

    /**
     * Calculate the rows of an anniversary report.
     *
     * @param int $year The year to generate the report for
     * @return array The anniversary report
     */
    public static function generateAnniversaryReport(int $year)
    {
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        $report = [];
        foreach ($allUnbannedUsers as $user) {
            $anniversary = date_create_from_format("d.m.Y", $user->getProfileData('anniversary'));
            $diff = date_diff($anniversary, date_create_from_format("d.m.Y", "01.01.$year"));
            if ($diff) {
                $report[$user->getDisplayName()] = [
                    'anniversary' => $user->getProfileData('anniversary'),
                    'years' => $diff->y + 1
                ];
            }
        }
        uasort($report, function ($a, $b) {
            $aActive = $a['years'];
            $bActive = $b['years'];
            return $bActive - $aActive;
        });
        return $report;
    }

    /**
     * Calculate the rows of an anniversary report.
     *
     * @param Season $season The season to generate the report for
     * @return array The anniversary report
     */
    public static function generateInsuranceReport(Season $season)
    {
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        $report = [
            $season->year => [
                'common' => 0,
                'teens' => 0,
                'board' => 0,
                'total' => count($allUnbannedUsers),
            ]
        ];
        foreach ($allUnbannedUsers as $user) {
            $userType = 'common';

            // Check if the member is a boardmember
            /* @var Participation[] $participations */
            $participations = $season->getDepartments()->getParticipations($user->getGUID());
            foreach ($participations as $participation) {
                if (in_array('vs', $participation->getParticipationTypes())) {
                    $userType = 'board';
                }
            }

            // Check if the member is a teen
            $birthday = date_create_from_format("Y-m-d", $user->getProfileData('birthday'));
            if (!$birthday) {
                $birthday = date_create_from_format("d.m.Y", $user->getProfileData('birthday'));
            }
            $diff = date_diff($birthday, date_create_from_format("d.m.Y", "01.01." . $season->year));
            if ($diff and $diff->y < 18) {
                $userType = 'teens';
            }

            $report[$season->year][$userType]++;
        }
        uasort($report, function ($a, $b) {
            $aActive = $a['years'];
            $bActive = $b['years'];
            return $bActive - $aActive;
        });
        return $report;
    }

    /**
     * Generate a report of all young (<18 years) members
     * @return array data of young members
     */
    public static function generateYoungMembersReport() {
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        $report = [];
        foreach ($allUnbannedUsers as $user) {
            // Check if the member is a teen
            $birthday = date_create_from_format("Y-m-d", $user->getProfileData('birthday'));
            if (!$birthday) {
                $birthday = date_create_from_format("d.m.Y", $user->getProfileData('birthday'));
            }
            $diff = date_diff($birthday, new \DateTime());
            if ($diff and $diff->y < 18) {
                array_push($report, [
                    $user->getDisplayName(),
                    $user->getProfileData("street"),
                    $user->getProfileData("zip"),
                    $user->getProfileData("city"),
                    $user->getProfileData("telephone"),
                    $user->getProfileData("mobile"),
                    $user->email,
                    $birthday->format('Y-m-d'),
                    $diff->y
                ]);
            }
        }

        return $report;
    }

    /**
     * Generate a report of all adult (>=18 years) members
     * @return array data of young members
     */
    public static function generateAdultMembersReport() {
        /** @var ElggUser[] $allUnbannedUsers */
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);
        $report = [];
        foreach ($allUnbannedUsers as $user) {
            // Check if the member is a teen
            $birthday = date_create_from_format("Y-m-d", $user->getProfileData('birthday'));
            if (!$birthday) {
                $birthday = date_create_from_format("d.m.Y", $user->getProfileData('birthday'));
            }
            $diff = date_diff($birthday, new \DateTime());
            if ($diff and $diff->y >= 18) {
                array_push($report, [
                    $user->getDisplayName(),
                    $user->getProfileData("street"),
                    $user->getProfileData("zip"),
                    $user->getProfileData("city"),
                    $user->getProfileData("telephone"),
                    $user->getProfileData("mobile"),
                    $user->email,
                    $birthday->format('Y-m-d'),
                    $diff->y
                ]);
            }
        }

        return $report;
    }

    public static function generateBirthdayJubileeReport(int $year) {
        $allUnbannedUsers = elgg_get_entities([
            'type' => 'user',
            'subtype' => 'user',
            'metadata_name_value_pairs' => [
                [
                    'name' => 'banned',
                    'value' => 'no',
                    'operand' => '='
                ]
            ],
            'limit' => '0'
        ]);

        $report = [];

        foreach ($allUnbannedUsers as $user) {
            $birthday = date_create_from_format("Y-m-d", $user->getProfileData('birthday'));
            if (!$birthday) {
                $birthday = date_create_from_format("d.m.Y", $user->getProfileData('birthday'));
            }
            $diff = date_diff($birthday, date_create_from_format("d.m.Y", "01.01." . $year));
            if ($diff and in_array($diff->y, [50, 60, 70, 75, 80, 85, 90, 95, 100, 105, 110])) {
                array_push($report, [
                    $user->getDisplayName(),
                    $user->getProfileData("street"),
                    $user->getProfileData("zip"),
                    $user->getProfileData("city"),
                    $user->getProfileData("telephone"),
                    $user->getProfileData("mobile"),
                    $user->email,
                    $birthday->format('Y-m-d'),
                    $diff->y
                ]);
            }
        }

        return $report;
    }
}

