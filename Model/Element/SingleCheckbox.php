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

namespace Cytracon\BlueFormBuilderCore\Model\Element;

class SingleCheckbox extends \Cytracon\BlueFormBuilderCore\Model\Element
{
	public function prepareValue($val)
	{
		$value = $val;
		if ($val) {
			if ($this->getConfig('checked_value')) {
				$value = $this->getConfig('checked_value');
			}
		} else {
			if ($this->getConfig('unchecked_value')) {
				$value = $this->getConfig('unchecked_value');
			}
		}
		$this->setValue($value);
        $this->setHtmlValue($value);
        $this->setEmailHtmlValue($value);
	}
}