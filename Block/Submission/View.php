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

namespace Cytracon\BlueFormBuilderCore\Block\Submission;

class View extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'BlueFormBuilder_Core::submission/view.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context  
     * @param \Magento\Framework\Registry                      $registry 
     * @param array                                            $data     
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Cytracon\Core\Helper\Data $coreHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Get current submission
     *
     * @return \Cytracon\BlueFormBuilderCore\Model\Submission
     */
    public function getCurrentSubmission()
    {
        return $this->_coreRegistry->registry('current_submission');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $title = __('Submission Confirmed');

        $this->pageConfig->getTitle()->set($title);

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }

        return $this;
    }

    public function getValues()
    {
        $submission = $this->getCurrentSubmission();
        $form = $submission->getForm();
        $profile['elements'] = $this->coreHelper->unserialize($submission->getElements());
        $form->setProfile($this->coreHelper->serialize($profile));
        return $submission->getSimpleValues();
    }
}
