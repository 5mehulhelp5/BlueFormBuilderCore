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

class Select extends \Cytracon\BlueFormBuilderCore\Model\Element
{
	public function prepareValue($value)
	{
		if ($value) {
			$post      = $this->getPost();
			$othersKey = $this->getElemName() . '_others';
			if ($value == 'bfb_others' && isset($post[$othersKey])) {
				$value = $this->getConfig('others_label') . ': ' . $post[$othersKey];
			} else {
				foreach ($this->getConfig('options') as $_option) {
					$key = (isset($_option['value']) && $_option['value']) ? $_option['value'] : $_option['label'];
					if ($value == $key) {
						$value = $_option['label'];
						break;
					}
				}
			}
			$this->setValue($value);
	        $this->setHtmlValue($value);
	        $this->setEmailHtmlValue($value);
	    }
	}

	public function getInsightsData()
	{
		$simpleValues = $this->getSubmission()->getSimpleValues();
		$values = isset($simpleValues[$this->getElemName()]) ? explode(', ', $simpleValues[$this->getElemName()]) : [];
		return $values;
	}

	public function getInsightsLabels()
	{
		$simpleValues = $this->getSubmission()->getSimpleValues();
		$values = isset($simpleValues[$this->getElemName()]) ? explode(', ', $simpleValues[$this->getElemName()]) : [];
		foreach ($this->getConfig('options') as $_option) {
			$key = (isset($_option['value']) && $_option['value']) ? $_option['value'] : $_option['label'];
			if (!in_array($key, $values)) {
				$values[] = $key;
			}
		}
		
		return $values;
	}
}