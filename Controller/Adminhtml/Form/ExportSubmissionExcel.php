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

namespace Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportSubmissionExcel extends \Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cytracon_BlueFormBuilderCore::form';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context              $context     
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $dateTime    
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->dateTime     = $dateTime;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * Export products most viewed report to XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $form     = $this->_initForm();
        $date     = $this->dateTime->date('Y-m-d_H-i-s');
        $fileName = $form->getName() . '- Submissions - ' . $date . '.xml';
        $grid = $this->_view->getLayout()->createBlock(\Cytracon\BlueFormBuilderCore\Block\Adminhtml\Form\SubmissionGrid::class);
        return $this->_fileFactory->create(
            $fileName,
            $grid->getExcelFile($fileName),
            DirectoryList::VAR_DIR
        );
    }
}
