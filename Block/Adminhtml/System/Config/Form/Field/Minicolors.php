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

namespace BlueFormBuilder\Pdf\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

class Minicolors extends Field
{

    /**
     * render separator config row
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);

        $htmlId = $element->getHtmlId();

        $html .= "<script>
            require(['jquery', 'Cytracon_Core/js/jquery.minicolors'], function($) {
                $('#" . $htmlId . "').minicolors({
                    theme: 'bootstrap'
                });
            });
        </script>
        ";
        return $html;
    }
}
