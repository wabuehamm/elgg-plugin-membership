<?php

namespace Wabue\Membership\Entities;

use ElggObject;
use ElggUser;

/**
 * Class Season
 * Membership participation records for a specific season
 * @package Wabue\Membership
 * @property int year The seasons year
 * @property int lockdate The date (as a Unix epoch) where the season is locked
 * @property int enddate The date (as a Unix epoch) where the season ends
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

    /**
     * Did the given member participate in this season in any way?
     * @param $member ElggUser member to check
     * @return bool wether the user participated or not
     */
    public function didMemberParticipate($member): bool {
        $didParticipate = false;
        if (count($this->getDepartments()->getParticipations($member->getGUID())) > 0) {
            $didParticipate = true;
        }

        if (!$didParticipate) {
            /** @var Production[] $productions */
            $productions = $this->getProductions();
            foreach ($productions as $production) {
                if (count($production->getParticipations($member->getGUID())) > 0) {
                    $didParticipate = true;
                    break;
                }
            }
        }

        return $didParticipate;
    }

    public static function factory($year, $enddate, $lockdate, $participationTypes): Season
    {
        $season = new Season();
        $season->owner_guid = 0;
        $season->access_id = ACCESS_PUBLIC;
        $season->setYear($year);
        $season->setEnddate($enddate);
        $season->setLockdate($lockdate);
        $season->save();

        Departments::factory($season->getGUID(), $participationTypes);
        return $season;
    }

}
