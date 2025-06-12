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

namespace Cytracon\BlueFormBuilderCore\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SubmissionRepositoryInterface
{
    /**
     * Save submission.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface $submission
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface $submission);

    /**
     * Retrieve submission.
     *
     * @param int $submissionId
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($submissionId);

    /**
     * Retrieve submissions matching the specified searchCriteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete submission.
     *
     * @param \Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface $submission
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cytracon\BlueFormBuilderCore\Api\Data\SubmissionInterface $submission);

    /**
     * Delete submission by ID.
     *
     * @param int $submissionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($submissionId);
}
