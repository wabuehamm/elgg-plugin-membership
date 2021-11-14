<?php

namespace Wabue\Membership;

/**
 * Class ACL
 *
 * The following routes exist under the report basepath:
 *
 * /{season_guid} => Show all participations of all participation objects in the season
 * /{season_guid}/{participation_object_guids} => Show all participations of the selected participation objects
 *   (0=departments) in the season
 * /{season_guid}/{participation_object_guids}/{participation_types} => Show only the selected participation types
 *   of the selected participation objects (0=departments) in the season
 *
 * The ACL DSL is based on that in this form:
 *
 * <role or username>:/season_guid/participation_object_guids or 0/participation_types
 *
 * Every part can hold a "*" to allow ALL objects of the respective parts and can be a list of items separated by ,
 *
 * Examples (blanks just added for escaping):
 *
 * example.user:/ * / * / * => Access to every report
 * example.user:/ 12345 / * / * => Access to every report in the given season
 * example.user:/ * / 0 / * => Access to all department and season reports
 * example.user:/ * / 0,34567 / * => Access all department and season reports and reports of one specific production
 * example.user:/ * / 0 / bb => Access to all "Bühnenbau" reports
 *
 * Additionally, the following season reports are available and can be used in the last section:
 *
 * * _jubilee: Jubilee report
 * * _anniversary: Anniversary report
 * * _insurance: Insurance report
 * * _all: All participants of the season
 *
 * Roles should be at the top of the DSL text and are configured like this:
 *
 * rolename=<username>,<username>,<username>
 */
class Acl {
    private static $_singleton = null;

    /** @var array rules */
    private $rules = [];
    /** @var array users to roles */
    private $userToRoles = [];

    public function __construct()
    {
        // Build the acl

        $dsl = elgg_get_plugin_setting('acl', 'membership', '');

        if ($dsl == '') {
            return;
        }

        $this->_evalAcl($dsl);
    }

    public static function factory(): ?Acl
    {
        if (self::$_singleton == null) {
            self::$_singleton = new Acl();
        }
        return self::$_singleton;
    }

    /**
     * Get all applicable roles for the given user
     * @param string $user
     * @return array Rules for the user
     */
    public function getRulesForUser(string $user): array
    {
        $roles = $this->userToRoles[$user];

        $rules = [];

        if (array_key_exists('*', $this->rules)) {
            $rules = array_replace_recursive($rules, $this->rules['*']);
        }

        if (array_key_exists($user, $this->rules)) {
            $rules = array_replace_recursive($rules, $this->rules[$user]);
        }

        foreach ($roles as $role) {
            if (array_key_exists($role, $this->rules)) {
                $rules = array_replace_recursive($rules, $this->rules[$role]);
            }
        }

        return $rules;
    }

    public function getAllowedSeasons(string $user): array
    {
        return array_keys($this->getRulesForUser($user));
    }

