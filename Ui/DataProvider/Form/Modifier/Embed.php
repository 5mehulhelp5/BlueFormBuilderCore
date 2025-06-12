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

namespace Cytracon\BlueFormBuilderCore\Ui\DataProvider\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;
use Cytracon\UiBuilder\Data\Form\Element\AbstractElement;
use Cytracon\UiBuilder\Data\Form\Element\Factory;
use Cytracon\UiBuilder\Data\Form\Element\CollectionFactory;

class Embed extends AbstractModifier
{
    const GROUP_EMBED_NAME               = 'embed';
    const GROUP_EMBED_DEFAULT_SORT_ORDER = 1100;

    /**
     * @var array
     */
    protected $meta;

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
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getCurrentForm()->getId()) {
            return $meta;
        }

        $this->meta = $meta;

        $this->prepareChildren();

        $this->createEmbedPanel();

        return $this->meta;
    }

    /**
     * @return \Cytracon\UiBuilder\Data\Form\Element\Fieldset
     */
    public function prepareChildren()
    {
        $form   = $this->getCurrentForm();
        $formId = $form->getIdentifier();

        $this->addContainer(
            'shortcode',
            [
                'label'    => __('Short Code'),
                'template' =>'ui/form/components/complex',
                'content'  => '
	            <ul>
	                <li>
	                    <div class="bfb-embed-note">Insert the code below into WYSIWYG editor</div>
	                    <div class="bfb-embed-code"><textarea disabled>{{widget type="Cytracon\BlueFormBuilderCore\Block\Widget\Form" code="' . $formId . '"}}</textarea></div>
	                </li>
	                <li>
	                    <div class="bfb-embed-note">Insert the code below into a template file</div>
	                    <div class="bfb-embed-code"><textarea disabled><?= $this->helper(\'Cytracon\BlueFormBuilderCore\Helper\Data\')->renderForm("' . $formId . '") ?></textarea></div>
	                </li>
	                <li>
	                    <div class="bfb-embed-note">Insert the code below into a layout file</div>
	                    <div class="bfb-embed-code"><textarea rows="6" disabled>
<block class="Cytracon\BlueFormBuilderCore\Block\Form" name="bfb-form">
        <arguments>
                <argument name="code" xsi:type="string">' . $formId . '</argument>
        </arguments>
</block>
</textarea></div>
	                </li>
	            </ul>'
            ]
        );
    }

    /**
     * Create Embed panel
     *
     * @return $this
     * @since 101.0.0
     */
    protected function createEmbedPanel()
    {
        $this->meta['settings']['children'] = array_replace_recursive(
            $this->meta['settings']['children'],
            [
                static::GROUP_EMBED_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'         => __('Embed'),
                                'componentType' => Fieldset::NAME,
                                'dataScope'     => 'data',
                                'collapsible'   => true,
                                'sortOrder'     => static::GROUP_EMBED_DEFAULT_SORT_ORDER,
                                'additionalClasses' => [
                                    'bfb-embed' => true
                                ]
                            ]
                        ]
                    ],
                    'children' => $this->getChildren()
                ]
            ]
        );
        return $this;
    }
}
