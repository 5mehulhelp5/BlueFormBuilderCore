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

namespace Cytracon\BlueFormBuilderCore\Controller\Adminhtml;

abstract class Form extends \Magento\Backend\App\Action
{
    /**
     * Initialize requested form and put it into registry.
     *
     * @return \Cytracon\BlueFormBuilderCore\Model\Form|false
     */
    protected function _initForm()
    {
        $formId = $this->resolveFormId();
        $form   = $this->_objectManager->create(\Cytracon\BlueFormBuilderCore\Model\Form::class);

        if ($formId) {
            $form->load($formId);
        }

        $this->_objectManager->get(\Magento\Framework\Registry::class)->register('form', $form);
        $this->_objectManager->get(\Magento\Framework\Registry::class)->register('current_form', $form);
        return $form;
    }

    /**
     * Resolve Form Id (from get or from post)
     *
     * @return int
     */
    public function resolveFormId()
    {
        $formId = (int) $this->getRequest()->getParam('id', false);

        return $formId ?: (int) $this->getRequest()->getParam('form_id', false);
    }
}
