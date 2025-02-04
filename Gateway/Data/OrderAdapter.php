<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace xpanse\Payment\Gateway\Data;

use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Model\Order;

/**
 * Class OrderAdapter
 */
class OrderAdapter implements OrderAdapterInterface
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var \Magento\Payment\Gateway\Data\Order\AddressAdapter
     */
    private $addressAdapterFactory;

    /**
     * @param Order $order
     * @param \Magento\Payment\Gateway\Data\Order\AddressAdapterFactory $addressAdapterFactory
     */
    public function __construct(
        Order $order,
        \Magento\Payment\Gateway\Data\Order\AddressAdapterFactory $addressAdapterFactory
    ) {
        $this->order = $order;
        $this->addressAdapterFactory = $addressAdapterFactory;
    }

    /**
     * Returns currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->order->getBaseCurrencyCode();
    }

    /**
     * Returns order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->order->getIncrementId();
    }

    /**
     * Returns customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->order->getCustomerId();
    }

    /**
     * Returns billing address
     *
     * @return AddressAdapterInterface|null
     */
    public function getBillingAddress()
    {
        return $this->order->getBillingAddress();
    }

    /**
     * Returns shipping address
     *
     * @return AddressAdapterInterface|null
     */
    public function getShippingAddress()
    {
        return $this->order->getShippingAddress();
    }

    /**
     * Returns order store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->order->getStoreId();
    }

    /**
     * Returns order id
     *
     * @return int
     */
    public function getId()
    {
        return $this->order->getEntityId();
    }

    /**
     * Returns order grand total amount
     *
     * @return float|null
     */
    public function getGrandTotalAmount()
    {
        return $this->order->getBaseGrandTotal();
    }

    /**
     * Returns list of line items in the cart
     *
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getItems()
    {
        return $this->order->getItems();
    }

    /**
     * Gets the remote IP address for the order.
     *
     * @return string|null Remote IP address.
     */
    public function getRemoteIp()
    {
        return $this->order->getRemoteIp();
    }

    /**
     * Get Actual Order Currency Code
     *
     * @return float|string|null
     */
    public function getOrderCurrentCode()
    {
        return $this->order->getOrderCurrencyCode();
    }

    /**
     * Get Actual Order Grand Total
     * @return float|null
     */
    public function getOrderGrandTotal()
    {
        return $this->order->getGrandTotal();
    }

    /**
     * get customer email
     * @return float|string|null
     */
    public function getCustomerEmail()
    {
        return $this->order->getCustomerEmail();
    }

    public function getCustomerFirstname()
    {
        return $this->order->getCustomerFirstname();
    }

    public function getCustomerLastname()
    {
        return $this->order->getCustomerLastname();
    }

    public function getShippingAmount()
    {
        return $this->order->getShippingAmount();
    }
}
