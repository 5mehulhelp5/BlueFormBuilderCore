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

namespace Cytracon\BlueFormBuilderCore\Block\Adminhtml\Form\Edit\Button;

class ExportSubmissions extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data  = [];
        $form  = $this->getCurrentForm();
        $count = $form->getSubmissionCount();
        if ($form->getId() && $count) {
            $exportUrl = $this->getUrl('blueformbuilder/form/exportSubmission', ['form_id' => $form->getId()]);
            $data = [
                'label'      => __('Export Submissions(' . $count . ')'),
                'class'      => 'report',
                'on_click'   => 'window.open(\'' . $exportUrl . '\', \'_self\')',
                'sort_order' => 10
            ];
        }
        return $data;
    }
}