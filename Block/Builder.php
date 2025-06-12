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

namespace Cytracon\BlueFormBuilderCore\Block;

class Builder extends \Cytracon\Builder\Block\Builder
{
	/**
	 * @param \Magento\Framework\View\Element\Template\Context          $context        
	 * @param \Cytracon\BlueFormBuilderCore\Model\CompositeConfigProvider $configProvider 
	 * @param array                                                     $data           
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cytracon\BlueFormBuilderCore\Model\CompositeConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
    }
}