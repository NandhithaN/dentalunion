<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

class Ajax extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderFront $revSliderFront,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin $revSliderAdmin,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $pluginHelper->loadPlugins($frameworkHelper);

		parent::__construct($context);

        $revSliderAdmin->onAjaxAction();
	}

}
