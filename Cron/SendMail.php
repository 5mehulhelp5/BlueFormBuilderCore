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

namespace Cytracon\BlueFormBuilderCore\Cron;

class SendMail
{
    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\EmailNotificationFactory
     */
    protected $emailNotificationFactory;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory
     */
    protected $submissionCollectionFactory;

    /**
     * @param \Cytracon\BlueFormBuilderCore\Model\EmailNotificationFactory                   $emailNotificationFactory    
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory 
     */
    public function __construct(
        \Cytracon\BlueFormBuilderCore\Model\EmailNotificationFactory $emailNotificationFactory,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
    ) {
        $this->emailNotificationFactory = $emailNotificationFactory;
        $this->submissionCollectionFactory = $submissionCollectionFactory;
    }

    /**
     * Delete un-usued attachments
     *
     * @return void
     */
    public function execute()
    {
        $ids        = [];
        $collection = $this->submissionCollectionFactory->create();
        foreach ($collection as $submission) {
            if ($submission->getAdminNotification() && !$submission->getSendCount()) {
                $emailNotification = $this->emailNotificationFactory->create();
                $emailNotification->setSubmission($submission)->sendAdminNotification();
                $emailNotification->updateSubmission();
            }
            if ($submission->getEnableCustomerNotification() && !$submission->getCustomerSendCount()) {
                $emailNotification = $this->emailNotificationFactory->create();
                $emailNotification->setSubmission($submission)->sendCustomerNotification();
                $emailNotification->updateSubmission();
            }
        }
    }
}
