<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xpanse\Payment\Model\Adapter;

use Xpanse\Sdk as XpanseSDK;

/**
 * Class XpanseAdapter
 * @codeCoverageIgnore
 */
class XpanseAdapter
{
    /**
     * @var \Xpanse\Payment\Model\Config
     */
    private $config;

    /**
     * @param \Xpanse\Payment\Model\Config $config
     */
    public function __construct(
        \Xpanse\Payment\Model\Config $config
    ) {
        $this->config = $config;
        XpanseSDK\Config::initialise($this->config->getSecretKey(), $this->config->getEnv());
    }

    /**
     * @param $data
     * @return array|mixed
     */
    public function chargeByToken($data)
    {
        $svc = new XpanseSDK\Charge();
        try {
            return $svc->CreateWithToken($data);
        } catch (XpanseSDK\ResponseException $exception) {
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
        $svc = new XpanseSDK\Charge();
        try {
            return $svc->Single($data);
        } catch (XpanseSDK\ResponseException $exception) {
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
        $svc = new XpanseSDK\Charge();
        try {
            return $svc->Refund($data);
        } catch (XpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function getCustomerPaymentMethods($data)
    {
        $svc = new XpanseSDK\Customer();
        try {
            return $svc->CustomerPaymentMethods($data);
        } catch (XpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function chargeByPaymentMethod($data)
    {
        $svc = new XpanseSDK\Charge();
        try {
            if ($data['Phone'] && substr($data['Phone'], 0, 1) == '0') {
                $data['Phone'] = '+61' . substr($data['Phone'], 1);
            }
            return $svc->CreateWithPaymentMethod($data);
        } catch (XpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function chargeByCustomerToken($data, $customerXpanseId)
    {
        try {
            if ($customerXpanseId) {
                return $this->chargeForExistXpanseCustomer($data, $customerXpanseId);
            } else {
                return $this->chargeForNewXpanseCustomer($data);
            }
        } catch (XpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    public function getProvidersInfo($data)
    {
        $svc = new XpanseSDK\Info();

        try {
            return $svc->Providers($data);
        } catch (XpanseSDK\ResponseException $exception) {
            return [
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode()
            ];
        }
    }

    protected function chargeForExistXpanseCustomer($data, $customerXpanseId)
    {
        $customerSdk = new XpanseSDK\Customer();
        $chargeSdk = new XpanseSDK\Charge();

        $data['CustomerId'] = $customerXpanseId;
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
        $chargeSdk = new XpanseSDK\Charge();

        if ($data['Phone'] && substr($data['Phone'], 0, 1) == '0') {
            $data['Phone'] = '+61' . substr($data['Phone'], 1);
        }

        if ($data['xpanseSaveMyPayment']) {
            $customerSdk = new XpanseSDK\Customer();
            $response = $customerSdk->CreateWithToken($data);
            $data['CustomerId'] = $response['customerId'];
            return $chargeSdk->CreateWithCustomer($data);
        } else {
            // not save card
            return $chargeSdk->CreateWithToken($data);
        }
    }
}
