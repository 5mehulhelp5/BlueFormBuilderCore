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
	 * @param \Magento\Framework\App\Action\Context                                  $context                     
	 * @param \Cytracon\Core\Helper\Data                                              $coreHelper                  
	 * @param \Cytracon\BlueFormBuilderCore\Model\EmailNotification                          $emailNotification           
	 * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory 
	 */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Model\EmailNotification $emailNotification,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
    ) {
        parent::__construct($context);
		$this->coreHelper                  = $coreHelper;
		$this->emailNotification           = $emailNotification;
		$this->submissionCollectionFactory = $submissionCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
    	try {
			$post = $this->getRequest()->getPostValue();
			if (isset($post['key']) && $post['key']) {
				$collection = $this->submissionCollectionFactory->create();
				$collection->addFieldToFilter('submission_hash', $post['key']);
				$submission = $collection->getFirstItem();
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

                    $this->emailNotification->setAttachments(
                        $this->_attachments
                    )->setSubmission(
                        $submission
                    )->sendEmail();

		            return;
				}
			}
		} catch (\Exception $e) {
		}
    }
}