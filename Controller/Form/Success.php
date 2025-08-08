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

namespace Cytracon\BlueFormBuilderCore\Controller\Form;

class Success extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var array
	 */
	protected $_attachments = [];

	/**
	 * @var \Cytracon\Core\Helper\Data
	 */
	protected $coreHelper;

	/**
	 * @var \Cytracon\BlueFormBuilderCore\Model\EmailNotification
	 */
	protected $emailNotification;

	/**
	 * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory
	 */
	protected $submissionCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

	/**
	 * @param \Magento\Framework\App\Action\Context                                  $context                     
	 * @param \Cytracon\Core\Helper\Data                                              $coreHelper                  
	 * @param \Cytracon\BlueFormBuilderCore\Model\EmailNotification                          $emailNotification           
	 * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory 
	 */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Model\EmailNotification $emailNotification,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
		$this->coreHelper                  = $coreHelper;
		$this->emailNotification           = $emailNotification;
		$this->submissionCollectionFactory = $submissionCollectionFactory;
        $this->logger                      = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $this->logger->debug('BFB Success: entered', [
            'params' => $this->getRequest()->getParams()
        ]);
    	try {
			$post = $this->getRequest()->getPostValue();
			if (isset($post['key']) && $post['key']) {
                $this->logger->debug('BFB Success: has key', ['key' => $post['key'], 'submission_id' => $post['submission_id'] ?? null]);
				$collection = $this->submissionCollectionFactory->create();
				$collection->addFieldToFilter('submission_hash', $post['key']);
				$submission = $collection->getFirstItem();
                $this->logger->debug('BFB Success: loaded submission', ['id' => $submission->getId()]);
				if ($submission->getId() && (!$submission->getIsActive() || $submission->getId()==$post['submission_id'])) {
					$form = $submission->getForm();
					// Before Save
					$post = $this->coreHelper->unserialize($submission->getPost());
	                foreach ($form->getElements() as $element) {
                    	$val = isset($post[$element->getElemName()]) ? $post[$element->getElemName()] : '';
	                    $element->setForm($form);
	                    $element->setOrigValue($val);
	                    $element->prepareValue($val);
	                    $element->beforeSave();
	                }

	                // Prepare Values
	                foreach ($form->getElements() as $element) {
	                    if ($attachments = $element->getAttachments()) {
	                        foreach ($attachments as $_attachment) {
	                            $this->_attachments[] = $_attachment;
	                        }
	                    }
	                }
	                $submission->setAttachments($this->_attachments);

		            // After Save
	                foreach ($form->getElements() as $element) {
	                	$element->setSubmission($submission);
	                    $element->success();
	                }

                    $this->logger->debug('BFB Success: sending email...');
                    $this->emailNotification->setAttachments(
                        $this->_attachments
                    )->setSubmission(
                        $submission
                    )->sendEmail();
                    $this->logger->debug('BFB Success: email send invoked');

		            return;
				} else {
                    $this->logger->warning('BFB Success: submission not eligible', [
                        'id' => $submission->getId(),
                        'is_active' => $submission->getIsActive()
                    ]);
                }
			} else {
                $this->logger->warning('BFB Success: missing key in request');
            }
		} catch (\Exception $e) {
            $this->logger->error('BFB Success: exception', ['error' => $e->getMessage()]);
		}
    }
}