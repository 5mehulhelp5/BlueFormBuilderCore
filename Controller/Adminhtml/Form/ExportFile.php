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

namespace Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportFile extends \Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param \Magento\Backend\App\Action\Context              $context     
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory 
     * @param \Cytracon\Core\Helper\Data                        $coreHelper  
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Cytracon\Core\Helper\Data $coreHelper
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->coreHelper   = $coreHelper;
    }

    /**
     * Export customer grid to CSV format
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $formId = (int) $this->getRequest()->getParam('id');
        $form   = $this->_initForm(true);

        if (!$form->getId()) {
            $this->messageManager->addError(__('This form no longer exists.'));
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $data = $form->getData();
        unset($data['bfb_form_key']);
        unset($data['form_id']);


        foreach ($data as $key => &$value) {
            $value = $this->coreHelper->unserialize($value);
        }
        $fileContent = $this->coreHelper->serialize($data);
        return $this->_fileFactory->create(
            $form->getName() . '.json',
            $fileContent,
            DirectoryList::VAR_DIR
        );
    }
}
