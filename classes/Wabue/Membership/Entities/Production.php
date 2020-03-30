<?php

namespace Wabue\Membership\Entities;

/**
 * Class Production
 * Used for participation in a production
 * @package Wabue\Membership
 */
class Production extends ParticipationObject
{
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes['subtype'] = 'production';
    }

    static public function factory($title, $seasonGuid, $participationTypes) {
        $entity = new Production();
        $entity->owner_guid = 0;
        $entity->access_id = ACCESS_PUBLIC;
        $entity->title = $title;
        $entity->container_guid = $seasonGuid;
        $entity->setParticipationTypes(
            ParticipationObject::participationSettingToArray($participationTypes)
        );
        $entity->save();
        return $entity;
    }

}
