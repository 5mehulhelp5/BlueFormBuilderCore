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

class SaveProgress extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Cytracon\Core\Helper\Data
	 */
	protected $resource;

	/**
	 * @var \Magento\Customer\Model\Visitor
	 */
	protected $customerVisitor;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Helper\Form
     */
    protected $formHelper;

    /**
     * @param \Magento\Framework\App\Action\Context     $context         
     * @param \Magento\Framework\App\ResourceConnection $resource        
     * @param \Magento\Customer\Model\Visitor           $customerVisitor 
     * @param \Cytracon\Core\Helper\Data                 $coreHelper      
     * @param \Cytracon\BlueFormBuilderCore\Helper\Form         $formHelper      
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Cytracon\Core\Helper\Data $coreHelper,
        \Cytracon\BlueFormBuilderCore\Helper\Form $formHelper
    ) {
        parent::__construct($context);
        $this->resource        = $resource;
        $this->customerVisitor = $customerVisitor;
        $this->coreHelper      = $coreHelper;
        $this->formHelper      = $formHelper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
    	$params = $this->getRequest()->getParams();
    	$result['status'] = false;
    	try {
            $formKey = $this->getRequest()->getParam('key');
            $form    = $this->formHelper->loadForm($formKey, 'bfb_form_key');
            if ($form->getId() && isset($params['post']) && $form->getEnableAutosave()) {
                $visitorId  = $this->customerVisitor->getId();
                $post = array();
                parse_str($this->getRequest()->getParam('post'), $post);
                unset($post['bfb_form_key']);
                unset($post['product_id']);
                unset($post['submission_id']);
                $data = [
                    'form_id'    => $form->getId(),
                    'visitor_id' => $visitorId,
                    'post'       => $this->coreHelper->serialize($post)
                ];
                $table = $this->resource->getTableName('mgz_blueformbuilder_form_progress');
                $this->resource->getConnection()->insertOnDuplicate($table, $data);
    			$result['status'] = true;
            }
		} catch (\Exception $e) {
		}
		$this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
        return;
    }
}