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

namespace Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Files;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory
     */
    protected $fileCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                              $context
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
    ) {
        parent::__construct($context);
        $this->fileCollectionFactory = $fileCollectionFactory;
    }

    /**
     * Delete backups mass action
     *
     * @return \Magento\Backend\App\Action
     */
    public function execute()
    {
        $fileIds = $this->getRequest()->getParam('ids', []);

        if (!is_array($fileIds) || !count($fileIds)) {
            return $this->_redirect('*/files');
        }

        try {
            $collection = $this->fileCollectionFactory->create();
            $collection->addFieldToFilter('file_id', ['in' => $fileIds]);

            $fileDeleted = 0;
            foreach ($collection as $_file) {
                $_file->delete();
                $fileDeleted++;
            }

            $this->messageManager->addSuccess(
                __('A total of %1 file(s) have been deleted.', $fileDeleted)
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $this->_redirect('*/files');
    }
}
