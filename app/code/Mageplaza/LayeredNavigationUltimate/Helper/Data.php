<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Helper;

use Mageplaza\LayeredNavigationPro\Helper\Data as AbstractData;

/**
 * Class Data
 * @package Mageplaza\LayeredNavigationPro\Helper
 */
class Data extends AbstractData
{
	const FIELD_SLIDER_TYPE = 'slider_type';
	const FIELD_DISPLAY_TYPE = 'display_type';
	const FIELD_DISPLAY_SIZE = 'display_size';
	const FIELD_DISPLAY_HEIGHT = 'display_height';

	const DEFAULT_ROUTE = 'products';

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
	 */
	public $attributes;

	/**
	 * @var \Magento\Catalog\Model\Product\Attribute\Repository
	 */
	public $attributeRepository;

	/**
	 * @var \Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory
	 */
	public $pageFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	public $storeManager;

	/**
	 * Data constructor.
	 * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributecollectionFactory
	 * @param \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
	 * @param \Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory $pageFactory
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributecollectionFactory,
		\Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository,
		\Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory $pageFactory,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		$this->attributes          = $attributecollectionFactory;
		$this->attributeRepository = $attributeRepository;
		$this->pageFactory         = $pageFactory;
		$this->storeManager        = $storeManager;
		parent::__construct($context, $objectManager, $storeManager);
	}

	/**
	 * Layer additional field on attribute edit page
	 *
	 * @return array
	 */
	public function getLayerAdditionalFields()
	{
		$fields = parent::getLayerAdditionalFields();

		return array_merge($fields, [
			self::FIELD_DISPLAY_TYPE,
			self::FIELD_DISPLAY_SIZE,
			self::FIELD_DISPLAY_HEIGHT
		]);
	}

	/**
	 * @return bool
	 */
	public function enableIonRangeSlider()
	{
		return ($this->getDesignConfig('slider_type') != 2);
	}

	/**
	 * @param string $code
	 * @param null $storeId
	 * @return mixed
	 */
	public function getDesignConfig($code = '', $storeId = null)
	{
		$code = ($code !== '') ? '/' . $code : '';

		return $this->getConfigValue('layered_navigation/design' . $code, $storeId);
	}

	/**
	 * Slider skin css file
	 *
	 * @return string
	 */
	public function getSliderSkinFile()
	{
		$sliderType = $this->getDesignConfig('slider_type');
		$fileName   = \Mageplaza\LayeredNavigationUltimate\Model\Config\Source\SliderType::getFileName($sliderType);

		return 'Mageplaza_LayeredNavigationUltimate/css/skin/' . $fileName;
	}

	/**
	 * @param \Mageplaza\LayeredNavigationUltimate\Model\ProductsPage $page
	 * @param $position
	 * @return bool
	 */
	public function canShowProductPageLink($page, $position)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$positionConfig = explode(',', $page->getData('position') ?: '');

		return in_array($position, $positionConfig);
	}

	/**
	 * @param \Mageplaza\LayeredNavigationUltimate\Model\ProductsPage $page
	 * @return string
	 */
	public function getProductPageUrl($page)
	{
		return $this->getBaseUrl() . $page->getData('route') . $this->getUrlSuffix();
	}

	/**
	 * @return mixed
	 */
	public function getBaseUrl()
	{
		return $this->storeManager->getStore()->getBaseUrl();
	}

	/**
	 * get all attributes code and frontend label
	 * @return array
	 */
	public function getAllAttributes()
	{
		$attributesData = $this->attributes->create()
			->addIsFilterableFilter()->getData();

		$allAttributes = [];
		if (!empty($attributesData)) {
			foreach ($attributesData as $item) {
				$attributeOptions = $this->getAttributeOptions($item['attribute_code']);
				if (!empty($attributeOptions)) {
					$allAttributes[$item['attribute_code']] = $item['frontend_label'];
				}
			}
		}

		return $allAttributes;
	}

	/**
	 * get attribute options by attribute code
	 * @param $attCode
	 * @return array
	 */
	public function getAttributeOptions($attCode)
	{
		$options = $this->attributeRepository->get($attCode)->getOptions();
		$result  = [];
		array_shift($options);

		foreach ($options as $option) {
			if ($option->getValue()) {
				$result[$attCode . '=' . $option->getValue()] = $option->getLabel();
			}
		}

		return $result;
	}

	/**
	 * get products page list
	 * @return array
	 */
	public function getProductsPageCollection()
	{
		$pages = $this->pageFactory->create()->getCollection()
			->addVisibleFilter()
			->addStoreFilter($this->storeManager->getStore()->getId());

		return $pages;
	}

	/**
	 * get products page by id
	 * @param $id
	 * @return \Mageplaza\LayeredNavigationUltimate\Model\ProductsPage | null
	 */
	public function getPageById($id)
	{
		$page = $this->pageFactory->create()->load($id);
		if ($page->getId()) {
			return $page;
		}

		return null;
	}

	/**
	 * @param $route
	 * @return \Mageplaza\LayeredNavigationUltimate\Model\ProductsPage |null
	 */
	public function getPageByRoute($route)
	{
		$page = $this->getProductsPageCollection()
			->addFieldToFilter('route', $route)
			->getFirstItem();

		if ($page->getId()) {
			return $page;
		}

		return null;
	}
}
