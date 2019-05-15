<?php

namespace PHPAccounting\XERO\Message\Customers\Responses;


use PHPAccounting\Common\Message\RequestInterface;
use Response;

class CreateCustomerResponse extends Response
{

    public function __construct(RequestInterface $request, $data, array $headers = [])
    {
        parent::__construct($request, $data, $headers);
    }
}