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

class StarRatings extends \Cytracon\BlueFormBuilderCore\Model\Element
{
	public function prepareValue($value)
	{
        if ($value) {
            $starValues = explode("\n", $this->getConfig('star_values'));
            if ($starValues) {
                foreach ($starValues as $_star) {
                    if (strpos($_star, '==') !== false) {
                        $_row = explode('==', $_star);
                        if (count($_row) >= 2 && $value==$_row[0]) {
                            $value = $_row[1];
                        }
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
        $values     = [];
        $starValues = explode("\n", $this->getConfig('star_values'));
        if ($starValues) {
            foreach ($starValues as $_star) {
                if (strpos($_star, '==') !== false) {
                    $_row = explode('==', $_star);
                    $values[] = $_row[1];
                }
            }
        }
        return $values;
    }
}