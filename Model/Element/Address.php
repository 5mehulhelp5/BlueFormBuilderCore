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

namespace Cytracon\BlueFormBuilderCore\Model\Element;

class Address extends \Cytracon\BlueFormBuilderCore\Model\Element
{
    /**
     * @param  string $value
     */
    public function prepareValue($value)
    {
        if (is_array($value)) $value = implode(' | ', $value);
        $this->setValue($value);
        $this->setHtmlValue($value);
        $this->setEmailHtmlValue($value);
        return $value;
    }
    
}