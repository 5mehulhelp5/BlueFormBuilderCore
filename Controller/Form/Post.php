<?php
    /**
     * Cytracon
     *
     * This source file is subject to the Cytracon Software License, which is 
     * available at https://www.cytracon.com/license.
     * Do not edit or add to this file if you wish to upgrade the to newer 
     * versions in the future.
     * If you wish to customize this module for your needs.
     * Please refer to https://www.cytracon.com for more information.
     *
     * @category  BlueFormBuilder
     * @package   Cytracon_BlueFormBuilderCore
     * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
     */

    namespace Cytracon\BlueFormBuilderCore\Controller\Form;

    use Magento\Framework\Exception\LocalizedException;
    use \Magento\Framework\App\ObjectManager;
    // Added for email notification
    use Cytracon\BlueFormBuilderCore\Model\EmailNotification;

    class Post extends \Magento\Framework\App\Action\Action
    {
        /**
         * @var \Cytracon\BlueFormBuilderCore\Model\Form
         */
        protected $form;

        /**
         * @var \Cytracon\BlueFormBuilderCore\Model\Submission
         */
        protected $submission;

        /**
         * @var array
         */
        protected $values;

        /**
         * @var array
         */
        protected $_attachments = [];

        /**
         * @var \Magento\Store\Model\StoreManagerInterface
         */
        protected $_storeManager;

        /**
         * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
         */
        protected $remoteAddress;

        /**
         * @var \Magento\Framework\View\LayoutFactory
         */
        protected $layoutFactory;

        /**
         * @var \Magento\Framework\App\ResourceConnection
         */
        protected $_resource;

        /**
         * @var \Psr\Log\LoggerInterface
         */
        protected $logger;

        /**
         * @var \Magento\Captcha\Helper\Data
         */
        protected $captchaHelper;

        /**
         * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
         */
        protected $timezoneInterface;

        /**
         * @var \Magento\Directory\Model\CountryFactory
         */
        protected $countryFactory;

        /**
         * @var \Magento\Framework\HTTP\ClientInterface
         */
        protected $client;

        /**
         * @var \Cytracon\Core\Helper\Data
         */
        protected $coreHelper;

        /**
         * @var \Cytracon\Builder\Helper\Data
         */
        protected $builderHelper;

        /**
         * @var \Cytracon\BlueFormBuilderCore\Model\SubmissionFactory
         */
        protected $submissionFactory;

        /**
         * @var \Cytracon\BlueFormBuilderCore\Helper\Form
         */
        protected $formHelper;

        /**
         * @var \Cytracon\BlueFormBuilderCore\Helper\Data
         */
        protected $dataHelper;

        /**
         * @var 
         * \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory
         */
        protected $submissionCollectionFactory;

        /**
         * @var \Cytracon\BlueFormBuilderCore\Model\FormProcessor
         */
        protected $formProcessor;

        /**
         * @var array
         */
        protected $elements;

        /**
         * @var \Magento\Customer\Model\Session
         */
        protected $customerSession;

        /**
         * @var EmailNotification
         */
        protected $emailNotification;

        /**
         * Constructor with many injected dependencies.
         * Added EmailNotification as last parameter and property assignment.
         *
         * @param \Magento\Framework\App\Action\Context $context
         * @param \Magento\Store\Model\StoreManagerInterface $storeManager
         * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
         * @param \Magento\Customer\Model\Session $customerSession
         * @param \Magento\Framework\View\LayoutFactory $layoutFactory
         * @param \Magento\Framework\App\ResourceConnection $resource
         * @param \Psr\Log\LoggerInterface $logger
         * @param \Magento\Captcha\Helper\Data $captchaHelper
         * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
         * @param \Magento\Directory\Model\CountryFactory $countryFactory
         * @param \Magento\Framework\HTTP\ClientInterface $client
         * @param \Cytracon\Core\Helper\Data $coreHelper
         * @param \Cytracon\Builder\Helper\Data $builderHelper
         * @param \Cytracon\BlueFormBuilderCore\Model\SubmissionFactory $submissionFactory
         * @param \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper
         * @param \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper
         * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
         * @param \Cytracon\BlueFormBuilderCore\Model\FormProcessor $formProcessor
         * @param EmailNotification $emailNotification
         */
        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Framework\App\ResourceConnection $resource,
            \Psr\Log\LoggerInterface $logger,
            \Magento\Captcha\Helper\Data $captchaHelper,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
            \Magento\Directory\Model\CountryFactory $countryFactory,
            \Magento\Framework\HTTP\ClientInterface $client,
            \Cytracon\Core\Helper\Data $coreHelper,
            \Cytracon\Builder\Helper\Data $builderHelper,
            \Cytracon\BlueFormBuilderCore\Model\SubmissionFactory $submissionFactory,
            \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper,
            \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper,
            \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory,
            \Cytracon\BlueFormBuilderCore\Model\FormProcessor $formProcessor,
            EmailNotification $emailNotification
        ) {
            $this->_storeManager               = $storeManager;
            $this->remoteAddress               = $remoteAddress;
            $this->customerSession             = $customerSession;
            $this->layoutFactory               = $layoutFactory;
            $this->_resource                   = $resource;
            $this->logger                      = $logger;
            $this->captchaHelper               = $captchaHelper;
            $this->timezoneInterface           = $timezoneInterface;
            $this->countryFactory              = $countryFactory;
            $this->client                      = $client;
            $this->coreHelper                  = $coreHelper;
            $this->builderHelper               = $builderHelper;
            $this->submissionFactory           = $submissionFactory;
            $this->formHelper                  = $formHelper;
            $this->dataHelper                  = $dataHelper;
            $this->submissionCollectionFactory = $submissionCollectionFactory;
            $this->formProcessor               = $formProcessor;
            $this->emailNotification           = $emailNotification;
            parent::__construct($context);
        }

        /**
         * Set form
         *
         * @param \Cytracon\BlueFormBuilderCore\Model\Form $form
         */
        public function setForm(\Cytracon\BlueFormBuilderCore\Model\Form $form)
        {
            $this->form = $form;
            return $this;
        }

        /**
         * Get form
         *
         * @return \Cytracon\BlueFormBuilderCore\Model\Form
         */
        public function getForm()
        {
            return $this->form;
        }

        /**
         * Set submission
         *
         * @param \Cytracon\BlueFormBuilderCore\Model\Submission $submission
         */
        public function setSubmission(\Cytracon\BlueFormBuilderCore\Model\Submission $submission)
        {
            $this->submission = $submission;
            return $this;
        }

        /**
         * Get submission
         *
         * @return \Cytracon\BlueFormBuilderCore\Model\Submission $submission
         */
        public function getSubmission()
        {
            return $this->submission;
        }

        /**
         * Set values
         *
         * @param array $values
         */
        public function setValues($values)
        {
            $this->values = $values;
            return $this;
        }

        /**
         * Get values
         *
         * @return array
         */
        public function getValues()
        {
            return $this->values;
        }

        /**
         * Main execution method
         *
         * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\Result\Redirect
         */
        public function execute()
        {
            $post             = $this->getRequest()->getPostValue();
            $formKey          = $this->getRequest()->getParam('bfb_form_key', null);
            $result['status'] = false;
            $store            = $this->_storeManager->getStore();

            try {
                $form = $this->_initForm();
                $this->logger->debug('BFB Post: init form', ['form_id' => $form->getId(), 'form_key' => $formKey]);

                $post = $this->getFormPost();

                if ($post && $formKey) {
                    $this->_resource->getConnection()->beginTransaction();
                    $this->logger->debug('BFB Post: transaction started');

                    $this->_eventManager->dispatch(
                        'bfb_submission_post_save_before',
                        ['action' => $this]
                    );

                    $this->verifyMagento2Captcha();
                    $this->verifyReCaptcha();

                    if ($this->hasSubmitted()) {
                        $this->logger->warning('BFB Post: blocked by disable_multiple');
                        $result['message'] = $form->getDisableMultipleMessage();
                        $result['type']    = 'alert';
                        if ($this->getRequest()->isAjax()) {
                            $this->_resource->getConnection()->rollBack();
                            $this->logger->debug('BFB Post: rolled back (ajax)');
                            $this->getResponse()->representJson(
                                $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
                            );
                            return;
                        } else {
                            $this->_resource->getConnection()->rollBack();
                            $this->logger->debug('BFB Post: rolled back (redirect)');
                            $this->messageManager->addError($result['message']);
                            $redirectTo = $this->_redirect->getRefererUrl();
                            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                            return $resultRedirect;
                        }
                    }

                    // Before Save: prepare and validate each element
                    foreach ($this->getElements() as $element) {
                        $val = isset($post[$element->getElemName()]) ? $post[$element->getElemName()] : '';
                        $element->setForm($form);
                        $element->setOrigValue($val);
                        $element->setPost($this->getFormPost());
                        $element->prepareValue($val);
                        $element->beforeSave();
                    }

                    // Prepare Values and collect attachments
                    $values    = [];
                    foreach ($this->getElements() as $element) {
                        $values[$element->getElemName()] = [
                            'simple' => $element->getValue(),
                            'html'   => $element->getHtmlValue(),
                            'email'  => $element->getEmailHtmlValue()
                        ];
                        if ($attachments = $element->getAttachments()) {
                            foreach ($attachments as $_attachment) {
                                $this->_attachments[] = $_attachment;
                            }
                        }
                    }
                    $this->setValues($values);

                    // Save submission
                    $submission = $this->saveSubmission();
                    $this->logger->debug('BFB Post: submission saved', ['submission_id' => $submission->getId()]);

                    // After Save: allow elements to perform post-save actions
                    foreach ($this->getElements() as $element) {
                        $element->setSubmission($submission);
                        $element->afterSave();
                    }

                    // Handle conditions for redirects and emails
                    // Pass the sanitized post array to avoid calling getFormPost() internally
                    $actions = $this->getConditionAction($this->getFormPost());
                    if ($actions['redirect_to']) {
                        $form->setRedirectTo($actions['redirect_to']);
                    }
                    $redirectTo = $form->getRedirectTo();
                    if (!$redirectTo) {
                        $redirectTo = $store->getBaseUrl();
                    }
                    if ($redirectTo === '/') {
                        $redirectTo = null;
                    } else {
                        $redirectTo = $this->dataHelper->filter($redirectTo);
                    }
                    if (!$form->getRedirectDelay() && $redirectTo) {
                        $result['redirect'] = $redirectTo;
                    }
                    $form->setRedirectTo($redirectTo);

                    // Build response data
                    $result['message'] = $this->getSuccessMessage($redirectTo);
                    $result['status']  = true;
                    $result['key']     = $submission->getSubmissionHash();

                    // Clean up form process
                    $this->formProcessor->deleteFormProcess($form->getId());

                    // Dispatch post complete event
                    $this->_eventManager->dispatch(
                        'blueformbuilder_submission_post_complete',
                        ['submission' => $submission, 'form' => $form]
                    );

                    // Commit transaction
                    $this->_resource->getConnection()->commit();
                    $this->logger->debug('BFB Post: commit complete', ['submission_hash' => $result['key']]);

                    // Email sending: directly trigger notifications after commit
                    try {
                        $this->emailNotification
                            ->setAttachments($this->_attachments)->setForm($form)
                            ->setSubmission($submission)
                            ->sendEmail();
                        $this->logger->debug('BFB Post: Email Versand im Post-Controller angestoßen');
                    } catch (\Exception $e) {
                        $this->logger->error('BFB Post: Fehler beim E-Mail-Versand', ['error' => $e->getMessage()]);
                    }
                }
            } catch (LocalizedException $e) {
                $this->_resource->getConnection()->rollBack();
                $this->logger->error('BFB Post: LocalizedException', ['error' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->_resource->getConnection()->rollBack();
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.')
                );
            }

            // Return AJAX or redirect result
            if ($this->getRequest()->isAjax()) {
                $this->getResponse()->representJson(
                    $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
                );
                return;
            } else {
                $redirectTo = $this->_redirect->getRefererUrl();
                if (isset($form) && $form->getRedirectTo()) {
                    $redirectTo = $form->getRedirectTo();
                }
                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($redirectTo);
                return $resultRedirect;
            }
        }

        /**
         * Build cleaned post array
         *
         * @return array
         */
        public function getFormPost()
        {
            $form      = $this->getForm();
            $post      = $this->getRequest()->getPostValue();
            $postValue = [];
            foreach ($post as $_name => $val) {
                if ($val === 'bfbdisabled') {
                    continue;
                }
                $element = $form->getElement($_name, 'elem_name');
                if ($element) {
                    // Clean address fields to names
                    if ($element->getType() == 'bfb_address') {
                        if (isset($val['country'])) {
                            $country = $this->countryFactory->create()->loadByCode($val['country']);
                            $val['country'] = $country->getName();
                            $regionCollection = $country->getRegionCollection();
                            if (isset($val['state_id'])) {
                                if ($regionCollection->count()) {
                                    $region = $regionCollection->getItemById($val['state_id']);
                                    if ($region) {
                                        unset($val['state_id']);
                                        $val['state'] = $region->getName();
                                    }
                                } else {
                                    unset($val['state_id']);
                                }
                            }
                        }
                    }

                    // Remove potential scripts
                    if (is_array($val)) {
                        foreach ($val as &$_val) {
                            if (is_array($_val)) {
                                foreach ($_val as &$_val2) {
                                    if (is_string($_val2)) {
                                        $_val2 = $this->dataHelper->removeScript(trim($_val2));
                                    }
                                }
                            } else {
                                $_val = $this->dataHelper->removeScript(trim($_val));
                            }
                        }
                    } else {
                        $val = $this->dataHelper->removeScript(trim($val));
                    }

                    $postValue[$_name] = $val;
                    if (isset($post[$_name . '_others'])) {
                        $postValue[$_name . '_others'] = $this->dataHelper->removeScript(trim($post[$_name . '_others']));
                    }
                }
            }
            return $postValue;
        }

        /**
         * Initialize form instance from request data
         *
         * @return \Cytracon\BlueFormBuilderCore\Model\Form|false
         */
        protected function _initForm()
        {
            $formKey = $this->getRequest()->getParam('bfb_form_key');
            $form    = $this->formHelper->loadForm($formKey, 'bfb_form_key');

            if (!$form->getId()) {
                throw new LocalizedException(__('This form no longer exists.'));
            }

            $this->setForm($form);

            return $form;
        }

        /**
         * Get only used elements
         *
         * @return array
         */
        public function getElements($type = 'all')
        {
            if (!isset($this->elements[$type])) {
                $result   = [];
                $formPost = $this->getFormPost();
                $elements = $this->getForm()->getElements();
                foreach ($elements as $element) {
                    if (isset($formPost[$element->getElemName()])) {
                        $result[] = $element;
                    }
                }
                $this->elements[$type] = $result;
            }
            return $this->elements[$type];
        }

        /**
         * Verify Magento built‑in CAPTCHA
         */
        protected function verifyMagento2Captcha()
        {
            $post = $this->getRequest()->getPostValue();
            if (isset($post['captcha']) && is_array($post['captcha'])) {
                foreach ($post['captcha'] as $formId => $captchaString) {
                    $captchaModel = $this->captchaHelper->getCaptcha($formId);
                    if (!$captchaModel->isCorrect($captchaString)) {
                        throw new LocalizedException(__('Incorrect CAPTCHA.'));
                    }
                }
            }
        }

        /**
         * Verify reCAPTCHA v2/v3
         *
         * @return boolean
         */
        protected function verifyReCaptcha()
        {
            $form = $this->getForm();

            if ($form->getEnableRecaptcha()) {
                $this->verifyReCaptcha3();
            } else {
                $post      = $this->getRequest()->getPostValue();
                $valid     = true;
                $secretKey = $this->dataHelper->getConfig('recaptcha/secret_key');
                $publicKey = $this->dataHelper->getConfig('recaptcha/secret_key');
                $elements  = $form->getAllElements();
                $remoteIp  = $this->remoteAddress->getRemoteAddress();
                $url       = 'https://www.google.com/recaptcha/api/siteverify';

                if (!$secretKey || !$publicKey) {
                    return true;
                }
                foreach ($elements as $_element) {
                    if ($_element['type'] === 'bfb_recaptcha') {
                        $valid = false;
                        if (isset($post['g-recaptcha-response'])) {
                            $postData = http_build_query([
                                'secret'   => $secretKey,
                                'response' => $post['g-recaptcha-response'],
                                'remoteip' => $remoteIp
                            ]);
                            $this->client->post($url, $postData);
                            $response = $this->client->getBody();
                            $result   = json_decode($response);
                            $valid    = $result->success;
                        }
                        break;
                    }
                }

                if (!$valid) {
                    throw new LocalizedException(__('Incorrect CAPTCHA.'));
                }
                return $valid;
            }
        }

        private function verifyReCaptcha3()
        {
            $post      = $this->getRequest()->getPostValue();
            $secretKey = $this->dataHelper->getConfig('recaptcha3/secret_key');
            $remoteIp  = $this->remoteAddress->getRemoteAddress();
            if (isset($post['g-recaptcha-response'])) {
                $postData = http_build_query([
                    'secret'   => $secretKey,
                    'response' => $post['g-recaptcha-response'],
                    'remoteip' => $remoteIp
                ]);
                $this->client->post('https://www.google.com/recaptcha/api/siteverify', $postData);
                $response = $this->client->getBody();
                $result   = json_decode($response);
                if (!$result->success) {
                    throw new LocalizedException(__('Incorrect CAPTCHA.'));
                }
            }
        }

        /**
         * Check if user already submitted this form based on configured condition
         *
         * @return boolean
         */
        public function hasSubmitted()
        {
            $form  = $this->getForm();
            $valid = false;
            if ($form->getDisableMultiple()) {
                switch ($form->getDisableMultipleCondition()) {
                    case 'customer_id':
                        $customerId = $this->customerSession->getCustomerId();
                        if ($customerId) {
                            $collection = $this->submissionCollectionFactory->create();
                            $collection->addFieldToFilter('customer_id', $customerId);
                            $collection->addFieldToFilter('form_id', $form->getId());
                            if ($collection->getSize()) {
                                $valid = true;
                            }
                        }
                        break;

                    case 'ip_address':
                        $remoteId = $this->remoteAddress->getRemoteAddress();
                        $collection = $this->submissionCollectionFactory->create();
                        $collection->addFieldToFilter('remote_ip', $remoteId);
                        $collection->addFieldToFilter('form_id', $form->getId());
                        if ($collection->getSize()) {
                            $valid = true;
                        }
                        break;

                    case 'form_fields':
                        if ($fields = $form->getDisableMultipleFields()) {
                            $post       = $this->getFormPost();
                            $fields     = $this->coreHelper->unserialize($fields);
                            $collection = $this->submissionCollectionFactory->create();
                            $collection->addFieldToFilter('form_id', $form->getId());
                            foreach ($collection as $submission) {
                                $_valid = false;
                                $params = $this->coreHelper->unserialize($submission->getParams());
                                foreach ($fields as $k2 => $field) {
                                    $element = $form->getElement($field);
                                    if ($element) {
                                        $field = $element->getElemName();
                                        if (
                                            !isset($params[$field]) ||
                                            !isset($post[$field])   ||
                                            (isset($params[$field]) && isset($post[$field]) && (trim($params[$field]) != trim($post[$field])))
                                        ) {
                                            $_valid = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$_valid) {
                                    $valid = true;
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
            return $valid;
        }

        /**
         * Save submission to database
         *
         * @param  array $post
         * @return \Cytracon\BlueFormBuilderCore\Model\Submission
         */
        protected function saveSubmission()
        {
            $store     = $this->_storeManager->getStore();
            $post      = $this->getRequest()->getPostValue();
            $form      = $this->getForm();
            $profile   = $form->getProfile();
            $createdAt = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
            $data      = [];

            // Serialize main data
            $data['post']                     = $this->coreHelper->serialize($this->getFormPost());
            $data['values']                   = $this->coreHelper->serialize($this->getValues());
            $data['admin_submission_content'] = $this->getAdminSubmissionContent();
            $data['submission_content']       = $this->getSubmissionEmailContent();
            // Pass the sanitized post to getConditionAction to avoid internal calls
            $actions = $this->getConditionAction($this->getFormPost());
            $data['condition_emails'] = implode(',', $actions['emails']);

            $data['form_params']            = $this->coreHelper->serialize($form->getData());
            $data['form_id']                = $form->getId();
            $data['elements']               = $this->coreHelper->serialize($profile['elements']);
            $data['customer_id']            = $this->customerSession->getId();
            $data['remote_ip']              = $this->remoteAddress->getRemoteAddress();
            $data['remote_ip_long']         = $this->remoteAddress->getRemoteAddress(true);
            $data['creation_time']          = $createdAt;
            $data['product_id']             = isset($post['product_id']) ? (int)$post['product_id'] : '';
            $data['store_id']               = $store->getId();
            $data['submitted_page']         = $this->_redirect->getRefererUrl();
            $data['brower']                 = $this->getRequest()->getServer('HTTP_USER_AGENT');
            $data['sender_name']            = $this->processVariables($form->getSenderName());
            $data['sender_email']           = $this->processVariables($form->getSenderEmail());
            $data['reply_to']               = $this->processVariables($form->getReplyTo());
            $data['recipients']             = $this->processVariables($form->getRecipients());
            $data['recipients_bcc']         = $this->processVariables($form->getRecipientsBcc());
            $data['email_subject']          = '';
            $data['email_body']             = '';
            $data['customer_sender_name']   = $this->processVariables($form->getCustomerSenderName());
            $data['customer_sender_email']  = $this->processVariables($form->getCustomerSenderEmail());
            $data['customer_reply_to']      = $this->processVariables($form->getCustomerReplyTo());
            $data['customer_email_subject'] = '';
            $data['customer_email_body']    = '';
            $data['read']                   = \Cytracon\BlueFormBuilderCore\Model\Submission::STATUS_UNREAD;
            $data['admin_notification']     = $form->getEnableNotification();
            $data['customer_notification']  = $form->getEnableCustomerNotification();

            $submission = $this->submissionFactory->create();
            if (isset($post['submission_id'])) {
                $submission->load($post['submission_id']);
                if (!$submission->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('This submission no longer exists.')
                    );
                }
            }

            $data['form'] = $form;
            $submission->addData($data);

            $this->_eventManager->dispatch(
                'blueformbuilder_submission_before_save',
                ['submission' => $submission, 'post' => $post, 'action' => $this]
            );

            $submission->save();

            $this->_eventManager->dispatch(
                'blueformbuilder_submission_after_save',
                ['submission' => $submission, 'post' => $post, 'action' => $this]
            );

            $this->setSubmission($submission);

            return $submission;
        }

        /**
         * Determine conditional actions (emails, redirects)
         *
         * @param  array $post
         * @return array
         */
        /**
         * Determine conditional actions based on form conditions.
         * Accepts an optional $post array to avoid calling getFormPost() within
         * this method. If $post is null, it falls back to $this->getFormPost().
         *
         * @param array|null $post
         * @return array
         */
        protected function getConditionAction($post = null)
        {
            $emails     = [];
            $redirectTo = '';
            // Use provided $post if available; otherwise obtain sanitized post values
            $post       = $post ?? $this->getFormPost();
            $form       = $this->getForm();
            if ($form->getConditional()) {
                $conditional = $this->coreHelper->unserialize($form->getConditional());
                foreach ($conditional as $_row) {
                    if (isset($_row['conditions']) && $this->dataHelper->validateCondition($form, $_row['conditions'], $post)) {
                        if (isset($_row['actions'])) {
                            foreach ($_row['actions'] as $_row) {
                                if (isset($_row['action']) && ($_row['action'] == 'set' || $_row['action'] == 'rt')) {
                                    $_row['value'] = trim($_row['value']);
                                    if (($_row['action'] === 'set') && filter_var($_row['value'], FILTER_VALIDATE_EMAIL)) {
                                        $emails[] = $_row['value'];
                                    }
                                    if ($_row['action'] === 'rt' && $redirectTo == '') {
                                        $redirectTo = $this->coreHelper->filter($_row['value']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return [
                'emails'      => $emails,
                'redirect_to' => $redirectTo
            ];
        }

        /**
         * Replace variables in a content string with their values
         *
         * @param  string $content
         * @return string
         */
        public function processVariables($content)
        {
            $variables  = $this->getValues();
            $submission = $this->getSubmission();
            if ($submission) {
                $variables = array_merge($variables, $submission->getVariables());
                foreach ($variables as $name => $value) {
                    $content = str_replace('[' . $name . ']', !empty($value) ? $value : '', $content);
                    $content = str_replace('[<span>' . $name . '</span>]', !empty($value) ? $value : '', $content);
                }
            } else {
                foreach ($variables as $name => $element) {
                    if ($element['email']) {
                        $content = str_replace('[' . $name . ']', !empty($element['email']) ? $element['email'] : '', $content);
                        $content = str_replace('[<span>' . $name . '</span>]', !empty($element['email']) ? $element['email'] : '', $content);
                    }
                }
            }
            return $this->dataHelper->filter($content);
        }

        /**
         * Build the success message block
         *
         * @param  \Cytracon\BlueFormBuilderCore\Model\Form $form
         * @return string
         */
        public function getSuccessMessage($redirectTo)
        {
            $layout = $this->layoutFactory->create();
            $block  = $layout->createBlock('\Magento\Framework\View\Element\Template')->setTemplate('Cytracon_BlueFormBuilderCore::success.phtml');
            $block->setForm($this->getForm());
            $block->setRedirectTo($redirectTo);
            $html = $this->processVariables($block->toHtml());
            return $html;
        }

        /**
         * Build the HTML content for customer email submission
         *
         * @return string
         */
        protected function getSubmissionEmailContent()
        {
            $form     = $this->getForm();
            $elements = [];
            foreach ($this->getElements(\Cytracon\BlueFormBuilderCore\Model\EmailNotification::TYPE_CUSTOMER) as $element) {
                $newElement = $element;
                if ($newElement->getConfig('exclude_from_email')) {
                    continue;
                }
                if ($emailLabel = $newElement->getConfig('email_label')) {
                    $config = $newElement->setConfig('label', $emailLabel);
                }
                $elements[] = $newElement;
            }

            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $this->layoutFactory->create();
            $block  = $layout->createBlock('Cytracon\BlueFormBuilderCore\Block\Email\Message');
            $html   = $block->setTemplate('submission_content.phtml')
                            ->setForm($form)
                            ->setElements($elements)
                            ->setPost($this->getFormPost())
                            ->toHtml();
            return $html;
        }

        /**
         * Build the HTML content for admin email submission
         *
         * @return string
         */
        protected function getAdminSubmissionContent()
        {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $this->layoutFactory->create();
            $form   = $this->getForm();
            $block  = $layout->createBlock('Cytracon\BlueFormBuilderCore\Block\Email\Message');
            $html   = $block->setTemplate('submission_content.phtml')
                            ->setForm($form)
                            ->setElements($this->getElements(\Cytracon\BlueFormBuilderCore\Model\EmailNotification::TYPE_ADMIN))
                            ->setPost($this->getFormPost())
                            ->toHtml();
            return $html;
        }

        /**
         * Get attachments collected during submission
         *
         * @return array
         */
        public function getAttachments()
        {
            return $this->_attachments;
        }
    }