    /**
     * Evaluate the ACL DSL and build this->rules, this->roles and this->userToRole
     * @param $dsl
     */
    private function _evalAcl($dsl): void
    {
        $acl = preg_split('/\r\n/', $dsl);

        foreach ($acl as $line) {

            $matches = [];

            if (preg_match_all('/^(?<rolename>.+)=(?<users>.+)$/', $line, $matches) > 0) {
                $users = preg_split('/,/', $matches['users'][0]);
                $this->roles[$matches['rolename'][0]] = $users;
                foreach ($users as $user) {
                    if (!array_key_exists($user, $this->userToRoles)) {
                        $this->userToRoles[$user] = [];
                    }
                    array_push($this->userToRoles[$user], $matches['rolename'][0]);
                }
            }

            if (preg_match_all('#^(?<target>[^:]+):/(?<season_guid>[0-9*,]+)/(?<participation_object_guids>[0-9*,]+)/(?<participation_types>[a-z_,*]+)#', $line, $matches) > 0) {

                $targets = preg_split('/,/', $matches['target'][0]);
                $ruleSeasonGuids = preg_split('/,/', $matches['season_guid'][0]);
                $ruleParticipationObjectGuids = preg_split('/,/', $matches['participation_object_guids'][0]);
                $ruleParticipationTypes = preg_split('/,/', $matches['participation_types'][0]);

                foreach ($targets as $target) {
                    if (!array_key_exists($target, $this->rules)) {
                        $this->rules[$target] = [];
                    }

                    foreach ($ruleSeasonGuids as $ruleSeasonGuid) {
                        if (!array_key_exists($ruleSeasonGuid, $this->rules[$target])) {
                            $this->rules[$target][$ruleSeasonGuid] = [];
                        }

                        foreach ($ruleParticipationObjectGuids as $ruleParticipationObjectGuid) {
                            if (!array_key_exists($ruleParticipationObjectGuid, $this->rules[$target][$ruleSeasonGuid])) {
                                $this->rules[$target][$ruleSeasonGuid][$ruleParticipationObjectGuid] = [];
                            }
                            foreach ($ruleParticipationTypes as $ruleParticipationType) {
                                if (!in_array($ruleParticipationType, $this->rules[$target][$ruleSeasonGuid][$ruleParticipationObjectGuid])) {
                                    array_push($this->rules[$target][$ruleSeasonGuid][$ruleParticipationObjectGuid], $ruleParticipationType);
                                }
                            }
                        }
                    }
                }

            }

        }
    }

    public function getSeasonRules(string $username, int $season_guid): array
    {
        $rules = $this->getRulesForUser($username);

        $season_rules = [];

        if (array_key_exists('*', $rules)) {
            $season_rules = $rules['*'];
        } elseif (array_key_exists($season_guid, $rules)) {
            $season_rules = $rules[$season_guid];
        }

        return $season_rules;
    }

    public function getAllowedDepartments(string $username, int $season_guid): array
    {
        $season_rules = $this->getSeasonRules($username, $season_guid);

        if (array_key_exists("*", $season_rules)) {
            return $season_rules["*"];
        } elseif (array_key_exists("0", $season_rules)) {
            return $season_rules["0"];
        }
        return [];
    }

    public function getAllowedProductions(string $username, int $season_guid): array
    {
        $season_rules = $this->getSeasonRules($username, $season_guid);

        if (array_key_exists("0", $season_rules)) {
            unset($season_rules["0"]);
        }
        return array_keys($season_rules);
    }

    public function getAllowedParticipations(string $username, int $season_guid, int $area): array
    {
        $season_rules = $this->getSeasonRules($username, $season_guid);

        if (array_key_exists("*", $season_rules)) {
            return $season_rules["*"];
        } elseif (array_key_exists($area, $season_rules)) {
            return $season_rules[$area];
        }
        return [];
    }

    public function isParticipationAllowed(string $username, int $season_guid, int $area, string $participation): bool
    {
        $area_rules = $this->getAllowedParticipations($username, $season_guid, $area);
        return in_array("*", $area_rules) or in_array($participation, $area_rules);
    }

    public function canAccess(string $username, array $season_guids, array $areas, array $participations): bool
    {
        foreach ($season_guids as $season_guid) {
            $season_rules = $this->getSeasonRules($username, $season_guid);
            if (count($areas) == 0 and count($season_rules) > 0) {
                return true;
            } elseif (count($areas) > 0) {
                if (in_array("0", $areas)) {
                    $departments = $this->getAllowedDepartments($username, $season_guid);
                    if (count($participations) == 0 and (count($departments) == 0)) {
                        return false;
                    } elseif (count($participations) > 0) {
                        foreach ($participations as $participation) {
                            if (!$this->isParticipationAllowed($username, $season_guid, "0", $participation)) {
                                return false;
                            }
                        }
                    }
                }
                foreach (array_filter($areas, function ($area) { return $area != "0"; }) as $area) {
                    $productions = $this->getAllowedProductions($username, $season_guid);
                    if (count($participations) == 0 and array_key_exists($area, $productions)) {
                        return true;
                    } elseif (count($participations) > 0) {
                        foreach ($participations as $participation) {
                            if (!$this->isParticipationAllowed($username, $season_guid, $area, $participation)) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
