<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   BlueFormBuilder_Core
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\BlueFormBuilderCore\Block;

class TopLinks extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'toplink.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory
     */
    protected $formCollectionFactory;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Form
     */
    protected $formHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                 $context               
     * @param \Magento\Framework\App\Http\Context                              $httpContext           
     * @param \Magento\Customer\Model\Session                                  $customerSession       
     * @param \Cytracon\Core\Helper\Data                                        $coreHelper            
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory 
     * @param \Cytracon\BlueFormBuilderCore\Helper\Data                                $dataHelper            
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form                                $formHelper            
     * @param array                                                            $data                  
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory,
        \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper,
        array $data = []
    ) {
        parent::__construct($context);
        $this->httpContext           = $httpContext;
        $this->customerSession       = $customerSession;
        $this->coreHelper            = $coreHelper;
        $this->formCollectionFactory = $formCollectionFactory;
        $this->dataHelper            = $dataHelper;
        $this->formHelper            = $formHelper;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }

        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags'     => [\Cytracon\BlueFormBuilderCore\Model\Form::CACHE_TAG]
            ]
        );
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'BLUEFORMBUILDER_FORM',
            $this->_storeManager->getStore()->getId(),
            (int)$this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            $this->getCustomerGroupId(),
            $this->coreHelper->serialize($this->getData()),
            'template' => $this->getTemplate()
        ];
    }

    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->dataHelper->isEnabled()) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\Collection
     */
    public function getCollection()
    {
        $store   = $this->_storeManager->getStore();
        $groupId = $this->customerSession->getCustomerGroupId();

        $collection = $this->formCollectionFactory->create();
        $collection->addFieldToFilter('show_toplink', 1)
        ->addFieldToFilter('disable_form_page', 0)
        ->addIsActiveFilter()
        ->addStoreFilter($store)
        ->addCustomerGroupFilter($groupId)
        ->setOrder('position', 'ASC');

        return $collection;
    }
}
