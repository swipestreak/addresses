<?php

/**
 * Adds phone numbers to address model.
 *
 * StreakAddresses_AddressExtension
 */
class StreakAddresses_PhoneNumbersExtension extends DataExtension {
    private static $db = array(
        'StreakPhone' => 'Varchar(32)',
        'StreakMobile' => 'Varchar(32)'
    );
    private static $field_labels = array(
        'StreakPhone' => 'Phone',
        'StreakMobile' => 'Mobile'
    );
    private static $summary_fields = array(
        'StreakPhone' => 'Phone',
        'StreakMobile' => 'Mobile'
    );
    private static $enabled = true;

    public static function enabled() {
        return Config::inst()->get(__CLASS__, 'enabled');
    }
    public static function enable() {
        Config::inst()->update(__CLASS__, 'enabled', true);
    }
    public static function disable() {
        Config::inst()->update(__CLASS__, 'enabled', false);
    }
}