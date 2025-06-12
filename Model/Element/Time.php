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

class Time extends \Cytracon\BlueFormBuilderCore\Model\Element
{
	public function prepareValue($val)
	{
		if (is_array($val) && count($val) >= 2) {
			$value = $val['hour'] . ':' . $val['min'];
			if (isset($val['type'])) $value .= ' ' . strtoupper($val['type']);
			$this->setValue($value);
            $this->setHtmlValue($value);
            $this->setEmailHtmlValue($value);
        }
	}
}