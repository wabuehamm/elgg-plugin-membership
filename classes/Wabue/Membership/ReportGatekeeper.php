<?php

namespace Wabue\Membership;

use Elgg\HttpException;
use Elgg\Request;
use ElggUser;

/**
 * Class ReportGatekeeper
 *
 * The report gatekeeper does intense ACL checkings for showing the available reports.
 */
class ReportGatekeeper
{

    private $_aclClass;

    /**
     * ReportGatekeeper constructor.
     */
    public function __construct()
    {
        $this->_aclClass = Acl::factory();
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

        $seasonGuids = $seasonGuid != "" ? preg_split('/,/', $seasonGuid) : [];
        $participationObjectsGuids = $participationObjectsGuid != "" ? preg_split('/,/', $participationObjectsGuid) : [];
        $participationTypes = $participationType != "" ? preg_split('/,/', $participationType): [];

        /** @var ElggUser $user */
        $user = elgg_get_logged_in_user_entity();

        $rules = $this->_aclClass->getRulesForUser($user->username);

        if ($request->getPath() == "membership" && count($rules) > 0) {
            # The user has at least one rule allowing them to see the membership overview
            return;
        } elseif (count($rules) == 0) {
            throw new HttpException(elgg_echo('membership:reports:gatekeeper:errorredirect', [
                elgg_generate_url('view:participations:seasons', [
                    'guid' => $user->getGUID(),
                ])
            ]), 403);
        }

        if (!$this->_aclClass->canAccess($user->username, $seasonGuids, $participationObjectsGuids, $participationTypes)) {
            throw new HttpException(elgg_echo('membership:reports:gatekeeper:error'), 403);
        }
    }

}
