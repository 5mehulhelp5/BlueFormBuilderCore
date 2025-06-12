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

namespace Cytracon\BlueFormBuilderCore\Helper;

use Magento\Store\Model\Store;

class Form extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\HTTP\ClientInterface
     */
    protected $client;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Cytracon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var \Cytracon\Builder\Model\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\Source\CustomerGroup
     */
    protected $customerGroup;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\FormProcessor
     */
    protected $formProcessor;

    /**
     * @param \Magento\Framework\App\Helper\Context            $context         
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager    
     * @param \Magento\Framework\App\ResourceConnection        $resource        
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager   
     * @param \Magento\Customer\Model\Session                  $customerSession 
     * @param \Magento\Framework\Filter\FilterManager          $filterManager   
     * @param \Magento\Framework\HTTP\ClientInterface          $client          
     * @param \Cytracon\Core\Helper\Data                        $coreHelper      
     * @param \Cytracon\Builder\Helper\Data                     $builderHelper   
     * @param \Cytracon\Builder\Model\CacheManager              $cacheManager    
     * @param \Cytracon\BlueFormBuilderCore\Model\FormFactory          $formFactory     
     * @param \Cytracon\BlueFormBuilderCore\Model\Source\CustomerGroup $customerGroup   
     * @param \Cytracon\BlueFormBuilderCore\Model\FormProcessor        $formProcessor   
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\HTTP\ClientInterface $client,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\Builder\Helper\Data $builderHelper,
        \Cytracon\Builder\Model\CacheManager $cacheManager,
        \Cytracon\BlueFormBuilderCore\Model\FormFactory $formFactory,
        \Cytracon\BlueFormBuilderCore\Model\Source\CustomerGroup $customerGroup,
        \Cytracon\BlueFormBuilderCore\Model\FormProcessor $formProcessor
    ) {
        parent::__construct($context);
        $this->_storeManager   = $storeManager;
        $this->_resource       = $resource;
        $this->_objectManager  = $objectManager;
        $this->customerSession = $customerSession;
        $this->filterManager   = $filterManager;
        $this->client          = $client;
        $this->coreHelper      = $coreHelper;
        $this->builderHelper   = $builderHelper;
        $this->cacheManager    = $cacheManager;
        $this->formFactory     = $formFactory;
        $this->customerGroup   = $customerGroup;
        $this->formProcessor   = $formProcessor;
    }

    /**
     * @param  \Cytracon\BlueFormBuilderCore\Model\Form $form
     * @return boolean
     */
    public function isValid(\Cytracon\BlueFormBuilderCore\Model\Form $form)
    {
        $disableAfterNos = (int)$form->getDisableAfterNos();
        if ($disableAfterNos) {
            $submissionCount = $form->getSubmissionCount();
            if ($submissionCount >= $disableAfterNos) {
                return false;
            }
        }

        if ($this->formProcessor->hasSubmitted($form) && $form->getDisableMultipleMessage()) {
            return false;
        }

        return true;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    protected function prepareForm($form)
    {
        $form->getBfbFormKey(null);
        $form->setCreationTime(null);
        $form->setUpdateTime(null);
        $form->setId(null);
        return $form;
    }


    /**
     * @param  string $value 
     * @param  string $field 
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function loadForm($value, $field = 'identifier', $skipValid = false)
    {
        $form = $this->formFactory->create();
        $storeId = $this->_storeManager->getStore()->getId();
        $customerGroupdId = $this->customerSession->getCustomerGroupId();
        if (!$this->_storeManager->isSingleStoreMode()) {
            $form->setStoreId($storeId);
        }
        $form->setCustomerGroupId($customerGroupdId)->load($value, $field);

        if (!$this->isValid($form) && !$skipValid) {
            $form = $form->getCollection()->getNewEmptyItem();
        }

        return $form;
    }

    private function getCustomerGroups()
    {
        $result = [];
        $groups = $this->customerGroup->toOptionArray();
        foreach ($groups as $key => $value) {
            $result[] = $value['value'];
        }
        return $result;
    }

    public function getFormDefaultData($additionalData = [])
    {
        $data['is_active']       = 1;
        $data['success_message'] = '<p>Thank you for submitting. You can view it anytime from this link <a href="[track_link]">[track_link]</a></p>';
        $data['email_subject']   = '[form_name] - New Form Submission';
        $data['email_body']      = '<p>Hello,</p>
        <p>You have received new form submission for the form [form_name]. Here are the details:</p>
        <p>[submission_content]</p>
        <p></p>
        <p>Page: [submit_from_page]</p>
        <p>ID: #[submission_id]</p>
        <p>Date: [submission_date]</p>
        <p><a href="[markunread_url]">Mark as read</a></p>';
        $data['customer_email_subject']                   = 'Thank you for your submission';
        $data['customer_email_body']                      = '<p>Hello,</p>
        <p>We have received your submission. Here are the details you have submitted to us:</p>
        <p>[submission_content]</p>
        <p></p>
        <p>Regards,</p>
        <p>Michael</p>';
        $data['enable_notification']                      = 0;
        $data['attach_files']                             = 1;
        $data['enable_customer_notification']             = 0;
        $data['customer_attach_files']                    = 1;
        $data['disable_form_page']                        = 0;
        $data['show_toplink']                             = 0;
        $data['redirect_to']                              = '/';
        $data['page_layout']                              = '1column';
        $data['disable_form_page']                        = 0;
        $data['success_message_header']                   = 'Your form has been submitted';
        $data['success_message_footer']                   = 'You will be redirected in [redirect_time] second(s). If your browser fails to redirect, then please click this link [redirect_link]';
        $data['disable_multiple_message']                 = 'You are already submit';
        $data['disable_multiple_condition']               = 'ip_address';
        $data['success_message_heading_color']            = '#FFF';
        $data['success_message_heading_background_color'] = '#fff';
        $data['success_message_heading_background_color'] = '#007dbd';
        $data['success_message_heading_border_color']     = '#006699';
        $data['success_message_style']                    = 'style1';
        $data['customer_group_id']                        = $this->getCustomerGroups();
        $data['store_id']                                 = Store::DEFAULT_STORE_ID;
        $data['stores']                                   = Store::DEFAULT_STORE_ID;
        $data['enable_autosave']                          = 1;
        $data = array_merge($data, $additionalData);
        return $data;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function createNewForm($additionalData = [])
    {
        $data               = $this->getFormDefaultData($additionalData);
        $data['identifier'] = $this->generateIdentifier($data['name']);
        $form = $this->_objectManager->create(\Cytracon\BlueFormBuilderCore\Model\Form::class);
        $form->setData($data);
        $form->save();
        return $form;
    }

    private function prepareElements($elements) {
        foreach ($elements as $i => &$element) {
            //$element['id'] .= $i;
            if (isset($element['elements'])) {
                $element['elements'] = $this->prepareElements($element['elements']);
            }
        }
        return $elements;
    }

    private function prepareProfile($newForm) {
        $profile = $this->builderHelper->prepareProfile($newForm->getProfile());
        if (isset($profile['elements']) && is_array($profile['elements'])) {
            $profile['elements'] = $this->prepareElements($profile['elements']);
        }
        $profile = $this->coreHelper->serialize($profile);
        $newForm->setProfile($profile);
        return $profile;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function duplicateForm($form, $extendData = [])
    {
        $newForm = $this->formFactory->create();
        $data = array_merge($form->getData(), $extendData);
        $newForm->setData($data);
        $this->prepareForm($newForm);
        $newIdentifier = $this->generateIdentifier($form->getIdentifier());
        $newForm->setBfbFormKey('');
        $newForm->setIdentifier($newIdentifier);
        $newForm->save();
        return $newForm;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function importTemplate($path, array $extendData)
    {
        $this->client->get($path);
        $content = $this->client->getBody();
        $this->importForm($content, $extendData);
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function importForm($content, array $extendData)
    {
        $data = $this->coreHelper->unserialize($content);
        $data['customer_group_id'] = $this->getCustomerGroups();
        $data = array_merge($data, $extendData);
        $newForm = $this->formFactory->create();

        if (isset($data['profile']) && is_array($data['profile'])) {
            $data['profile'] = $this->coreHelper->serialize($data['profile']);
        }

        if (isset($data['conditional']) && is_array($data['conditional'])) {
            $data['conditional'] = $this->coreHelper->serialize($data['conditional']);
        }

        $newForm->setData($data);
        $this->prepareForm($newForm);
        if (isset($data['identifier']) && $data['identifier']) {
            $identifier = $data['identifier'];
        } else if (isset($data['name']) && $data['name']) {
            $identifier = $data['name'];
        } else {
            $identifier = time() . uniqid();
        }
        $newIdentifier = $this->generateIdentifier($identifier);
        $newForm->setIdentifier($newIdentifier);
        $newForm->save();
        return $newForm;
    }

    /**
     * @param  \Cytracon\BlueFormBuilderCore\Model\Form $objct
     * @return string
     */
    protected function generateIdentifier($identifier)
    {
        $table      = $this->_resource->getTableName('mgz_blueformbuilder_form');
        $identifier = $this->filterManager->translitUrl($identifier);
        $connection = $this->_resource->getConnection();
        $select     = $connection->select()->from($table);
        $forms      = $connection->fetchAll($select);
        $x          = 1;
        while (true) {
            $validate = true;
            foreach ($forms as $_form) {
                if ($identifier === $_form['identifier']) {
                    $validate   = false;
                    $identifier = $identifier . $x;
                    $x++;
                }
            }
            if ($validate) {
                break;
            }
        }

        return $identifier;
    }

    public function getFormTemplates()
    {
        $templates = [];
        try {
            $key = 'BLUEFORMBUILDER_TEMPLATES';
            $templates = $this->cacheManager->getFromCache($key);
            if ($templates) {
                return $this->coreHelper->unserialize($templates);
            }
            $url = 'https://www.cytracon.com/productfile/blueformbuilder/templates.json';
            $ch  = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);
            if ($content) {
                $templates = $this->coreHelper->unserialize($content);

                $newTemplates = [];
                foreach ($templates as $template) {
                    try {
                        $newTemplates[] = $template;
                    } catch (\Exception $e) {
                    }
                }
                $templates = $newTemplates;
            }
            $this->cacheManager->saveToCache($key, $templates);
        } catch (\Exception $e) {

        }
        return $templates;
    }
}
