<?php

namespace Wabue\Membership\Entities;

use ElggObject;

/**
 * @property array participationTypes The types the member wishes to participate
 */
abstract class ParticipationObject extends ElggObject
{
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->participationTypes = [];
    }

    /**
     * @return array
     */
    public function getParticipationTypes()
    {
        return $this->participationTypes;
    }

    /**
     * @param array $participationTypes
     */
    public function addParticipationTypes($participationTypes)
    {
        $this->participationTypes = array_merge($this->participationTypes, [$participationTypes]);
    }

    /**
     * @param array $participationTypes
     */
    public function setParticipationTypes(array $participationTypes)
    {
        $this->participationTypes = $participationTypes;
    }

    public function getParticipations()
    {
        $season_guid = $this->container_guid;
        return elgg_get_entities([
            'type' => 'object',
            'subtype' => 'participation',
            'container_guid' => $season_guid,
            'relationship_guid' => $this->guid,
            'relationship' => 'participate'
        ]);
    }

}