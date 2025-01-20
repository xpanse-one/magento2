<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Xpanse\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTransaction
 */
abstract class AbstractTransaction implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * @var \Xpanse\Payment\Model\Adapter\XpanseAdapterFactory $adapterFactory
     */
    protected $adapterFactory;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        \Xpanse\Payment\Model\Adapter\XpanseAdapterFactory $adapterFactory
    ) {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $log = [
            'request' => $data,
            'client' => static::class
        ];
        $response['object'] = [];

        try {
            $response['object'] = $this->process($data);
        } catch (\Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = (array) $response['object'];
            $this->customLogger->debug($log);
        }

        return $response;
    }

    /**
     * Process http request
     * @param array $data
     * @return mixed
     * @throw \xpanse\Sdk\ResponseException
     */
    abstract protected function process(array $data);
}
