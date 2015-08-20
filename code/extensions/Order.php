<?php
class StreakAddresses_OrderExtension extends DataExtension {
    public function onBeforePayment() {
        $customer = $this->owner->Member();
        if ($customer && $customer->exists()) {
            $this->updateAddresses($this->owner);
        }
    }
    protected function updateAddresses($order) {
        $data = $order->toMap();

        $shippingAddress = Address_Shipping::create(array(
            'MemberID' => $this->owner->Member()->ID,
            'FirstName' => $data['ShippingFirstName'],
            'Surname' => $data['ShippingSurname'],
            'Company' => $data['ShippingCompany'],
            'Address' => $data['ShippingAddress'],
            'AddressLine2' => $data['ShippingAddressLine2'],
            'City' => $data['ShippingCity'],
            'PostalCode' => $data['ShippingPostalCode'],
            'State' => $data['ShippingState'],
            'CountryName' => $data['ShippingCountryName'],
            'CountryCode' => $data['ShippingCountryCode'],
            'RegionName' => (isset($data['ShippingRegionName'])) ? $data['ShippingRegionName'] : null,
            'RegionCode' => (isset($data['ShippingRegionCode'])) ? $data['ShippingRegionCode'] : null,
        ));
        $newShipping = array_filter($shippingAddress->toMap());

        $billingAddress = Address_Billing::create(array(
            'MemberID' => $this->owner->Member()->ID,
            'FirstName' => $data['BillingFirstName'],
            'Surname' => $data['BillingSurname'],
            'Company' => $data['BillingCompany'],
            'Address' => $data['BillingAddress'],
            'AddressLine2' => $data['BillingAddressLine2'],
            'City' => $data['BillingCity'],
            'PostalCode' => $data['BillingPostalCode'],
            'State' => $data['BillingState'],
            'CountryName' => $data['BillingCountryName'],
            'CountryCode' => $data['BillingCountryCode'],
            'RegionName' => (isset($data['BillingRegionName'])) ? $data['ShippingRegionName'] : null,
            'RegionCode' => (isset($data['BillingRegionCode'])) ? $data['ShippingRegionCode'] : null,
        ));
        $newBilling = array_filter($billingAddress->toMap());

        foreach ($this->owner->Member()->ShippingAddresses() as $address) {

            $existing = array_filter($address->toMap());
            $result = array_intersect_assoc($existing, $newShipping);

            //If no difference, then match is found
            $diff = array_diff_assoc($newShipping, $result);
            $match = empty($diff);

            if ($match) {
                $address->StreakPhone = $order->ShippingStreakPhone;
                $address->StreakMobile = $order->ShippingStreakMobile;

                $address->write();
            }
        }
        foreach ($this->owner->Member()->BillingAddresses() as $address) {

            $existing = array_filter($address->toMap());
            $result = array_intersect_assoc($existing, $newBilling);

            $diff = array_diff_assoc($newBilling, $result);
            $match = empty($diff);

            if ($match) {
                $address->StreakPhone = $order->BillingStreakPhone;
                $address->StreakMobile = $order->BillingStreakMobile;

                $address->write();
            }
        }
    }
}