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

namespace Cytracon\BlueFormBuilderCore\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface SubmissionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get submissions list.
     *
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface[]
     */
    public function getItems();

    /**
     * Set submissions list.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
