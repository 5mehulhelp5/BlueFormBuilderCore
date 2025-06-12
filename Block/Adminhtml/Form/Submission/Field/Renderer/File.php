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

namespace Cytracon\BlueFormBuilderCore\Block\Adminhtml\Form\Submission\Field\Renderer;

class File extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render column for export
     *
     * @param Object $row
     * @return string
     */
    public function renderExport(\Magento\Framework\DataObject $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }

	public function _getValue(\Magento\Framework\DataObject $row)
	{
		$result    = '';
		$value     = $row->getData($this->getColumn()->getIndex());
		if ($value) {
			$files  = explode(',', $value);
			foreach ($files as $k => $file) {
				if ($file) {
					$result .= '<p>' . $file . '</p>';
				}
			}
		}
		return $result;
	}
}