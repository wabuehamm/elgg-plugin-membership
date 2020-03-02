<?php

namespace Wabue\Membership\Entities;

/**
 * Class Departments
 * Used for participation in non-production tasks
 * @package Wabue\Membership
 */

class Departments extends ParticipationObject {
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

}
