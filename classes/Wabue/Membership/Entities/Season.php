<?php

namespace Wabue\Membership\Entities;

use ElggObject;

/**
 * Class Season
 * Membership participation records for a specific season
 * @package Wabue\Membership
 * @property int    year
 * @property string lockdate
 * @property string enddate
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
     * @return string
     */
    public function getLockdate(): string
    {
        return $this->lockdate;
    }

    /**
     * @param string $lockdate
     */
    public function setLockdate(string $lockdate)
    {
        $this->lockdate = $lockdate;
    }

    /**
     * @return string
     */
    public function getEnddate(): string
    {
        return $this->enddate;
    }

    /**
     * @param string $enddate
     */
    public function setEnddate(string $enddate)
    {
        $this->enddate = $enddate;
    }

    public function getDisplayName(): string
    {
        return elgg_echo("membership:season:title", [
            $this->year
        ]);
    }

}
