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

namespace Cytracon\BlueFormBuilderCore\Block\Element;

class Address extends Control
{
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                 $context                  
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory 
     * @param \Magento\Directory\Helper\Data                                   $directoryHelper          
     * @param array                                                            $data                     
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->directoryHelper           = $directoryHelper;
    }

    public function getCountryCollection()
    {
        return $this->_countryCollectionFactory->create()->loadByStore();
    }

    public function getCountrySelected()
    {
        $result = $this->directoryHelper->getDefaultCountry();
        if (isset($defaultValue['country'])) $result = $defaultValue['country'];
        return $result;
    }
}