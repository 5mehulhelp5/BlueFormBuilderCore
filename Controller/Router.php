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

namespace Cytracon\BlueFormBuilderCore\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;

class Router implements RouterInterface
{
    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory
     */
    protected $adminSessionInfoCollectionFactory;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Form
     */
    protected $formHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\FormProcessor
     */
    protected $formProcessor;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory
     */
    protected $submisionCollectionFactory;

    /**
     * @param ActionFactory                                                            $actionFactory                     
     * @param \Magento\Framework\Registry                                              $coreRegistry                      
     * @param \Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory $adminSessionInfoCollectionFactory 
     * @param \Cytracon\BlueFormBuilderCore\Helper\Data                                        $dataHelper                        
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form                                        $formHelper                        
     * @param \Cytracon\BlueFormBuilderCore\Model\FormProcessor                                $formProcessor                     
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory   $submisionCollectionFactory        
     */
    public function __construct(
        ActionFactory $actionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory $adminSessionInfoCollectionFactory,
        \Cytracon\BlueFormBuilderCore\Helper\Data $dataHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper,
        \Cytracon\BlueFormBuilderCore\Model\FormProcessor $formProcessor,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\Submission\CollectionFactory $submisionCollectionFactory
    ) {
        $this->actionFactory                     = $actionFactory;
        $this->_coreRegistry                     = $coreRegistry;
        $this->adminSessionInfoCollectionFactory = $adminSessionInfoCollectionFactory;
        $this->dataHelper                        = $dataHelper;
        $this->formHelper                        = $formHelper;
        $this->formProcessor                     = $formProcessor;
        $this->submisionCollectionFactory        = $submisionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        if (!$this->dispatched && $this->dataHelper->isEnabled()) {
            $pathInfo = trim($request->getPathInfo(), '/');
            $result   = $this->processUrlKey($pathInfo, $request);
            if ($result) {
                $request->setModuleName($result->getModuleName())
                    ->setControllerName($result->getControllerName())
                    ->setActionName($result->getActionName());
                if ($params = $result->getParams()) {
                    $request->setParams($params);
                }

                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
        }
    }

    /**
     * @param  string $urlKey
     * @return boolean
     */
    protected function processUrlKey($urlKey, $request)
    {
        $result = false;
        $route  = $this->dataHelper->getRoute();

        if ($route) {
            $paths = explode("/", $urlKey);
            if (count($paths) == 1 && ($urlKey == $route)) {
                $result = new DataObject([
                    'module_name'     => 'blueformbuilder',
                    'controller_name' => 'index',
                    'action_name'     => 'index'
                ]);
            } else if (count($paths) == 2) {
                if ($paths[0] == $route && ($form = $this->getForm($paths[1], $request))) {
                    $result = new DataObject([
                        'module_name'     => 'blueformbuilder',
                        'controller_name' => 'form',
                        'action_name'     => 'view',
                        'params' => [
                            'id' => $form->getId()
                        ]
                    ]);
                }
            }
        } else if ($form = $this->getForm($urlKey, $request)) {
            $result = new DataObject([
                'module_name'     => 'blueformbuilder',
                'controller_name' => 'form',
                'action_name'     => 'view',
                'params' => [
                    'id' => $form->getId()
                ]
            ]);
        }

        if (isset($form) && $form && $this->formProcessor->hasSubmitted($form) && $form->getDisableMultipleMessage()) {
            $result = new DataObject([
                'module_name'     => 'blueformbuilder',
                'controller_name' => 'form',
                'action_name'     => 'submitted',
                'params' => [
                    'id' => $form->getId()
                ]
            ]);
        }

        $paths = explode("/", $urlKey);
        if (count($paths)>=2 && $paths[0]=='submission-confirmed') {
            $submissionCollection = $this->submisionCollectionFactory->create();
            $submissionCollection->addFieldToFilter('submission_hash', $paths[1]);
            $submissionCollection->addFieldToFilter('enable_trackback_page', 1);
            $submission = $submissionCollection->getFirstItem();
            if ($submission->getId()) {
                $this->_coreRegistry->register("current_submission", $submission);
                $result = new DataObject([
                    'module_name'     => 'blueformbuilder',
                    'controller_name' => 'submission',
                    'action_name'     => 'view',
                    'params' => [
                        'id' => $submission->getId()
                    ]
                ]);
            }
        }

        return $result;
    }

    /**
     * Get form by url key
     *
     * @param  string $identifier
     * @param  RequestInterface $request
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getForm($identifier, $request)
    {
        $identifier = str_replace('.html', '', $identifier);
        $form = $this->formHelper->loadForm($identifier, 'identifier');
        if ($form->getId() && !$form->getDisableFormPage()) {
            $submissionId = $request->getParam('submission');
            $sessionId    = $request->getParam('key');
            if ($submissionId && $sessionId) {
                $sessionCollection = $this->adminSessionInfoCollectionFactory->create();
                $sessionCollection->addFieldToFilter('session_id', $sessionId)
                ->addFieldToFilter('status', 1);
                if ($sessionCollection->count()) {
                    $submissionCollection = $this->submisionCollectionFactory->create();
                    $submissionCollection->addFieldToFilter('submission_hash', $submissionId);
                    $submission = $submissionCollection->getFirstItem();
                    if ($submission->getId()) {
                        //$form = $this->formProcessor->updateSubmisionValues($form, $submission);
                    }
                    $this->_coreRegistry->register("current_submission", $submission);
                }
            }
            $this->_coreRegistry->register("current_form", $form);

            return $form;
        }
        return false;
    }
}
