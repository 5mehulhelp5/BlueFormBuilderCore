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

namespace Cytracon\BlueFormBuilderCore\Block;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Cytracon_BlueFormBuilderCore::form/view.phtml';

    /**
     * @var Cytracon\BlueFormBuilderCore\Model\Form
     */
    protected $_form;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Cytracon\Builder\Helper\Data
     */
    protected $builderHelper;

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
     * @param \Magento\Framework\Registry                      $coreRegistry  
     * @param \Cytracon\Core\Helper\Data                        $coreHelper    
     * @param \Cytracon\Builder\Helper\Data                     $builderHelper 
     * @param \Cytracon\BlueFormBuilderCore\Helper\Data                $dataHelper    
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form                $formHelper    
     * @param array                                            $data          
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $coreRegistry,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\Builder\Helper\Data $builderHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext   = $httpContext;
        $this->coreRegistry  = $coreRegistry;
        $this->coreHelper    = $coreHelper;
        $this->builderHelper = $builderHelper;
        $this->dataHelper    = $dataHelper;
        $this->formHelper    = $formHelper;
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
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     * @since 100.2.0
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    public function _toHtml()
    {
        if (!$this->dataHelper->isEnabled()) return;

        $form   = $this->getCurrentForm();
        $formId = $this->getData('form_id');

        if (!$form) {
            if ($formId) {
                $form = $this->formHelper->loadForm($formId);
            } else if ($code = $this->getData('code')) {
                $form = $this->formHelper->loadForm($code);
            }
            $this->setCurrentForm($form);
        }

        if ($form && $form->getId()) {
            return parent::_toHtml();
        }
        return;
    }

    /**
     * @param \Cytracon\BlueFormBuilderCore\Model\Form $form
     */
    public function setCurrentForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Get current form
     *
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getCurrentForm()
    {
        return $this->_form;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Get current form
     *
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getCurrentSubmission()
    {
        return $this->coreRegistry->registry('current_submission');
    }

    /**
     * @return string
     */
    public function getProfileHtml()
    {
        $form  = $this->getCurrentForm();
        $profile = str_replace(',"enable_cache":true', '', $form->getData('profile'));
        $block = $this->builderHelper->prepareProfileBlock(\Cytracon\Builder\Block\Profile::class, $profile);
        $block->addGlobalData('form', $form);
        return $block->toHtml();
    }

    /**
     * @return array
     */
    public function getFormElements()
    {
        $result   = [];
        $form     = $this->getCurrentForm();
        $elements = $form->getElements();
        foreach ($elements as $element) {
            $result[] = [
                'name' => $element->getElemName(),
                'id'   => $element->getElemId()
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getMageScript()
    {
        $element        = $this->getElement();
        $form           = $this->getCurrentForm();
        $formId         = $form->getHtmlId();
        $jsBeforeSubmit = $form->getJsBeforeSubmit();
        $jsAfterSubmit  = $form->getJsAfterSubmit();
        $submission     = $this->getCurrentSubmission();
        $id             = $form->getRandomId();
        $result['Cytracon_BlueFormBuilderCore/js/form'] = [
            'formElements'       => $this->getFormElements(),
            'validCurrentPage'   => true,
            'ajaxLoadSectionUrl' => $this->getUrl('blueformbuilder/section/load'),
            'beforeJsSelector'   => $jsBeforeSubmit ? ".bfb-form-" . $id . "beforesubmit" : '',
            'afterJsSelector'    => $jsAfterSubmit ? ".bfb-form-" . $id . "aftersubmit" : '',
            'successUrl'         => $this->getUrl('blueformbuilder/form/success'),
            'submissionId'       => $submission ? $submission->getId() : '',
            'submissionHash'     => $submission ? $submission->getSubmissionHash() : '',
            'reportUrl'          => $this->getUrl('blueformbuilder/form/ajax'),
            'key'                => $form->getBfbFormKey(),
            'loadDataUrl'        => $this->getUrl('blueformbuilder/form/loadData'),
            'saveProgressUrl'    => $this->getUrl('blueformbuilder/form/saveProgress'),
            'formId'             => $formId,
            'saveFormProgress'   => $form->getEnableAutosave() ? true : false
        ];
        return $result;
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->escapeUrl($this->getUrl('blueformbuilder/form/post'));
    }
}
