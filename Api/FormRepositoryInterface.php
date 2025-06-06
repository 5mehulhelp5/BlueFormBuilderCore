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

namespace Cytracon\BlueFormBuilderCore\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FormRepositoryInterface
{
    /**
     * Save form.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface $form
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Cytracon\BlueFormBuilderCore\Api\Data\FormInterface $form);

    /**
     * Retrieve form.
     *
     * @param int $formId
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($formId);

    /**
     * Retrieve forms matching the specified searchCriteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\FormSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete form.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\FormInterface $form
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cytracon\BlueFormBuilderCore\Api\Data\FormInterface $form);

    /**
     * Delete form by ID.
     *
     * @param int $formId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($formId);
}
