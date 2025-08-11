<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license.
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   Cytracon_BlueFormBuilderCore
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\BlueFormBuilderCore\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Cytracon\BlueFormBuilderCore\Model\File as FileModel;
use Magento\Framework\App\Filesystem\DirectoryList;

class EmailNotification extends DataObject
{
    const TYPE_ADMIN    = 'admin';
    const TYPE_CUSTOMER = 'customer';

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\Submission
     */
    protected $_submission;

    /**
     * @var array
     */
    protected $_templateVars;

    /**
     * @var array
     */
    protected $_submissionData = [];

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|null
     */
    protected $_customer;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\Form
     */
    protected $_form;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Email\Model\Template
     */
    protected $emailTemplate;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataProcessor;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Email\Model\TemplateFactory $emailTemplateFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor
     * @param CustomerViewHelper $customerViewHelper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Cytracon\Core\Helper\Data $coreHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        CustomerViewHelper $customerViewHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Filesystem\Io\File $file,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager    = $messageManager;
        $this->emailTemplate     = $emailTemplateFactory->create(['area' => 'frontend']);
        $designConfig            = $this->emailTemplate->getDesignConfig();
        $designConfig->setData('area', 'frontend');
        $this->emailTemplate->setDesignConfig($designConfig->getData());
        $this->_eventManager           = $eventManager;
        $this->customerRepository      = $customerRepository;
        $this->_resource               = $resource;
        $this->dataProcessor           = $dataProcessor;
        $this->customerViewHelper      = $customerViewHelper;
        $this->mediaDirectory          = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->customerRegistry        = $customerRegistry;
        $this->file                    = $file;
        $this->coreHelper              = $coreHelper;
        $this->transportBuilder        = $transportBuilder;
        $this->logger                  = $logger;
    }

    /**
     * @param \Cytracon\BlueFormBuilderCore\Model\Submission $submission
     * @return $this
     */
    public function setSubmission(\Cytracon\BlueFormBuilderCore\Model\Submission $submission)
    {
        $this->_submission = $submission;
        $this->_form = $submission->getForm();
        $this->setVariables($submission->getVariables());
        return $this;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Submission|null
     */
    public function getSubmission()
    {
        return $this->_submission;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form|null
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @return array|null
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function setVariables($variables)
    {
        $this->_variables = $variables;
        return $this;
    }

    /**
     * @return void
     */
public function sendEmail()
{
    $this->logger->debug('BlueFormBuilder EmailNotification: Starting sendEmail method');
    $form    = $this->getForm();
    $success = true;
    try {
        if ($form->getEnableCustomerNotification()
            && $form->getCustomerSenderEmail()
            && $form->getCustomerEmailBody()) {
            $this->sendCustomerNotification();
        }
        if ($form->getEnableNotification()
            && ($form->getRecipients() || $this->getAdminRecipientEmails())
            && $form->getEmailBody()) {
            $this->sendAdminNotification();
        }
    } catch (\Exception $e) {
        $success = false;
        $this->logger->error('BlueFormBuilder EmailNotification: SendEmail failed', [
            'error' => $e->getMessage()
        ]);
    }
    if ($success) {
        $this->_submissionData['is_active'] = 1;
    }
    $this->updateSubmission();
}


    /**
     * @return void
     */
    public function sendCustomerNotification()
    {
        $form    = $this->getForm();
        $subject = $this->getEmailSubject($form->getCustomerEmailSubject());
        $header  = $this->getEmailHtml($form->getCustomerEmailHeader());
        $footer  = $this->getEmailHtml($form->getCustomerFooterHeader());
        $body    = $header . $this->getEmailBody($form->getCustomerEmailBody()) . $footer;
        $emails  = $this->getCustomerRecipientEmails();
        if ($emails) {
            $attachments = $form->getCustomerAttachFiles() ? $this->getAttachments() : [];
            $this->send(
                static::TYPE_CUSTOMER,
                $form->getCustomerSenderName(),
                $form->getCustomerSenderEmail(),
                $emails,
                [],
                $form->getCustomerReplyTo(),
                $subject,
                $body,
                $attachments
            );
            $this->_submissionData['customer_send_count'] = $this->getSubmission()->getCustomerSendCount() + 1;
        }
        $this->_submissionData['customer_email_subject'] = $subject;
        $this->_submissionData['customer_email_body']    = $body;
        $this->_submissionData['customer_recipients']    = implode(', ', $emails);
    }

    /**
     * @return array
     */
    private function getAdminRecipientEmails()
    {
        $form = $this->getForm();
        $recipients = explode(',', $form->getRecipients());
        if ($adminAdditionEmails = $this->getAdminAdditionEmails()) {
            $recipients = array_merge($recipients, $adminAdditionEmails);
        }
        $recipientEmails = [];
        foreach ($recipients as $_email) {
            $recipientEmails[] = trim($_email);
        }
        $conditionEmails = explode(',', $this->getSubmission()->getConditionEmails());
        if ($conditionEmails) {
            foreach ($conditionEmails as $_email) {
                $recipientEmails[] = trim($_email);
            }
        }
        return $this->prepareEmails($recipientEmails);
    }

    /**
     * @return array
     */
    private function getAdminAdditionEmails()
    {
        // Implement logic to get additional admin emails if needed
        return [];
    }

    /**
     * @return void
     */
    public function sendAdminNotification()
    {
        $form       = $this->getForm();
        $submission = $this->getSubmission();
        $recipientEmails     = $this->getAdminRecipientEmails();
        $recipientsBcc       = explode(',', $form->getRecipientsBcc());
        $recipientsBccEmails = [];
        foreach ($recipientsBcc as $_email) {
            $recipientsBccEmails[] = trim($_email);
        }
        $recipientsBccEmails = $this->prepareEmails($recipientsBccEmails);
        $subject             = $this->getEmailSubject($form->getEmailSubject());
        $header              = $this->getEmailHtml($form->getEmailHeader());
        $footer              = $this->getEmailHtml($form->getEmailFooter());
        $body                = $header . $this->getEmailBody($form->getEmailBody()) . $footer;
        if ($recipientEmails) {
            $attachments = $form->getAttachFiles() ? $this->getAttachments() : [];
            $this->send(
                static::TYPE_ADMIN,
                $submission->getSenderName(),
                $submission->getSenderEmail(),
                $recipientEmails,
                $recipientsBccEmails,
                $submission->getReplyTo(),
                $subject,
                $body,
                $attachments
            );
            $this->_submissionData['send_count'] = $this->getSubmission()->getSendCount() + 1;
        }
        $this->_submissionData['recipients']    = implode(',', $recipientEmails);
        $this->_submissionData['email_subject'] = $subject;
        $this->_submissionData['email_body']    = $body;
    }

    /**
     * Return file name from file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        return substr($pathFile, strrpos($pathFile, '/') + 1);
    }

    /**
     * @param string $type
     * @param string $senderName
     * @param string $senderEmail
     * @param array $recipientEmails
     * @param array $recipientBccEmails
     * @param string $replyTo
     * @param string $subject
     * @param string $body
     * @param array $attachments
     * @return bool
     */
    public function send($type, $senderName, $senderEmail, $recipientEmails, $recipientBccEmails, $replyTo, $subject, $body, $attachments = [])
    {
        $this->logger->debug('BlueFormBuilder EmailNotification: Starting send method', [
            'type' => $type,
            'sender_email' => $senderEmail,
            'recipients' => $recipientEmails,
            'bcc_recipients' => $recipientBccEmails
        ]);

        if ($senderEmail) {
            $this->inlineTranslation->suspend();
            try {
                $submission = $this->getSubmission();
                $transportBuilder = $this->transportBuilder;

                // Use safe file-based template and pass body as "message"
                $transportBuilder
                    ->setTemplateIdentifier('Magento_Email::email_template_simple')
                    ->setTemplateOptions([
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => (int)$submission->getStoreId()
                    ])
                    ->setTemplateVars([
                        'message'    => $body,
                        // keep extra vars for custom templates if needed
                        'email_body' => $body,
                        'subject'    => $subject,
                        'submission' => $submission,
                        'form'       => $this->getForm()
                    ])
                    ->setFromByScope([
                        'name'  => $senderName,
                        'email' => $senderEmail
                    ], (int)$submission->getStoreId());

                foreach ($recipientEmails as $email) {
                    if ($email) {
                        $transportBuilder->addTo($email);
                    }
                }
                foreach ($recipientBccEmails as $bccEmail) {
                    if ($bccEmail) {
                        $transportBuilder->addBcc($bccEmail);
                    }
                }
                if ($replyTo) {
                    $transportBuilder->setReplyTo($replyTo);
                } else {
                    $transportBuilder->setReplyTo($senderEmail);
                }

                // Set subject explicitly on the underlying message
                if (method_exists($transportBuilder, 'getMessage') && $subject) {
                    try {
                        $transportBuilder->getMessage()->setSubject($subject);
                    } catch (\Throwable $e) {
                        $this->logger->warning('BlueFormBuilder EmailNotification: Unable to set message subject directly', ['error' => $e->getMessage()]);
                    }
                }

                // Attachments using Message::createAttachment if available
                if ($attachments && method_exists($transportBuilder, 'getMessage')) {
                    $message = $transportBuilder->getMessage();
                    foreach ($attachments as $attachment) {
                        try {
                            if (empty($attachment['path']) || !is_file($attachment['path'])) {
                                $this->logger->error('BlueFormBuilder EmailNotification: Attachment file missing', [
                                    'path' => $attachment['path'] ?? '(empty)'
                                ]);
                                continue;
                            }
                            $fileName = $this->getFileFromPathFile($attachment['file']);
                            $content  = $this->file->read($attachment['path']);
                            $mimeType = !empty($attachment['mine_type']) ? $attachment['mine_type'] : 'application/octet-stream';

                            if (method_exists($message, 'createAttachment')) {
                                $part = $message->createAttachment($content, $mimeType, 'attachment', 'base64', $fileName);
                                // no-op; createAttachment already sets most headers
                                $this->logger->debug('BlueFormBuilder EmailNotification: Attachment added', [
                                    'file_name' => $fileName,
                                    'mime'      => $mimeType
                                ]);
                            } else {
                                $this->logger->warning('BlueFormBuilder EmailNotification: Message does not support attachments on this Magento version.');
                            }
                        } catch (\Throwable $e) {
                            $this->logger->error('BlueFormBuilder EmailNotification: Failed to add attachment', [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }

                $this->_eventManager->dispatch(
                    'blueformbuilder_before_send_email_notification',
                    ['submission' => $submission, 'type' => $type, 'obj' => $this, 'transport' => $transportBuilder]
                );

                $transport = $this->transportBuilder->getTransport();
                $this->logger->debug('BlueFormBuilder EmailNotification: Sending email...');
                $transport->sendMessage();
                $this->logger->debug('BlueFormBuilder EmailNotification: Email sent successfully');
            } catch (LocalizedException $e) {
                $this->logger->error('BlueFormBuilder EmailNotification Error: ' . $e->getMessage());
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->error('BlueFormBuilder EmailNotification General Error: ' . $e->getMessage());
                $this->messageManager->addErrorMessage(__('We can\'t send the email right now: %1', $e->getMessage()));
            }

            $this->inlineTranslation->resume();
        } else {
            $this->logger->error('BlueFormBuilder EmailNotification: No sender email provided');
        }
        return true;
    }

    /**
     * @return array
     */
    public function getCustomerRecipientEmails()
    {
        $variables = $this->getVariables();
        $elements  = $this->getForm()->getElements();
        $emails    = [];
        foreach ($elements as $element) {
            if ($element->getType() == 'bfb_email' && $element->getConfig('autoresponder') && isset($variables[$element->getConfig('elem_name')])) {
                $emails[] = $variables[$element->getConfig('elem_name')];
            }
        }
        return $this->prepareEmails($emails);
    }

    /**
     * @param array $emails
     * @return array
     */
    public function prepareEmails($emails)
    {
        $newEmails = [];
        foreach ($emails as $email) {
            if (!$email) {
                continue;
            }
            if ($preparedEmail = $this->prepareEmail($email)) {
                $newEmails[] = $preparedEmail;
            }
        }
        return $newEmails;
    }

    /**
     * @param string $email
     * @return string|null
     */
    public function prepareEmail($email)
    {
        $email = $this->processVariables(trim($email));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        return null;
    }

    /**
     * @param string $subject
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailSubject($subject)
    {
        $templateVars = $this->getTemplateVars();
        $template = $this->emailTemplate;
        $template->setTemplateType('html');
        $templateSubject = $this->processVariables($subject);
        $template->setTemplateSubject($templateSubject);
        return $template->getProcessedTemplateSubject($templateVars);
    }

    /**
     * @param string $content
     * @return string|null
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailHtml($content)
    {
        if (!$content) {
            return null;
        }
        $templateVars = $this->getTemplateVars();
        $template = $this->emailTemplate;
        $template->setTemplateType('html');
        $template->setTemplateText($content);
        return $template->getProcessedTemplate($templateVars);
    }

    /**
     * @param string $content
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailBody($content)
    {
        $templateVars = $this->getTemplateVars();
        $template = $this->emailTemplate;
        $template->setTemplateType('html');
        $templateSubject = $this->processVariables($content);
        $template->setTemplateText($templateSubject);
        return $template->getProcessedTemplate($templateVars);
    }

    /**
     * @return array
     */
    public function getTemplateVars()
    {
        if ($this->_templateVars === null) {
            $submission = $this->getSubmission();
            $vars['customer'] = $this->getCustomerData();
            $vars['store']    = $submission->getStore();
            $vars['form']     = $submission->getForm();
            $vars['product']  = $submission->getProduct();
            $this->_templateVars = $vars;
        }
        return $this->_templateVars;
    }

    /**
     * Retrieve customer model object
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerData()
    {
        if ($this->_customer === null) {
            $submission = $this->getSubmission();
            if ($submission->getCustomerId()) {
                try {
                    $customerData = $this->customerRepository->getById($submission->getCustomerId());
                    if ($customerData) {
                        $customer        = $this->getFullCustomerObject($customerData);
                        $this->_customer = $customer;
                    }
                } catch (\Exception $e) {
                    $this->logger->error('BlueFormBuilder EmailNotification: Failed to load customer data', [
                        'customer_id' => $submission->getCustomerId(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        return $this->_customer;
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Model\Data\CustomerSecure
     */
    private function getFullCustomerObject($customer)
    {
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function processVariables($content)
    {
        $variables = $this->getVariables();
        foreach ($variables as $name => $value) {
            $content = str_replace('[' . $name . ']', (!empty($value)) ? $value : '', $content);
        }
        return $this->coreHelper->filter($content);
    }

    /**
     * @return void
     */
    public function updateSubmission()
    {
        $submission = $this->getSubmission();
        if ($this->_submissionData && $submission->getId()) {
            $connection = $this->_resource->getConnection();
            $table      = $this->_resource->getTableName('mgz_blueformbuilder_submission');
            $where      = ['submission_id = ?' => $submission->getId()];
            $connection->update($table, $this->_submissionData, $where);
            $this->logger->debug('BlueFormBuilder EmailNotification: Submission updated', [
                'submission_id' => $submission->getId(),
                'data' => $this->_submissionData
            ]);
        }
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        if (!$this->hasData('attachments')) {
            $directory = $this->mediaDirectory->getAbsolutePath(FileModel::UPLOAD_FOLDER);
            $attachments = [];
            $fileCollection = $this->getSubmission()->getFileCollection();
            foreach ($fileCollection as $file) {
                $data = $file->getData();
                $data['path'] = $directory . $file->getFile();
                $this->logger->debug('BlueFormBuilder EmailNotification: Attachment path', [
                    'path' => $data['path']
                ]);
                if (!file_exists($data['path'])) {
                    $this->logger->error('BlueFormBuilder EmailNotification: Attachment file missing', [
                        'path' => $data['path']
                    ]);
                }
                $attachments[] = $data;
            }
            $this->setData('attachments', $attachments);
        }
        return $this->getData('attachments');
    }
}