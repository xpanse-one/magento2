<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xpanse\Payment\Gateway\Response;

use Xpanse\Payment\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerDataHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    private $customerRepository;

    /**
     * TransactionDataHandler constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->subjectReader = $subjectReader;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        if ($paymentDO->getPayment() instanceof Payment) {

            $transaction = $this->subjectReader->readTransaction($response);

            // set customerId from Xpanse
            $order = $paymentDO->getOrder();

            $customerId = $order->getCustomerId();

            $isLoginCustomer = (bool)$customerId;
            if ($isLoginCustomer) {
                try {
                    $customer = $this->customerRepository->getById($customerId);
                    $customerXpanseId = $customer->getCustomAttribute('xpanse_payment_id') ?
                        $customer->getCustomAttribute('xpanse_payment_id')->getValue() : '';
                    if (!$customerXpanseId && isset($transaction['customerId'])) {
                        $customer->setCustomAttribute('xpanse_payment_id', $transaction['customerId']);
                        $this->customerRepository->save($customer);
                    }
                } catch (\Exception $e) {
                    // cannot get customer
                }
            }
        }
    }
}
