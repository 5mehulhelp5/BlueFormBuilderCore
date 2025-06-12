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

namespace Cytracon\BlueFormBuilderCore\Model\Config\Source;

class ButtonAligns implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'left',
                'label' => __('Left')
            ],
            [
                'value' => 'bottom-left',
                'label' => __('Bottom Left')
            ],
            [
                'value' => 'right',
                'label' => __('Right')
            ],
            [
                'value' => 'bottom-right',
                'label' => __('Bottom Right')
            ],
        ];
    }
}
