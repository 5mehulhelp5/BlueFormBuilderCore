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

namespace Cytracon\BlueFormBuilderCore\Block\Element;

class Magento2Captcha extends Control
{
	public function getCaptchaHtml()
	{
		$element = $this->getElement();
		$block   = $this->getLayout()->createBlock(\Cytracon\BlueFormBuilderCore\Block\Captcha\DefaultCaptcha::class);
		$html    = $block->setHtmlId($this->getHtmlId())
		->setImgWidth(200)
		->setImgHeight(50)
		->toHtml();
        return $html;
	}
}