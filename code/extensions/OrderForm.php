<?php
class StreakAddresses_OrderFormExtension extends Extension {
    // insert after this field (excluding the 'Shipping' and 'Billing' prefix
    private static $streak_addresses_insert_after = 'Company';
    // either above or below should be set, after will be used in preference anyway
    private static $streak_addresses_insert_before = '';

    // add required fields in config, e.g. 'ShippingStreakPhone' and 'BillingStreakMobile'
    private static $streak_addresses_required_fields = array();
    /**
     * Add phone number fields as defined in StreakAddresses_PhoneNumberExtension to OrderForm
     * @param FieldList $fields
     */
    public function updateFields(FieldList $fields) {
        $compositeFields = array(
            'Shipping',
            'Billing'
        );

        $config = Config::inst();

        list($phoneFields, $labels) = CrackerjackModule::get_config_settings(
            'StreakAddresses_PhoneNumbersExtension',
            array('db', 'field_labels')
        );
        list($insertAfter, $insertBefore, $required) = CrackerjackModule::get_config_settings(
            __CLASS__,
            array(
                'streak_addresses_insert_after',
                'streak_addresses_insert_before',
                'streak_addresses_required_fields'
            )
        );

        foreach ($compositeFields as $prefix) {
            foreach ($phoneFields as $fieldName => $schema) {

                if ($compositeField = $fields->fieldByName($prefix . 'Address')) {
                    /** FormField */
                    if ($insertAfter) {
                        $field = $compositeField->insertAfter(
                            new TextField($prefix . $fieldName, isset($labels[$fieldName]) ? $labels[$fieldName] : $fieldName),
                            $prefix . $insertAfter
                        );
                    } else {
                        $field = $compositeField->insertBefore(
                            new TextField($prefix . $fieldName, isset($labels[$fieldName]) ? $labels[$fieldName] : $fieldName),
                            $prefix . $insertBefore
                        );
                    }
                    $field->addExtraClass('phone-number');

                    if (isset($required[$fieldName])) {
                        $field->setCustomValidationMessage(
                            _t('CheckoutPage.PLEASE_ENTER_PHONE', "Please enter a phone number.")
                        );
                    }
                }
            }
        }
    }

    /**
     * Add required fields from config.streak_addresses_required_fields
     * @param $validator
     */
    public function updateValidator($validator) {
        $validator->appendRequiredFields(
            RequiredFields::create(
                Config::inst()->get(__CLASS__, 'streak_addresses_required_fields')
            )
        );
    }
    public function updatePopulateFields(&$data) {
        $member = Customer::currentUser() ? Customer::currentUser() : singleton('Customer');

        $shippingAddress = $member->ShippingAddress();
        $shippingAddressData = ($shippingAddress && $shippingAddress->exists())
            ? array(
                'ShippingStreakPhone' => $shippingAddress->StreakPhone,
                'ShippingStreakMobile' => $shippingAddress->StreakMobile
            )
            : array();

        $billingAddress = $member->BillingAddress();
        $billingAddressData = ($billingAddress && $billingAddress->exists())
            ? array(
                'BillingStreakPhone' => $billingAddress->StreakPhone,
                'BillingStreakMobile' => $billingAddress->StreakMobile
            )
            : array();

        $data = array_merge(
            $data,
            $shippingAddressData,
            $billingAddressData
        );
    }
}