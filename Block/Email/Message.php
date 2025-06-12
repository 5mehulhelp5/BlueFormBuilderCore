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

namespace Cytracon\BlueFormBuilderCore\Block\Email;

class Message extends \Magento\Framework\View\Element\Template
{
    /**
     * @param  array $styles 
     * @return string       
     */
    public function parseStyles($styles)
    {
        $result = '';
        foreach ($styles as $k => $v) {
            if (!$v) continue;
            $result .= $k . ': ' . $v . ';';
        }
        return $result;
    }
}
