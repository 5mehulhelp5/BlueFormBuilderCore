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

namespace Cytracon\BlueFormBuilderCore\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SubmissionStatus implements OptionSourceInterface
{
    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\Submission
     */
    protected $blueformbuilderSubmission;

    /**
     * Constructor
     *
     * @param \Cytracon\BlueFormBuilderCore\Model\Submission $blueformbuilderSubmission
     */
    public function __construct(\Cytracon\BlueFormBuilderCore\Model\Submission $blueformbuilderSubmission)
    {
        $this->blueformbuilderSubmission = $blueformbuilderSubmission;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->blueformbuilderSubmission->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key
            ];
        }
        return $options;
    }
}
