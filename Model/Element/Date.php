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

class Date extends \Cytracon\BlueFormBuilderCore\Model\Element
{

	public function prepareGridValue($value)
	{
		if ($value) {
			$builderElement = $this->getBuilderElement();
			$date  = \DateTime::createFromFormat(str_replace(['mm', 'dd', 'yy'], ['m', 'd', 'Y'], $this->getConfig('date_format')), $value);
			$value = date_format($date, 'Y-m-d');
			$value = new \DateTime($value);
		}
		return $value;
	}
	
}