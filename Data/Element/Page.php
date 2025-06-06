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

namespace Cytracon\BlueFormBuilderCore\Data\Element;

class Page extends \Cytracon\Builder\Data\Element\AbstractElement
{
    /**
     * @return Cytracon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'title',
	            'text',
	            [
					'sortOrder'       => 10,
					'key'             => 'title',
					'templateOptions' => [
						'label' => __('Title')
	                ]
	            ]
	        );

            $general->addChildren(
                'show_icon',
                'toggle',
                [
                    'sortOrder'       => 20,
                    'key'             => 'show_icon',
                    'templateOptions' => [
                        'label' => __('Add Icon')
                    ]
                ]
            );

            $container1 = $general->addContainerGroup(
                'container1',
                [
                    'sortOrder'      => 30,
                    'hideExpression' => '!model.show_icon'
                ]
            );

                $container1->addChildren(
                    'icon',
                    'icon',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'icon',
                        'templateOptions' => [
                            'label' => __('Icon')
                        ]
                    ]
                );

                $container1->addChildren(
                    'icon_position',
                    'select',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'icon_position',
                        'defaultValue'    => 'left',
                        'templateOptions' => [
                            'label'   => __('Icon Position'),
                            'options' => $this->getIconPosition()
                        ]
                    ]
                );

    	return $general;
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
    	return [
    		'title' => __('Page')
    	];
    }
}