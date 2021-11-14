<?php

namespace Wabue\Membership\Entities;

use ElggObject;
use stdClass;

/**
 * A base class for productions and departments which can record a specific participations
 */
abstract class ParticipationObject extends ElggObject
{

    /**
     * @var \Wabue\Membership\Acl|null The ACL singleton
     */
    private $_acl = null;

    /**
     * @var null A cache for storing the "parent" (related) GUID of this participation object
     */
    private $_related_guid = null;

    /**
     * @var array A cache for the resolved participation types
     */
    private $_resolved_types = [];

    /**
     * Fetch the acl singleton
     * @param stdClass|null $row
     * @throws \IOException
     */
    public function __construct(stdClass $row = null)
    {
        parent::__construct($row);
        $this->_acl = \Wabue\Membership\Acl::factory();
    }

    /**
     * Initialize the participation types
     */
    protected function initializeAttributes()
    {
        parent::initializeAttributes();
        // participationTypes is an additional entity attribute and is not defined here
        $this->participationTypes = serialize([]);
    }

    /**
     * Get the valid participation types of this participation object
     * @param bool $ignore_acl Ignore ACL checks (e.g. if not on the report view)
     * @return array The valid participation types
     */
    public function getParticipationTypes(bool $ignore_acl = false)
    {
        if (count(unserialize($this->participationTypes)) == 0) {
            return [];
        }

        $return = [];

        if (is_null($this->_related_guid)) {
            if ($this->subtype == "departments" or $this->subtype == "production") {
                $this->_related_guid = $this->guid;
                $this->_resolved_types = unserialize($this->participationTypes);
            } else {
                $related_entities = elgg_get_entities([
                    "relationship_guid" => [$this->guid],
                    "relationship" => "participate",
                ]);
                $this->_related_guid = $related_entities[0]->guid;
                foreach (unserialize($this->participationTypes) as $particionType) {
                    $this->_resolved_types[$particionType] = unserialize(
                        $related_entities[0]->participationTypes
                    )[$particionType];
                }
            }
        }

        foreach ($this->_resolved_types as $key => $value) {
            if ($ignore_acl or $this->_acl->isParticipationAllowed(
                elgg_get_logged_in_user_entity()->username,
                $this->container_guid,
                $this->_related_guid,
                $key
            )) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    /**
     * Return the participation types in serialized form suitable for
     * form fields
     * @param bool $ignore_acl Ignore ACL checks (e.g. if not on the report view)
     * @return string The participation types as string
     */
    public function getParticipationTypesAsString(bool $ignore_acl = false) {
        $return = [];

        foreach ($this->getParticipationTypes($ignore_acl) as $key => $label) {
            array_push($return, "$key:$label");
        }

        return join("\n", $return);
    }

    /**
     * Add a participation type to the list of valid participation types
     * @param $participationType
     */
    public function addParticipationType($participationType)
    {
        if (! is_array($participationType)) {
            $participationType = [$participationType];
        }
        $this->setParticipationTypes(array_unique($this->getParticipationTypes() + $participationType));
    }

    /**
     * Set the valid participation types
     * @param array $participationTypes
     */
    public function setParticipationTypes(array $participationTypes)
    {
        $this->participationTypes = serialize($participationTypes);
    }

    /**
     * Get the participations of this participation object
     * @param null $owner_guid
     * @return \ElggEntity[]|int|mixed
     */
    public function getParticipations($owner_guid = null)
    {
        $season_guid = $this->container_guid;
        $options = [
            'type' => 'object',
            'subtype' => 'participation',
            'container_guid' => $season_guid,
            'relationship_guid' => $this->guid,
            'relationship' => 'participate',
            'inverse_relationship' => true,
            'limit' => 999,
        ];
        if (!is_null($owner_guid)) {
            $options['owner_guids'] = [$owner_guid];
        }
        return elgg_get_entities($options);
    }

    /**
     * Cleans a participation setting from unwanted whitespace, carriage returns and empty lines
     * @param $setting string Setting string
     * @return string The cleaned setting string
     */
    public static function cleanParticipationSetting(string $setting): string {
        $noWhitespace = preg_replace('/^\s*(.+[^ ])\s*$/m', '$1', $setting);
        $noCarriageReturn = preg_replace("/\r/", '', $noWhitespace);
        return trim($noCarriageReturn);
    }

    /**
     * Converts a participation setting string to an associative array
     * @param $setting string the participation setting
     * @return array the associative array <keyword> => <title>
     */
    public static function participationSettingToArray(string $setting): array {
        $return = [];
        foreach (preg_split('/\n/', self::cleanParticipationSetting($setting)) as $settingLine) {
            list($keyword, $title) = preg_split('/:/', $settingLine);
            $return[$keyword] = $title;
        }
        return $return;
    }

    /**
     * Validate a participation type setting
     * @param string $setting The setting to validate
     * @return bool wether it complies or not
     */
    public static function validateParticipationSetting(string $setting): bool {
        $cleanedSetting = self::cleanParticipationSetting($setting);
        if (is_null($cleanedSetting) || $cleanedSetting == '') {
            return false;
        }

        $lines = preg_split('/\n/', $cleanedSetting);
        if (count($lines) == 0) {
            return false;
        }

        foreach($lines as $line) {
            if (preg_match('/^[^:]+:[^:]+$/', $line) !== 1) {
                return false;
            }
        }
        return true;
    }

}
