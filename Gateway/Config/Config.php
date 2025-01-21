<?php

namespace xpanse\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $methodCode = 'xpanse',
        $pathPattern = \Magento\Payment\Gateway\Config\Config::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }
}
