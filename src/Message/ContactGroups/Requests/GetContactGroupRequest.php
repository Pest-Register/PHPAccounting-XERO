<?php
namespace PHPAccounting\Xero\Message\ContactGroups\Requests;

use PHPAccounting\Xero\Message\AbstractRequest;
use PHPAccounting\Xero\Message\ContactGroups\Responses\GetAccountResponse;
use PHPAccounting\Xero\Message\ContactGroups\Responses\GetContactGroupResponse;
use XeroPHP\Models\Accounting\ContactGroup;
use XeroPHP\Remote\Exception\UnauthorizedException;
use XeroPHP\Remote\Exception\BadRequestException;
use XeroPHP\Remote\Exception\ForbiddenException;
use XeroPHP\Remote\Exception\ReportPermissionMissingException;
use XeroPHP\Remote\Exception\NotFoundException;
use XeroPHP\Remote\Exception\InternalErrorException;
use XeroPHP\Remote\Exception\NotImplementedException;
use XeroPHP\Remote\Exception\RateLimitExceededException;
use XeroPHP\Remote\Exception\NotAvailableException;
use XeroPHP\Remote\Exception\OrganisationOfflineException;
/**
 * Get Contact Group(s)
 * @package PHPAccounting\XERO\Message\ContactGroups\Requests
 */
class GetContactGroupRequest extends AbstractRequest
{
    /**
     * Set AccountingID from Parameter Bag (ContactGroupID generic interface)
     * @see https://developer.xero.com/documentation/api/contactgroups
     * @param $value
     * @return GetContactGroupRequest
     */
    public function setAccountingIDs($value) {
        return $this->setParameter('accounting_ids', $value);
    }

    /**
     * Set Page Value for Pagination from Parameter Bag
     * @see https://developer.xero.com/documentation/api/contactgroups
     * @param $value
     * @return GetContactGroupRequest
     */
    public function setPage($value) {
        return $this->setParameter('page', $value);
    }

    /**
     * Return Comma Delimited String of Accounting IDs (ContactGroupIDs)
     * @return mixed comma-delimited-string
     */
    public function getAccountingIDs() {
        if ($this->getParameter('accounting_ids')) {
            return implode(', ',$this->getParameter('accounting_ids'));
        }
        return null;
    }

    /**
     * Return Page Value for Pagination
     * @return integer
     */
    public function getPage() {
        return $this->getParameter('page');
    }

    /**
     * Send Data to Xero Endpoint and Retrieve Response via Response Interface
     * @param mixed $data Parameter Bag Variables After Validation
     * @return \Omnipay\Common\Message\ResponseInterface|GetContactGroupResponse
     * @throws \XeroPHP\Exception
     */
    public function sendData($data)
    {
        try {
            $xero = $this->createXeroApplication();


            if ($this->getAccountingIDs()) {
                if(strpos($this->getAccountingIDs(), ',') === false) {
                    $contactGroups = $xero->loadByGUID(ContactGroup::class, $this->getAccountingIDs());
                }
                else {
                    $contactGroups = $xero->loadByGUIDs(ContactGroup::class, $this->getAccountingIDs());
                }
            } else {
                $contactGroups = $xero->load(ContactGroup::class)->execute();
            }
            $response = $contactGroups;

        } catch (BadRequestException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'BadRequest',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (UnauthorizedException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'Unauthorized',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (ForbiddenException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'Forbidden',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (ReportPermissionMissingException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'ReportPermissionMissingException',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (NotFoundException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'NotFound',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (InternalErrorException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'Internal',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (NotImplementedException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'NotImplemented',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (RateLimitExceededException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'RateLimitExceeded',
                'rate_problem' => $exception->getRateLimitProblem(),
                'retry' => $exception->getRetryAfter(),
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (NotAvailableException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'NotAvailable',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        } catch (OrganisationOfflineException $exception) {
            $response = [
                'status' => 'error',
                'type' => 'OrganisationOffline',
                'detail' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'status_code' => $exception->getCode(),
            ];

            return $this->createResponse($response);
        }
        return $this->createResponse($response);
    }

    /**
     * Create Generic Response from Xero Endpoint
     * @param mixed $data Array Elements or Xero Collection from Response
     * @return GetContactGroupResponse
     */
    public function createResponse($data)
    {
        return $this->response = new GetContactGroupResponse($this, $data);
    }
}