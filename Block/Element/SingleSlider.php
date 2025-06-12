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

namespace Cytracon\BlueFormBuilderCore\Block\Element;

class SingleSlider extends Control
{
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = parent::getAdditionalStyleHtml();
		$element   = $this->getElement();

		$styles = [];
		$styles['background'] = $this->getStyleColor($element->getData('color'));

		$styleHtml .= $this->getStyles([
			'.irs-from',
			'.irs-to',
			'.irs-single',
			'.irs-bar-edge',
			'.irs-bar',
			'.irs-slider:before'
		], $styles);

		$styles = [];
		if ($element->hasData('color')) {
			$styles['border-top-color'] = $this->getStyleColor($element->getData('color'));
		}

		$styleHtml .= $this->getStyles([
			'.irs-from',
			'.irs-to',
			'.irs-single'
		], $styles, ':after');

		return $styleHtml;
	}
}