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

namespace Cytracon\BlueFormBuilderCore\Data\Element;

class Magento2Captcha extends Element
{
    /**
     * @return Cytracon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareAppearanceTab()
    {
        $tab = parent::prepareAppearanceTab();

            $container3 = $tab->getElements()->searchById('container3');
            if ($container3) {
                $tab->removeElement('container3');
            }

        return $tab;
    }

    public function getDefaultValues()
    {
        return [
            'label' => 'Please type the letters and numbers below'
        ];
    }
}