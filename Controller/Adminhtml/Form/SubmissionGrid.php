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

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class SubmissionGrid extends \Cytracon\BlueFormBuilderCore\Controller\Adminhtml\Form
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cytracon_BlueFormBuilderCore::submission';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @param \Magento\Backend\App\Action\Context                   $context       
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $form = $this->_initForm();
        $params = $this->getRequest()->getParams();
        if (isset($params['from'])) {
            $this->_objectManager->get(\Magento\Framework\Registry::class)->register('current_from', $params['from']);
        }
        if (isset($params['to'])) {
            $this->_objectManager->get(\Magento\Framework\Registry::class)->register('current_to', $params['to']);
        }
        $resultLayout  = $this->resultLayoutFactory->create();
        $accountsBlock = $resultLayout->getLayout()->getBlock('form.submission.grid');
        return $resultLayout;
    }
}
