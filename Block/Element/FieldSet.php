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

class FieldSet extends \Cytracon\BlueFormBuilderCore\Block\Element
{
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = parent::getAdditionalStyleHtml();
		$element   = $this->getElement();
		$titleTag  = $element->getData('title_tag');

		$styles['padding'] = $element->getData('fieldset_padding');
		$styleHtml .= $this->getStyles('.bfb-fieldset-content', $styles);

		$styles = [];
		$styles['padding']    = $element->getData('fieldset_padding');
		$styles['color']      = $this->getStyleColor($element->getData('heading_color'));
		$styles['background'] = $this->getStyleColor($element->getData('heading_background_color'));
		$styleHtml .= $this->getStyles('.bfb-fieldset-heading ' . $titleTag, $styles);

		return $styleHtml;
	}
}