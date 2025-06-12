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

namespace Cytracon\BlueFormBuilderCore\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface FormSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get forms list.
     *
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface[]
     */
    public function getItems();

    /**
     * Set forms list.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
