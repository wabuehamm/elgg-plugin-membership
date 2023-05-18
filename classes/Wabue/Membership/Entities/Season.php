<?php

namespace Wabue\Membership\Entities;

use ElggObject;
use ElggUser;
use stdClass;

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

    public $acl = null;

    public function __construct(stdClass $row = null)
    {
        parent::__construct($row);
        $this->acl = \Wabue\Membership\Acl::factory();
    }

    /**
     * Get the year of this production
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Set the year of this producition
     * @param int $year
     */
    public function setYear(int $year)
    {
        $this->year = $year;
    }

    /**
     * Initialize the entity
     */
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        $this->attributes['subtype'] = 'season';
    }

    /**
     * Get the date where entries to this season are locked for non-admins
     * @return int
     */
    public function getLockdate(): int
    {
        return $this->lockdate;
    }

    /**
     * Set the date where entries to this season are locked for non-admins
     * @param int $lockdate
     */
    public function setLockdate(int $lockdate)
    {
        $this->lockdate = $lockdate;
    }

    /**
     * Get the end date of this season
     * @return int
     */
    public function getEnddate(): int
    {
        return $this->enddate;
    }

    /**
     * Set the end date for this season
     * @param int $enddate
     */
    public function setEnddate(int $enddate)
    {
        $this->enddate = $enddate;
    }

    /**
     * Generate the display name of this season
     * @return string
     */
    public function getDisplayName(): string
    {
        return elgg_echo("membership:season:title", [
            $this->year
        ]);
    }

    /**
     * Get the departments participation object of this season
     * @param bool $ignore_acl Ignore the ACL (e.g. when not on the report pages)
     * @return Departments|null The departments participation object
     */
    public function getDepartments(bool $ignore_acl = false): ?Departments
    {
        if (elgg_is_admin_logged_in()) {
            $ignore_acl = true;
        }
        if (
            ! $ignore_acl and count(
                $this->acl->getAllowedDepartments(elgg_get_logged_in_user_entity()->username, $this->guid)
            ) == 0
        ) {
            return null;
        }

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

    /**
     * Get the productions of this season
     * @param bool $ignore_acl Ignore the ACL (e.g. when not on the report pages)
     * @return array The productions of this season
     */
    public function getProductions(bool $ignore_acl = false): array
    {
        if (elgg_is_admin_logged_in()) {
            $ignore_acl = true;
        }

        $valid_productions = null;

        if (! $ignore_acl) {
            $valid_productions = $this->acl->getAllowedProductions(
                elgg_get_logged_in_user_entity()->username,
                $this->guid
            );
            if (in_array("*", $valid_productions)) {
                $valid_productions = null;
            }
        }

        return elgg_get_entities([
            'type' => 'object',
            'subtype' => 'production',
            'container_guid' => $this->guid,
            'guids' => $valid_productions,
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
        if (count($this->getDepartments(true)->getParticipations($member->getGUID())) > 0) {
            $didParticipate = true;
        }

        if (!$didParticipate) {
            /** @var Production[] $productions */
            $productions = $this->getProductions(true);
            foreach ($productions as $production) {
                if (count($production->getParticipations($member->getGUID())) > 0) {
                    $didParticipate = true;
                    break;
                }
            }
        }

        return $didParticipate;
    }

    /**
     * Create a new season object
     * @param int $year The season's year
     * @param int $enddate The end date
     * @param int $lockdate The locking date
     * @param string $participationTypes An array of valid participation types for the departments participation object
     * @return Season
     */
    public static function factory(int $year, int $enddate, int $lockdate, string $participationTypes): Season
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
