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

namespace Cytracon\BlueFormBuilderCore\Block\Adminhtml\File\Grid\Renderer;

use Magento\Framework\DataObject;

class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Get value for the cel
     *
     * @param DataObject $row
     * @return string
     */
    public function _getValue(DataObject $row)
    {
        $value = parent::_getValue($row);
        $value = explode('/', $value);
        return $value[count($value)-1];
    }
}
