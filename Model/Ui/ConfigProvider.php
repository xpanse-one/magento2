<?php

namespace Xpanse\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Xpanse\Payment\Model\Xpanse;
use Xpanse\Payment\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;
use Xpanse\Payment\Model\Adapter\XpanseAdapter;

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
        XpanseAdapter $xpanseAdapter
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
            $customerXpanseId = $this->getCustomerXpanseId();

            if (!$customerXpanseId) {
                return false;
            }

            $data = [
                'CustomerId' => $customerXpanseId
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

    protected function getCustomerXpanseId()
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
