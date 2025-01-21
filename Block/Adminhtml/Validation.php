<?php
namespace xpanse\Payment\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Validation extends Field
{
    /**
     * @inheritDoc
     */
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        // Return empty label
        return '';
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        // Replace field markup with validation button
        $title = __('Validate Credentials');
        $envId = 'select-groups-xpanse-fields-env-value';
        $storeId = 0;

        if ($this->getRequest()->getParam('website')) {
            $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
            if ($website->getId()) {
                /** @var Store $store */
                $store = $website->getDefaultStore();
                $storeId = $store->getStoreId();
            }
        }

        $endpoint = $this->getUrl('xpanse/configuration/validate', ['storeId' => $storeId]);

        return <<<TEXT
            <button
                type="button"
                title="{$title}"
                class="button"
                onclick="xpanseValidator.call(this, '{$endpoint}', '{$envId}')">
                <span>{$title}</span>
            </button>
TEXT;
    }
}
