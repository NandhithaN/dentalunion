<?php

namespace Nwdthemes\Revslider\Plugin;

use \Magento\Store\Model\ScopeInterface;

class RevsliderLayoutHandle {

    protected $_optionFactory;
    protected $_status;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Nwdthemes\Revslider\Model\OptionFactory $optionFactory
    ) {
        $this->_optionFactory = $optionFactory;
        $this->_status = $scopeConfig->getValue('nwdthemes_revslider/revslider_configuration/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function afterAddPageLayoutHandles(\Magento\Framework\View\Result\Page $resultPage) {
        if ($this->_status) {

            $option = $this->_optionFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('handle', 'revslider-global-settings')
                ->setPageSize(1)
                ->getFirstItem()
                ->getData('option');
            $settings = unserialize($option);

            $includeSlider = ! isset($settings['includes_globally']) || $settings['includes_globally'] == 'on';
            if ( ! $includeSlider && isset($settings['pages_for_includes'])) {
                $arrHandles = explode(',', $settings['pages_for_includes']);
                foreach ($arrHandles as $handle) {
                    if (trim($handle) == $resultPage->getDefaultLayoutHandle()) {
                        $includeSlider = true;
                    }
                }
            }

            if ($includeSlider) {
                $resultPage->addHandle('nwdthemes_revslider_default');
            }
        }
        return $resultPage;
    }

}