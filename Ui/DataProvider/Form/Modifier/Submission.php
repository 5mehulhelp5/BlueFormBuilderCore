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

namespace Cytracon\BlueFormBuilderCore\Ui\DataProvider\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;

class Submission extends AbstractModifier
{
    const GROUP_SUBMISSION  = 'submissions';
    const GROUP_CONTENT     = 'content';
    const SORT_ORDER        = 2000;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param UrlInterface                $urlBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry   = $registry;
    }

    /**
     * Get current form
     *
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     * @throws NoSuchEntityException
     */
    public function getCurrentForm()
    {
        $form = $this->registry->registry('current_form');
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getCurrentForm()->getId()) {
            return $meta;
        }

        $meta[static::GROUP_SUBMISSION] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'             => __('Form Submissions'),
                        'collapsible'       => true,
                        'opened'            => false,
                        'componentType'     => Form\Fieldset::NAME,
                        'sortOrder'         => 1000,
                        'additionalClasses' => 'bfb-form-submissions'
                    ]
                ]
            ],
            'children' => [
                'blueformbuilder_submission_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender'         => true,
                                'componentType'      => 'insertListing',
                                'dataScope'          => 'blueformbuilder_submission_listing',
                                'externalProvider'   => 'blueformbuilder_submission_listing.blueformbuilder_submission_listing_data_source',
                                'selectionsProvider' => 'blueformbuilder_submission_listing.blueformbuilder_submission_listing.product_columns.ids',
                                'ns'                 => 'blueformbuilder_submission_listing',
                                'render_url'         => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink'       => false,
                                'behaviourType'      => 'simple',
                                'externalFilterMode' => true,
                                'imports'            => [
                                    'formId' => '${ $.provider }:data.current_form_id',
                                    '__disableTmpl' => ['formId' => false]
                                ],
                                'exports' => [
                                    'formId' => '${ $.externalProvider }:params.current_form_id',
                                    '__disableTmpl' => ['formId' => false]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $formId = $this->getCurrentForm()->getId();
        $data['current_form_id'] = $formId;

        return $data;
    }
}
