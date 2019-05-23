<?php
namespace PHPAccounting\Xero\Message\Contacts\Responses;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class GetContactResponse
 * @package PHPAccounting\Xero\Message\Contacts\Responses
 */
class GetContactResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data != null;
    }

    /**
     * Create Generic Contact Groups if Valid
     * @param $data
     * @param $contact
     * @return mixed
     */
    private function parseContactGroups($data, $contact) {
        $contact['contact_groups'] = [];
        if ($data) {
            $contactGroups = [];
            foreach($data as $contactGroup) {
                $newContactGroup = [];
                $newContactGroup['accounting_id'] = $contactGroup->getContactGroupID();
                $newContactGroup['name'] = $contactGroup->getName();
                $newContactGroup['status'] = $contactGroup->getStatus();
                array_push($contactGroups, $newContactGroup);
            }
            $contact['contact_groups'] = $contactGroups;
        }

        return $contact;
    }
    /**
     * Create Generic Addresses if Valid
     * @param $data
     * @param $contact
     * @return mixed
     */
    private function parseAddresses($data, $contact) {
        $contact['addresses'] = [];
        if ($data) {
            $addresses = [];
            foreach($data as $address) {
                $newAddress = [];
                $newAddress['address_type'] = $address->getAddressType();
                $newAddress['address_line_1'] = $address->getAddressLine1();
                $newAddress['city'] = $address->getCity();
                $newAddress['postal_code'] = $address->getPostalCode();
                $newAddress['country'] = $address->getCountry();
                array_push($addresses, $newAddress);
            }
            $contact['addresses'] = $addresses;
        }

        return $contact;
    }

    /**
     * Create Generic Phones if Valid
     * @param $data
     * @param $contact
     * @return mixed
     */
    private function parsePhones($data, $contact) {
        if ($data) {
            foreach($data as $phone) {
                $phoneNumber = $phone->getPhoneAreaCode().$phone->getPhoneNumber();
                switch($phone->getPhoneType()){
                    case 'DEFAULT':
                        $contact['business_hours_phone'] = $phoneNumber;
                        break;
                    case 'DDI':
                        $contact['after_hours_phone'] = $phoneNumber;
                        break;
                    case 'MOBILE':
                        $contact['mobile_phone'] = $phoneNumber;
                        break;
                }
            }
        }

        return $contact;
    }

    /**
     * Return all Contacts with Generic Schema Variable Assignment
     * @return array
     */
    public function getContacts(){
        $contacts = [];
        foreach ($this->data as $contact) {
            $newContact = [];
            $newContact['accounting_id'] = $contact->getContactID();
            $newContact['display_name'] = $contact->getFirstName();
            $newContact['last_name'] = $contact->getLastName();
            $newContact['email_address'] = $contact->getEmailAddress();
            $newContact['website'] = $contact->getWebsite();
            $newContact['type'] = ($contact->getIsSupplier() ? 'SUPPLIER' : 'CUSTOMER');
            $newContact['is_individual'] = $contact->getIsCustomer();
            $newContact['bank_account_details'] = $contact->getBankAccountDetails();
            $newContact['tax_number'] = $contact->getTaxNumber();
            $newContact['accounts_receivable_tax_type'] = $contact->getAccountsReceivableTaxType();
            $newContact['accounts_payable_tax_type'] = $contact->getAccountsPayableTaxType();
            $newContact['default_currency'] = $contact->getDefaultCurrency();
            $newContact = $this->parseContactGroups($contact->getContactGroups(), $newContact);
            $newContact = $this->parsePhones($contact->getPhones(), $newContact);
            $newContact = $this->parseAddresses($contact->getAddresses(), $newContact);
            array_push($contacts, $newContact);
        }
        return $contacts;
    }
}