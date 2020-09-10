<?php

namespace Wabue\Membership;

use Elgg\HttpException;
use Elgg\Request;
use ElggUser;

/**
 * Class ReportGatekeeper
 *
 * The report gatekeeper does intense ACL checkings for showing the available reports.
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
 * example.user:/ * / 0 / * => Access to all department reports
 * example.user:/ * / 0,34567 / * => Access all department reports and reports of one specific production
 * example.user:/ * / 0 / bb => Access to all "Bühnenbau" reports
 *
 * Roles should be at the top of the DSL text and are configured like this:
 *
 * rolename=<username>,<username>,<username>
 */
class ReportGatekeeper
{
    /** @var array roles */
    private $roles = [];
    /** @var array rules */
    private $rules = [];
    /** @var array users to roles */
    private $userToRoles = [];

    /**
     * ReportGatekeeper constructor.
     */
    public function __construct()
    {
        // Build the acl

        $dsl = elgg_get_plugin_setting('acl', 'membership', '');

        if ($dsl == '') {
            return;
        }

        $this->evalAcl($dsl);
    }


    /**
     * Gatekeeper
     *
     * @param Request $request Request
     *
     * @return void
     * @throws HttpException
     */
    public function __invoke(Request $request)
    {
        $seasonGuid = $request->getParam('season_guid');
        $participationObjectsGuid = $request->getParam('participation_object_guids', '');
        $participationType = $request->getParam('participation_types', '');

        Tools::assert(!is_null($seasonGuid));

        $seasonGuids = preg_split('/,/', $seasonGuid);

        $participationObjectsGuids = ['*'];
        $participationTypes = ['*'];

        if ($participationObjectsGuid != '') {
            $participationObjectsGuids = preg_split('/,/', $participationObjectsGuid);
        }

        if ($participationType != '') {
            $participationTypes = preg_split('/,/', $participationType);
        }

        /** @var ElggUser $user */
        $user = elgg_get_logged_in_user_entity();

        if (is_null($user) || (!array_key_exists($user->username, $this->userToRoles) && !array_key_exists($user->username, $this->rules) && !array_key_exists('*', $this->rules))) {
            throw new HttpException(elgg_echo('membership:reports:gatekeeper:error'), 403);
        }

        $rules = $this->getRulesForUser($user->username);

        foreach ($seasonGuids as $season_guid) {
            if (!array_key_exists('*', $rules) && !array_key_exists($season_guid, $rules)) {
                throw new HttpException(elgg_echo('membership:reports:gatekeeper:error'), 403);
            }

            $season_rules = [];

            if (array_key_exists('*', $rules)) {
                $season_rules = array_replace_recursive($season_rules, $rules['*']);
            }

            if (array_key_exists($season_guid, $rules)) {
                $season_rules = array_replace_recursive($season_rules, $rules[$season_guid]);
            }

            foreach ($participationObjectsGuids as $participationObjectsGuid) {
                if (!array_key_exists('*', $season_rules) && !array_key_exists($participationObjectsGuid, $season_rules)) {
                    throw new HttpException(elgg_echo('membership:reports:gatekeeper:error'), 403);
                }

                $participation_object_rules = [];

                if (array_key_exists('*', $season_rules)) {
                    $participation_object_rules = array_replace_recursive($participation_object_rules, $season_rules['*']);
                }

                if (array_key_exists($participationObjectsGuid, $season_rules)) {
                    $participation_object_rules = array_replace_recursive($participation_object_rules, $season_rules[$participationObjectsGuid]);
                }

                foreach ($participationTypes as $participationType) {
                    if (!in_array('*', $participation_object_rules) && !in_array($participationType, $participation_object_rules)) {
                        throw new HttpException(elgg_echo('membership:reports:gatekeeper:error'), 403);
                    }
                }
            }
        }

    }

    /**
     * Evaluate the ACL DSL and build this->rules, this->roles and this->userToRole
     * @param $dsl
     */
    private function evalAcl($dsl): void
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

            if (preg_match_all('#^(?<target>[^:]+):/(?<season_guid>[0-9*,]+)/(?<participation_object_guids>[0-9*,]+)/(?<participation_types>[a-z,*]+)#', $line, $matches) > 0) {

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
     * Get all applicable roles for the given user
     * @param string $user
     * @return array Rules for the user
     */
    private function getRulesForUser(string $user): array
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
}
