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

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportSubmission extends \Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form
{
    /**
     * Additional path to folder
     *
     * @var string
     */
    protected $_path = 'export';

    /**
     * @var array
     */
    protected $_headerKey = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $_directory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\Collection
     */
    protected $collection;

    /**
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Filesystem                    $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $dateTime
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->_filesystem  = $filesystem;
        $this->_directory   = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->_fileFactory = $fileFactory;
        $this->dateTime     = $dateTime;
    }

    public function setSubmissionCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    public function getSubmissionCollection()
    {
        return $this->collection;
    }

    /**
     * Export customer grid to CSV format
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $form = $this->_initForm(true);

        if (!$form->getId()) {
            $this->messageManager->addError(__('This form no longer exists.'));
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $date     = $this->dateTime->date('Y-m-d_H-i-s');
        $fileName = $form->getName() . '- Submissions - ' . $date . '.csv';
        return $this->_fileFactory->create(
            $fileName,
            $this->getCsvFile($fileName, $form),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Edit form page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function getCsvFile($fileName, $form)
    {

        $this->setSubmissionCollection($form->getSubmissionCollection());

        $file = $this->_path . '/' . $fileName . '.csv';

        $this->_directory->create($this->_path);
        $stream = $this->_directory->openFile($file, 'w+');

        $stream->writeCsv($this->_getExportHeaders($form));
        $stream->lock();
        $this->writeRow($form, $stream);
        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true
        ];
    }

    /**
     * Retrieve Headers row array for Export
     *
     * @return string[]
     */
    protected function _getExportHeaders($form)
    {
        $row      = [];
        $elements = $form->getElements();
        foreach ($elements as $element) {
            $row[] = $element->getConfig('label');
            $this->_headerKey[] = $element->getElemName();
        }
        return $row;
    }

    /**
     * Retrieve Totals row array for Export
     *
     * @return string[]
     */
    protected function writeRow($form, $stream)
    {
        $collection = $form->getSubmissionCollection();
        $row = [];
        foreach ($collection as $submission) {
            $values = $submission->getSimpleValues();
            $_row   = [];
            foreach ($this->_headerKey as $key) {
                $_row[] = isset($values[$key]) ? $values[$key] : '';
            }
            $stream->writeCsv($_row);
            $row[] = $_row;
        }
        return $row;
    }
}
