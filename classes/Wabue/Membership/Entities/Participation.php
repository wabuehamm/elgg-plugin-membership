<?php

namespace Wabue\Membership\Entities;

use ElggObject;
use ElggUser;

/**
 * Class Participation
 * Record a participation in a season for a user.
 * Used together with relationships to either a production or department
 * @package Wabue\Membership
 */
class Participation extends ParticipationObject
{
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes['subtype'] = 'participation';
    }

    public static function factory(ElggUser $user, Season $season, ParticipationObject $participationObject)
    {
        $participation = new Participation();
        $participation->owner_guid = $user->guid;
        $participation->container_guid = $season->guid;
        $participation->addRelationship($participationObject->guid, 'participate');
        $participation->setParticipationTypes([]);
        return $participation;
    }
}
