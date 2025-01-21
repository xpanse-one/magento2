<?php

namespace xpanse\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use xpanse\Payment\Model\xpanse;
use xpanse\Payment\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;
use xpanse\Payment\Model\Adapter\xpanseAdapter;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'xpanse';
    protected $config;
    protected $checkoutSession;
    protected $xpanseAdapter;
    protected $savedPaymentMethods = [];

    public function __construct(
        Config $config,
        CheckoutSession $checkoutSession,
        xpanseAdapter $xpanseAdapter
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->xpanseAdapter = $xpanseAdapter;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        if ($this->config->isActive() && $this->getCurrentPublicKey()) {
            return [
                'payment' => [
                    'xpanse' => [
                        'enabled' => $this->config->isActive(),
                        'debug' => $this->config->isDebug(),
                        'env' => $this->config->getEnv(),
                        'enableGooglePay' => $this->config->isGooglePay(),
                        'enableApplePay' => $this->config->isApplePay(),
                        'publicKey' => $this->getCurrentPublicKey(),
                        'title' => $this->config->getTitle(),
                        'getSavedPayments' => $this->getSavedPayments(),
                        'xpanseSaveMyPayment' => false,
                        'providersInfo' => $this->getProvidersInfo(),
                    ]
                ]
            ];
        }
        return [];
    }

    protected function getCurrentPublicKey()
    {
        if ($this->config->getEnv() == 'production') {
            return $this->config->getLivePublicKey();
        } else {
            return $this->config->getSandboxPublicKey();
        }
    }

    protected function getSavedPayments()
    {
        if (empty($this->savedPaymentMethods)) {
            $customerxpanseId = $this->getCustomerxpanseId();

            if (!$customerxpanseId) {
                return false;
            }

            $data = [
                'CustomerId' => $customerxpanseId
            ];

            $savedMethods = $this->xpanseAdapter->getCustomerPaymentMethods($data);
            if ($savedMethods && !isset($savedMethods['errorCode'])) {
                foreach ($savedMethods as $number => $method) {
                    $this->savedPaymentMethods[$number] = [
                        'paymentMethodId' => $method['paymentMethodId'],
                        'cardNumber' => $method['card']['cardNumber'],
                        'cardType' => $method['card']['type'],
                        'cardHolder' => $method['card']['cardholder']
                    ];
                }
            }
        }

        return $this->savedPaymentMethods;
    }

    protected function getCustomerxpanseId()
    {
        $customer = $this->checkoutSession->getQuote()->getCustomer();
        if ($customer->getId() != null) {
            return $customer->getCustomAttribute('xpanse_payment_id') ?
                $customer->getCustomAttribute('xpanse_payment_id')->getValue() : '';
        }
        return '';
    }

    protected function getProvidersInfo()
    {
        $quote = $this->checkoutSession->getQuote();
        $currency = $quote->getQuoteCurrencyCode();
        $total = $quote->getGrandTotal();
        return $this->xpanseAdapter->getProvidersInfo([
            'currency' => $currency,
            'amount' => $total,
        ]);
    }
}
