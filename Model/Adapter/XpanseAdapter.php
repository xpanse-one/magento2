<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace xpanse\Payment\Model\Adapter;

use xpanse\Sdk as xpanseSDK;

/**
 * Class XpanseAdapter
 * @codeCoverageIgnore
 */
class XpanseAdapter
{
    /**
     * @var \xpanse\Payment\Model\Config
     */
    private $config;

    /**
     * @param \xpanse\Payment\Model\Config $config
     */
    public function __construct(
        \xpanse\Payment\Model\Config $config
    ) {
        $this->config = $config;
        xpanseSDK\Config::initialise($this->config->getSecretKey(), $this->config->getEnv());
    }

    /**
     * @param $data
     * @return array|mixed
     */
    public function chargeByToken($data)
    {
        $svc = new xpanseSDK\Charge();
        try {
            return $svc->CreateWithToken($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    /**
     * @param $data
     * @return array|mixed
     */
    public function getTransactionInfo($data)
    {
        $svc = new xpanseSDK\Charge();
        try {
            return $svc->Single($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    /**
     * @param $data
     * @return array|mixed
     * @throws \Exception
     */
    public function refund($data)
    {
        $svc = new xpanseSDK\Charge();
        try {
            return $svc->Refund($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function getCustomerPaymentMethods($data)
    {
        $svc = new xpanseSDK\Customer();
        try {
            return $svc->CustomerPaymentMethods($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function chargeByPaymentMethod($data)
    {
        $svc = new xpanseSDK\Charge();
        try {
            if ($data['Phone'] && substr($data['Phone'], 0, 1) == '0') {
                $data['Phone'] = '+61' . substr($data['Phone'], 1);
            }
            return $svc->CreateWithPaymentMethod($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function chargeByCustomerToken($data, $customerxpanseId)
    {
        try {
            if ($customerxpanseId) {
                return $this->chargeForExistXpanseCustomer($data, $customerxpanseId);
            } else {
                return $this->chargeForNewXpanseCustomer($data);
            }
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function getProvidersInfo($data)
    {
        $svc = new xpanseSDK\Info();

        try {
            return $svc->Providers($data);
        } catch (xpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    protected function chargeForExistXpanseCustomer($data, $customerxpanseId)
    {
        $customerSdk = new xpanseSDK\Customer();
        $chargeSdk = new xpanseSDK\Charge();

        $data['CustomerId'] = $customerxpanseId;
        if ($data['xpanseSaveMyPayment']) {
            $response = $customerSdk->CreatePaymentMethodWithToken($data);
            $data['PaymentMethodId'] = $response['paymentMethodId'];
            return $chargeSdk->CreateWithPaymentMethod($data);
        } else {
            // login customer but use a new card
            //return $chargeSdk->CreateWithCustomer($data); # issue charge a default card
            return $chargeSdk->CreateWithToken($data);
        }
    }

    protected function chargeForNewXpanseCustomer($data)
    {
        $chargeSdk = new xpanseSDK\Charge();

        if ($data['Phone'] && substr($data['Phone'], 0, 1) == '0') {
            $data['Phone'] = '+61' . substr($data['Phone'], 1);
        }

        if ($data['xpanseSaveMyPayment']) {
            $customerSdk = new xpanseSDK\Customer();
            $response = $customerSdk->CreateWithToken($data);
            $data['CustomerId'] = $response['customerId'];
            return $chargeSdk->CreateWithCustomer($data);
        } else {
            // not save card
            return $chargeSdk->CreateWithToken($data);
        }
    }
}
