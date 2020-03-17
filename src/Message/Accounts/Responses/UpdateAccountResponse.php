<?php


namespace PHPAccounting\Xero\Message\Accounts\Responses;

use Omnipay\Common\Message\AbstractResponse;
use PHPAccounting\Xero\Helpers\ErrorResponseHelper;
use PHPAccounting\Xero\Helpers\IndexSanityCheckHelper;

class UpdateAccountResponse extends AbstractResponse
{
    /**
     * Check Response for Error or Success
     * @return boolean
     */
    public function isSuccessful()
    {
        if ($this->data) {
            if(array_key_exists('status', $this->data)){
                return !$this->data['status'] == 'error';
            }
            if (is_array($this->data)) {
                if (count($this->data) === 0) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Fetch Error Message from Response
     * @return string
     */
    public function getErrorMessage(){
        if ($this->data) {
            if(array_key_exists('status', $this->data)){
                return ErrorResponseHelper::parseErrorResponse($this->data['detail'], 'Account');
            }
            if (count($this->data) === 0) {
                return 'NULL Returned from API or End of Pagination';
            }
        }
        return null;
    }

    /**
     * Return all Invoices with Generic Schema Variable Assignment
     * @return array
     */
    public function getAccounts(){
        $accounts = [];
        foreach ($this->data as $account) {
            $newAccount = [];
            $newAccount['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('AccountID', $account);
            $newAccount['code'] = IndexSanityCheckHelper::indexSanityCheck('Code', $account);
            $newAccount['name'] = IndexSanityCheckHelper::indexSanityCheck('Name', $account);
            $newAccount['description'] = IndexSanityCheckHelper::indexSanityCheck('Description', $account);
            $newAccount['type'] = IndexSanityCheckHelper::indexSanityCheck('Type', $account);
            $newAccount['is_bank_account'] = (IndexSanityCheckHelper::indexSanityCheck('Type', $account) === 'BANK');
            $newAccount['enable_payments_to_account'] = IndexSanityCheckHelper::indexSanityCheck('EnablePaymentsToAccount', $account);;
            $newAccount['show_inexpense_claims'] = IndexSanityCheckHelper::indexSanityCheck('ShowInexpenseClaims', $account);
            $newAccount['tax_type'] = IndexSanityCheckHelper::indexSanityCheck('TaxType', $account);
            $newAccount['bank_account_number'] = IndexSanityCheckHelper::indexSanityCheck('BankAccountNumber', $account);
            $newAccount['bank_account_type'] = IndexSanityCheckHelper::indexSanityCheck('BankAccountType', $account);
            $newAccount['currency_code'] = IndexSanityCheckHelper::indexSanityCheck('CurrencyCode', $account);
            $newAccount['system_account'] = IndexSanityCheckHelper::indexSanityCheck('SystemAccount', $account);
            $newAccount['updated_at'] = IndexSanityCheckHelper::indexSanityCheck('UpdatedDateUTC', $account);
            array_push($accounts, $newAccount);
        }

        return $accounts;
    }
}