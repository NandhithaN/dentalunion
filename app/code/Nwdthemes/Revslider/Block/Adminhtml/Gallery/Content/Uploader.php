<?php

namespace Nwdthemes\Revslider\Block\Adminhtml\Gallery\Content;

class Uploader extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content\Uploader {

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage,
        array $data = []
    ) {
        parent::__construct($context, $fileSize, $imagesStorage, $data);
    }

    protected function _construct() {
        parent::_construct();
        $type = $this->_getMediaType();
        $allowed = $this->_imagesStorage->getAllowedExtensions($type);
        $labels = [];
        $files = [];
        foreach ($allowed as $ext) {
            $labels[] = '.' . $ext;
            $files[] = '*.' . $ext;
        }
        $this->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('nwdthemes_revslider/*/upload', ['type' => $type])
        )->setFileField(
            'image'
        )->setFilters(
            ['images' => ['label' => __('Images (%1)', implode(', ', $labels)), 'files' => $files]]
        );
    }

}
