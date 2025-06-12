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

namespace Cytracon\BlueFormBuilderCore\Block\Widget;

class Form extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\Form
     */
    protected $_form;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Form
     */
    protected $formHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Magento\Framework\App\Http\Context              $httpContext 
     * @param \Cytracon\Core\Helper\Data                        $coreHelper  
     * @param \Cytracon\BlueFormBuilderCore\Helper\Data                $dataHelper  
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form                $formHelper  
     * @param array                                            $data        
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->coreHelper  = $coreHelper;
        $this->dataHelper  = $dataHelper;
        $this->formHelper  = $formHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setTemplate('widget/form.phtml');
        parent::_construct();
        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags'     => [\Cytracon\BlueFormBuilderCore\Model\Form::CACHE_TAG
                ]
            ]
        );
    }

    public function toHtml()
    {
        $form = $this->getForm();
        if (!$form || !$form->getId()) {
            return;
        }
        return parent::toHtml();
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'BLUEFORMBUILDER_FORM_WIDGET',
            $this->_storeManager->getStore()->getId(),
            (int)$this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $this->coreHelper->serialize($this->getData()),
            'template' => $this->getTemplate()
        ];
    }

    public function getFormHtml()
    {
        $form = $this->getForm();
        if (!$form->getId()) {
            return;
        }
        $block = $this->getLayout()->createBlock('\Cytracon\BlueFormBuilderCore\Block\Form')
        ->setCode($form->getIdentifier())
        ->setCurrentForm($form);
        return $block->toHtml();
    }

    /**
     * @return Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getForm()
    {
        if ($this->_form === null) {
            $this->_form = $this->formHelper->loadForm($this->getData('code'));
        }

        return $this->_form;
    }

    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }
}
