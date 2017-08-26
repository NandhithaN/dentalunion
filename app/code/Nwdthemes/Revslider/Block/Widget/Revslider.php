<?php

namespace Nwdthemes\Revslider\Block\Widget;

class Revslider extends \Nwdthemes\Revslider\Block\Revslider implements \Magento\Widget\Block\BlockInterface {

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
		\Nwdthemes\Revslider\Helper\Plugin $pluginHelper,
		\Nwdthemes\Revslider\Model\Revslider\RevSliderFront $revsliderFront,
		array $data = []
	) {
        parent::__construct(
            $context,
            $frameworkHelper,
            $pluginHelper,
            $revsliderFront,
            $data
        );
	}

}