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
     * Return the participation types in serialized form suitable for
     * form fields
     * @return string The participation types as string
     */
    public function getParticipationTypesAsString() {
        $return = [];

        foreach ($this->participationTypes as $key => $label) {
            array_push($return, "$key:$label");
        }

        return join("\n", $return);
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
        if ($cleanedSetting == null || $cleanedSetting == '') {
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