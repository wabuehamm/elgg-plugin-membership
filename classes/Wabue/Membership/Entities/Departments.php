<?php

namespace Wabue\Membership\Entities;

/**
 * Class Departments
 * Used for participation in non-production tasks
 * @package Wabue\Membership
 */
class Departments extends ParticipationObject {

    /**
     * Initialize the entity
     */
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes['subtype'] = 'departments';
    }

    public function getURL()
    {
        return false;
    }

    public function getIconURL($params = [])
    {
        return null;
    }

    public function getDisplayName(): string
    {
        return elgg_echo("membership:departments:title");
    }

    public static function factory($seasonGuid, $participationTypes) {
        $departments = new Departments();
        $departments->owner_guid = 0;
        $departments->access_id = ACCESS_PUBLIC;
        $departments->container_guid = $seasonGuid;
        $departments->setParticipationTypes(
            ParticipationObject::participationSettingToArray($participationTypes)
        );
        $departments->save();
        return $departments;
    }
}
