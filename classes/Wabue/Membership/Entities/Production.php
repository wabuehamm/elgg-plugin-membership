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

}