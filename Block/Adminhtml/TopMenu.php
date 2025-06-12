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

namespace Cytracon\BlueFormBuilderCore\Block\Adminhtml;

use Magento\Framework\App\ObjectManager;

class TopMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $pageTitle;

    /**
     * @return \Magento\Framework\AuthorizationInterface
     */
    protected function getAuthorization()
    {
        if ($this->_authorization == null) {
            $this->_authorization = ObjectManager::getInstance()->get(\Magento\Framework\AuthorizationInterface::class);
        }
        return $this->_authorization;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->getAuthorization()->isAllowed($resourceId);
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        $links = $this->intLinks();
        if ($links) {
            foreach ($links as $z => &$_columnLinks) {
                if (!isset($_columnLinks['title'])) {
                    foreach ($_columnLinks as $k => $_link) {
                        if (isset($_link['resource']) && !$this->_isAllowedAction($_link['resource'])) {
                            unset($_columnLinks[$k]);
                        }
                    }
                } elseif (isset($_columnLinks['resource']) && !$this->_isAllowedAction($_columnLinks['resource'])) {
                    unset($links[$z]);
                }
            }
            return $links;
        }
        return false;
    }

    /**
     * Init menu items
     *
     * @return array
     */
    public function intLinks()
    {
        $links = [
            [
                [
                    'title'    => __('Add New Form'),
                    'link'     => $this->getUrl('blueformbuilder/form/new'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::form_save',
                    'class'    => 'bfb-add-new'
                ],
                [
                    'title'    => __('Manage Forms'),
                    'link'     => $this->getUrl('blueformbuilder/form/index'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::form'
                ],
                [
                    'title'    => __('Plugins'),
                    'link'     => $this->getUrl('blueformbuilder/form/new'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::form_save',
                    'class'    => 'bfb-plugins'
                ],
                [
                    'title'    => __('File Uploads'),
                    'link'     => $this->getUrl('blueformbuilder/files'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::form_save'
                ],
                [
                    'title'    => __('Settings'),
                    'link'     => $this->getUrl('adminhtml/system_config/edit/section/blueformbuilder'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::settings'
                ]
            ],
            [
                [
                    'title'    => __('Form Submissions'),
                    'link'     => $this->getUrl('blueformbuilder/submission/index'),
                    'resource' => 'Cytracon_BlueFormBuilderCore::submission',
                    'class'    => 'bfb-submissions'
                ]
            ],
            [
                'class' => 'separator'
            ],
            [
                'title'  => __('User Guide'),
                'link'   => 'https://www.cytracon.com/pub/media/productfile/blueformbuilder-v1.0.0-user_guides.pdf',
                'target' => '_blank'
            ],
            [
                'title'  => __('Change Log'),
                'link'   => 'https://www.cytracon.com/blue-form-builder.html#release_notes',
                'target' => '_blank'
            ],
            [
                'title'  => __('Get Support'),
                'link'   => 'https://www.cytracon.com/contact',
                'target' => '_blank'
            ]
        ];
        return $links;
    }

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }
        return __($this->pageConfig->getTitle()->getShort());
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        if (!empty($this->pageTitle)) {
            return __($this->pageTitle);
        }
        return __($this->pageConfig->getTitle()->getShortHeading());
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }
}
