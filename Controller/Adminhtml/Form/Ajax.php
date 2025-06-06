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

use Magento\Store\Model\Store;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Ajax extends \Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form
{
    const UPLOAD_TMP = 'bfbimport';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Form
     */
    protected $formHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filesystem       $fileSystem
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form   $formHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper
    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
        $this->formHelper = $formHelper;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $postData = $this->getRequest()->getPostValue();
            switch ($postData['type']) {
                case 'blank':
                        $form = $this->formHelper->createNewForm(['name' => $postData['name']]);
                    break;

                case 'template':
                        $form = $this->importFromTemplate();
                    break;

                case 'duplicate':
                        $form = $this->duplicateForm();
                    break;

                case 'import':
                        $form = $this->importFromFile();
                    break;
            }

            if ($form) {
                $resultRedirect->setPath(
                    '*/*/edit',
                    ['form_id' => $form->getId()]
                );
            } else {
                $resultRedirect->setPath('*/*/');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            $resultRedirect->setPath('blueformbuilder/form');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the form.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form|false
     */
    protected function duplicateForm()
    {
        $postData = $this->getRequest()->getPostValue();
        if (isset($postData['form_id'])) {
            $form = $this->_objectManager->create(\Cytracon\BlueFormBuilderCore\Model\Form::class);
            $form->load($postData['form_id']);
            $newForm = $this->formHelper->duplicateForm($form, ['name' => $postData['name']]);
            $newForm->save();
            return $newForm;
        } else {
            return $this->formHelper->createNewForm(['name' => $postData['name']]);
        }
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form|false
     */
    protected function importFromFile()
    {
        $postData = $this->getRequest()->getPostValue();
        $uploader = $this->_objectManager->create(
            \Magento\MediaStorage\Model\File\Uploader::class,
            ['fileId' => 'formfile']
        );
        $uploader->setAllowRenameFiles(true);

        $varDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $uploadDir    = $varDirectory->getAbsolutePath(static::UPLOAD_TMP);
        $result       = $uploader->save($uploadDir);
        $fullPath     = $uploadDir . '/' . $result['file'];
        $form         = $this->formHelper->importForm(file_get_contents($fullPath), ['name' => $postData['name']]);

        if (file_exists($fullPath) && is_writable($fullPath)) {
            unlink($fullPath);
        }

        return $form;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form|false
     */
    protected function importFromTemplate()
    {
        $postData = $this->getRequest()->getPostValue();
        if (isset($postData['template'])) {
            $form = $this->formHelper->importTemplate($postData['template'], ['name' => $postData['name']]);
        } else {
            $form = $this->formHelper->createNewForm(['name' => $postData['name']]);
        }
        return $form;
    }
}
