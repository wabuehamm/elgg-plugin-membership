<?php

namespace Wabue\Membership\Entities;

use ElggObject;

/**
 * Class Season
 * Membership participation records for a specific season
 * @package Wabue\Membership
 * @property int year
 * @property int lockdate
 * @property int enddate
 */
class Season extends ElggObject
{
    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year)
    {
        $this->year = $year;
    }
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes['subtype'] = 'season';
    }

    /**
     * @return int
     */
    public function getLockdate(): int
    {
        return $this->lockdate;
    }

    /**
     * @param int $lockdate
     */
    public function setLockdate(int $lockdate)
    {
        $this->lockdate = $lockdate;
    }

    /**
     * @return int
     */
    public function getEnddate(): int
    {
        return $this->enddate;
    }

    /**
     * @param int $enddate
     */
    public function setEnddate(int $enddate)
    {
        $this->enddate = $enddate;
    }

    public function getDisplayName(): string
    {
        return elgg_echo("membership:season:title", [
            $this->year
        ]);
    }

    public function getDepartments(): Departments
    {
        $departments = elgg_get_entities([
            'type' => 'object',
            'subtype' => 'departments',
            'container_guid' => $this->guid,
        ]);

        if (count($departments) == 0 || !$departments[0] instanceof Departments) {
            return null;
        }

        return $departments[0];
    }

    public function getProductions(): Array
    {
        return elgg_get_entities([
            'type' => 'object',
            'subtype' => 'production',
            'container_guid' => $this->guid,
            'order_by_metadata' => [
                'name' => 'title',
                'direction' => 'ASC',
                'as' => 'string'
            ]
        ]);
    }

}
