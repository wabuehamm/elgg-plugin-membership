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
 * <role or username>:/season_guid/participation_object_guids or 0 for departments/participation_types
 *
 * Every part can hold a "*" to allow ALL objects of the respective parts and can be a list of items separated by ,
 *
 * Examples (blanks just added for escaping):
 *
 * example.user:/ * / * / * => Access to every report
 * example.user:/ 12345 / * / * => Access to every report in the given season
 * example.user:/ * / 0 / * => Access to all department and season reports
 * example.user:/ * / 0,34567 / * => Access all department and season reports and reports of one specific production
 * example.user:/ * / 0 / bb => Access to all reports for the specific partition with the key "bb"
 *
 * Additionally, the following season reports are available and can be used as partition types for the departments (0)
 * partition object:
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
class Acl
{
    /** @var string ACL Role definition */
    const ROLE_DEF = '/^(?<rolename>.+)=(?<users>.+)$/';
    /** @var string ACL Rule definition */
    const RULE_DEF = '#^(?<target>[^:]+):/(?<season_guid>[0-9*,]+)/(?<participation_object_guids>[0-9*,]+)/(?<participation_types>[a-z_,*]+)#';


    /** @var ACL Singleton cache */
    private static $_singleton = null;

    /** @var array rules The rules gathered from the ACL dsl */
    private $rules = [];
    /** @var array A mapping of user to their gathered roles */
    private $userToRoles = [];

    /**
     * Create the object and evaluate the ACL DSL
     */
    public function __construct()
    {
        $dsl = elgg_get_plugin_setting('acl', 'membership', '');

        if ($dsl == '') {
            return;
        }

        $this->_evalAcl($dsl);
    }

    /**
     * Create or return a previously
     * @return Acl
     */
    public static function factory(): Acl
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

    /**
     * Get the allowed season GUIDs for the given user
     * @param string $user Username of the user
     * @return array A list of allowed season guids
     */
    public function getAllowedSeasons(string $user): array
    {
        return array_keys($this->getRulesForUser($user));
    }

    /**
     * Get the allowed rules for the departments participation type for a user in a specific question
     * @param string $username User in question
     * @param int $season_guid GUID of the season in question
     * @return array Associative array of department rules
     */
    public function getAllowedDepartments(string $username, int $season_guid): array
    {
        $season_rules = $this->_getSeasonRules($username, $season_guid);

        if (array_key_exists("*", $season_rules)) {
            return $season_rules["*"];
        } elseif (array_key_exists("0", $season_rules)) {
            return $season_rules["0"];
        }
        return [];
    }

    /**
     * Get the allowed production GUIDs for a specific user in a season
     * @param string $username Username of a user
     * @param int $season_guid GUID of the season in question
     * @return array Production GUIDs
     */
    public function getAllowedProductions(string $username, int $season_guid): array
    {
        $season_rules = $this->_getSeasonRules($username, $season_guid);

        if (array_key_exists("0", $season_rules)) {
            unset($season_rules["0"]);
        }
        return array_keys($season_rules);
    }

    /**
     * Get the allowed participations for a participation type in a season for a user
     * @param string $username Username of the user
     * @param int $season_guid GUID of the season in question
     * @param int $participation_type_guid GUID of the participation type (or 0 for departments)
     * @return array List of allowed participations
     */
    public function getAllowedParticipations(string $username, int $season_guid, int $participation_type_guid): array
    {
        $season_rules = $this->_getSeasonRules($username, $season_guid);

        if (array_key_exists("*", $season_rules)) {
            return $season_rules["*"];
        } elseif (array_key_exists($participation_type_guid, $season_rules)) {
            return $season_rules[$participation_type_guid];
        }
        return [];
    }

    /**
     * Check if a specific participation (report) is allowed for a participation object in a  given season for a user
     * @param string $username Username of the user
     * @param int $season_guid GUID of the season in question
     * @param int $participation_type_guid GUID of the participation type (or 0 for departments)
     * @param string $participation Participation in question
     * @return bool Wether the user can view the report
     */
    public function isParticipationAllowed(
        string $username,
        int $season_guid,
        int $participation_type_guid,
        string $participation
    ): bool
    {
        $area_rules = $this->getAllowedParticipations($username, $season_guid, $participation_type_guid);
        return in_array("*", $area_rules) or in_array($participation, $area_rules);
    }

    /**
     * Check wether a user can access the reports for specific seasons, participation types and participations
     * @param string $username Username of user
     * @param array $season_guids Array of season guids to check
     * @param array $participation_type_guids Array of participation type guids (or 0 for departments)
     * @param array $participations Array of participations to check
     * @return bool Wether the user can access the reports
     */
    public function canAccess(
        string $username,
        array $season_guids,
        array $participation_type_guids,
        array $participations
    ): bool
    {
        foreach ($season_guids as $season_guid) {
            $season_rules = $this->_getSeasonRules($username, $season_guid);
            if (count($participation_type_guids) == 0 and count($season_rules) > 0) {
                return true;
            } elseif (count($participation_type_guids) > 0) {
                if (in_array("0", $participation_type_guids)) {
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
                foreach (array_filter($participation_type_guids, function ($area) {
                    return $area != "0";
                }) as $area) {
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

    /**
     * Evaluate the ACL DSL and build this->rules, and this->userToRole
     * @param $dsl
     */
    private function _evalAcl($dsl): void
    {
        $acl = preg_split('/\r\n/', $dsl);

        foreach ($acl as $line) {

            $matches = [];

            // Check role definitions
            if (preg_match_all(self::ROLE_DEF, $line, $matches) > 0) {
                $users = preg_split('/,/', $matches['users'][0]);
                $this->roles[$matches['rolename'][0]] = $users;
                foreach ($users as $user) {
                    if (!array_key_exists($user, $this->userToRoles)) {
                        $this->userToRoles[$user] = [];
                    }
                    array_push($this->userToRoles[$user], $matches['rolename'][0]);
                }
            }

            // Check a rule definition
            if (preg_match_all(self::RULE_DEF, $line, $matches) > 0) {
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

    /**
     * Get the rules for a specific season and user
     * @param string $username Username of the user
     * @param int $season_guid The GUID of the requested season
     * @return array Associative array of rules
     */
    private function _getSeasonRules(string $username, int $season_guid): array
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
}
