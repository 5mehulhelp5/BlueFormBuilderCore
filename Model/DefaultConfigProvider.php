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

namespace Cytracon\BlueFormBuilderCore\Model;

class DefaultConfigProvider extends \Cytracon\Builder\Model\DefaultConfigProvider
{
	/**
	 * @var string
	 */
	protected $_builderArea = 'bfb';

	/**
	 * @return array
	 */
	public function getConfig()
	{
		$config = parent::getConfig();
		$config['profile'] = [
			'builder'           => 'Cytracon\BlueFormBuilderCore\Block\Builder',
			'home'              => 'https://www.cytracon.com/magento-2-form-builder.html?utm_campaign=mgzbuilder&utm_source=mgz_user&utm_medium=backend',
			'templateUrl'       => 'https://www.cytracon.com/productfile/blueformbuilder/templates.php',
			'prefinedVariables' => [
				'class' => '\Cytracon\BlueFormBuilderCore\Model\Source\PrefinedVariables'
			]
		];
		foreach ($config['elements'] as &$element) {
		 	if (isset($element['area']) && in_array('bfb', $element['area'])) {
	 			if (!isset($element['element']) || !$element['element']) {
	 				$element['element'] = 'BlueFormBuilder_Core/js/builder/field';
			 		if (!isset($element['templateUrl']) || !$element['templateUrl']) {
					 	if (isset($element['control']) && $element['control']) {
					 		$element['templateUrl'] = 'BlueFormBuilder_Core/js/templates/builder/control.html';
					 	} else {
					 		$element['templateUrl'] = 'BlueFormBuilder_Core/js/templates/builder/field.html';
					 	}
					}
	 			}
		 	}
		}
		return $config;
	}

	/**
	 * @return array
	 */
	// public function getConfig($cache = true)
	// {
	// 	$config  = parent::getConfig();
	// 	$profile = $this->profileFactory->create();
	// 	$path    = $this->builderHelper->getArrayManager()->findPath('column', $config, 'elements');
	// 	$config['builderClass'] = 'Cytracon\BlueFormBuilderCore\Block\Builder';
	// 	$config['profile']      = [
	// 		'home' => 'https://www.cytracon.com/magento-2-form-builder.html?utm_campaign=mgzbuilder&utm_source=mgz_user&utm_medium=backend',
	// 		'settings' => [
	// 			'class' => 'Cytracon\Builder\Data\Modal\Profile'
	// 		],
	// 		'defaultSettings' => $profile->prepareForm()->getFormDefaultValues(),
	// 		'prefinedVariables' => [
	// 			'class' => '\Cytracon\BlueFormBuilderCore\Model\Source\PrefinedVariables'
	// 		],
	// 		'templates' => [
	// 			'class' => 'Cytracon\BlueFormBuilderCore\Data\Modal\Templates'
	// 		],
	// 		'editorMode' => true
	// 	];
	// 	foreach ($config['elements'] as &$element) {
	// 	 	if (in_array('bfb', $element['area'])) {
	//  			if (!isset($element['element']) || !$element['element']) {
	//  				$element['element'] = 'BlueFormBuilder_Core/js/builder/field';
	// 		 		if (!isset($element['templateUrl']) || !$element['templateUrl']) {
	// 				 	if (isset($element['control']) && $element['control']) {
	// 				 		$element['templateUrl'] = 'BlueFormBuilder_Core/js/templates/builder/control.html';
	// 				 	} else {
	// 				 		$element['templateUrl'] = 'BlueFormBuilder_Core/js/templates/builder/field.html';
	// 				 	}
	// 				}
	//  			}
	// 	 	}
	// 	}
	// 	return $config;
	// }
}