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
 * @package   Cytracon_BlueFormBuilderCore
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\BlueFormBuilderCore\Block\Adminhtml;

class AddForm extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory
     */
    protected $formCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context               
     * @param \Magento\Backend\Model\Auth\Session                              $authSession           
     * @param \Magento\Framework\Locale\ResolverInterface                      $localeResolver        
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory 
     * @param array                                                            $data                  
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context);
        $this->_authSession          = $authSession;
        $this->_localeResolver       = $localeResolver;
        $this->formCollectionFactory = $formCollectionFactory;
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [
            'bfb_form_newform',
            $this->getActive(),
            $this->_authSession->getUser()->getId(),
            $this->_localeResolver->getLocale()
        ];

        // Add additional key parameters if needed
        $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return 86400;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Form\Collection
     */
    public function getCollection()
    {
        return $this->formCollectionFactory->create();
    }
}
