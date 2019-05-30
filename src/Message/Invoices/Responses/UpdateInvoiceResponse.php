<?php
/**
 * Created by IntelliJ IDEA.
 * User: Dylan
 * Date: 29/05/2019
 * Time: 1:07 PM
 */

namespace PHPAccounting\Xero\Message\Invoices\Responses;


use Omnipay\Common\Message\AbstractResponse;
use PHPAccounting\Xero\Helpers\IndexSanityCheckHelper;

class UpdateInvoiceResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if(array_key_exists('status', $this->data)){
            return !$this->data['status'] == 'error';
        }
        return true;
    }

    public function getErrorMessage(){
        if(array_key_exists('status', $this->data)){
            return $this->data['detail'];
        }
        return null;
    }

    /**
     * Create Generic Phones if Valid
     * @param $data
     * @param $invoice
     * @return mixed
     */
    private function parseLineItems($data, $invoice) {
        if ($data) {
            $lineItems = [];
            foreach($data as $lineItem) {
                $newLineItem = [];
                $newLineItem['description'] = IndexSanityCheckHelper::indexSanityCheck('Description', $lineItem);
                $newLineItem['unit_amount'] = IndexSanityCheckHelper::indexSanityCheck('UnitAmount', $lineItem);
                $newLineItem['line_amount'] = IndexSanityCheckHelper::indexSanityCheck('LineAmount', $lineItem);
                $newLineItem['quantity'] = IndexSanityCheckHelper::indexSanityCheck('Quantity', $lineItem);
                $newLineItem['discount'] = IndexSanityCheckHelper::indexSanityCheck('DiscountRate', $lineItem);
                $newLineItem['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('LineItemID', $lineItem);
                $newLineItem['discount_amount'] = IndexSanityCheckHelper::indexSanityCheck('DiscountAmount', $lineItem);
                $newLineItem['amount'] = IndexSanityCheckHelper::indexSanityCheck('LineAmount', $lineItem);
                array_push($lineItems, $newLineItem);
            }

            $invoice['invoice_data'] = $lineItems;
        }

        return $invoice;
    }
    /**
     * Create Generic Phones if Valid
     * @param $data
     * @param $invoice
     * @return mixed
     */
    private function parseContact($data, $invoice) {
        if ($data) {
            $newContact = [];
            $newContact['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('ContactID',$data);
            $newContact['name'] = IndexSanityCheckHelper::indexSanityCheck('Name',$data);
            $invoice['contact'] = $newContact;
        }

        return $invoice;
    }

    /**
     * Return all Contacts with Generic Schema Variable Assignment
     * @return array
     */
    public function getInvoice(){
        $invoices = [];
        foreach ($this->data as $invoice) {
            $newInvoice = [];
            $newInvoice['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('InvoiceID', $invoice);
            $newInvoice['status'] = IndexSanityCheckHelper::indexSanityCheck('Status', $invoice);
            $newInvoice['sub_total'] = IndexSanityCheckHelper::indexSanityCheck('SubTotal', $invoice);
            $newInvoice['total_tax'] = IndexSanityCheckHelper::indexSanityCheck('TotalTax', $invoice);
            $newInvoice['total'] = IndexSanityCheckHelper::indexSanityCheck('Total', $invoice);
            $newInvoice['currency'] = IndexSanityCheckHelper::indexSanityCheck('CurrencyCode', $invoice);
            $newInvoice['type'] = IndexSanityCheckHelper::indexSanityCheck('Type', $invoice);
            $newInvoice['invoice_number'] = IndexSanityCheckHelper::indexSanityCheck('InvoiceNumber', $invoice);
            $newInvoice['amount_due'] = IndexSanityCheckHelper::indexSanityCheck('AmountDue', $invoice);
            $newInvoice['amount_paid'] = IndexSanityCheckHelper::indexSanityCheck('AmountPaid', $invoice);
            $newInvoice['currency_rate'] = IndexSanityCheckHelper::indexSanityCheck('CurrencyRate', $invoice);
            $newInvoice['discount_total'] = IndexSanityCheckHelper::indexSanityCheck('TotalDiscount', $invoice);
            $newInvoice['date'] = IndexSanityCheckHelper::indexSanityCheck('Date', $invoice);

            if (IndexSanityCheckHelper::indexSanityCheck('Contact', $invoice)) {
                $newInvoice = $this->parseContact($invoice['Contact'], $newInvoice);
            }
            if (IndexSanityCheckHelper::indexSanityCheck('LineItems', $invoice)) {
                $newInvoice = $this->parseLineItems($invoice['LineItems'], $newInvoice);
            }

            array_push($invoices, $newInvoice);
        }

        return $invoices;
    }
}