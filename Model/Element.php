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

namespace Cytracon\BlueFormBuilderCore\Model;

use \Magento\Framework\App\ObjectManager;

class Element extends \Magento\Framework\DataObject implements ElementInterface
{
	protected $_form;
	protected $_submission;
	protected $_value;
    protected $_origValue;
    protected $_post;
    protected $_gridValue;
    protected $builderElements;
    protected $_htmlValue;
    protected $_emailHtmlValue;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param \Cytracon\Builder\Data\Elements $builderElements 
     * @param array                          $data            
     */
    public function __construct(
        \Cytracon\Builder\Data\Elements $builderElements,
        array $data = []
    ) {
        parent::__construct($data); 
        $this->builderElements = $builderElements;
    }

    public function beforeSave()
    {

    }

    public function afterSave()
    {

    }

    /**
     * @param  string $value
     */
    public function prepareValue($value)
    {
    	if (is_array($value)) $value = implode(', ', $value);
        $this->setValue($value);
        $this->setHtmlValue($value);
        $this->setEmailHtmlValue($value);
    	return $value;
    }

    /**
     * @return string
     */
    public function setValue($value)
    {
    	$this->_value = $value;
    	return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
    	return $this->_value;
    }

    public function setHtmlValue($value)
    {
        $this->_htmlValue = $value;
        return $this;
    }

    public function getHtmlValue()
    {
        return $this->_htmlValue;
    }

    public function setEmailHtmlValue($value)
    {
        $this->_emailHtmlValue = $this->getEscaper()->escapeHtml($value);
        return $this;
    }

    public function getEmailHtmlValue()
    {
        return $this->_emailHtmlValue;
    }

    /**
     * @return string
     */
    public function setOrigValue($value)
    {
        $this->_origValue = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigValue()
    {
        return $this->_origValue;
    }

    /**
     * @param \Cytracon\BlueFormBuilderCore\Model\Form $form
     */
    public function setForm(\Cytracon\BlueFormBuilderCore\Model\Form $form) {
    	$this->_form = $form;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Form
     */
    public function getForm()
    {
    	return $this->_form;
    }

    /**
     * @param \Cytracon\BlueFormBuilderCore\Model\Submission $submission
     */
    public function setSubmission(\Cytracon\BlueFormBuilderCore\Model\Submission $submission) {
    	$this->_submission = $submission;
    	return $this;
    }

    /**
     * @return \Cytracon\BlueFormBuilderCore\Model\Submission
     */
    public function getSubmission()
    {
    	return $this->_submission;
    }

    public function getBuilderElement()
    {
        return $this->builderElements->getElement($this->getType());
    }

    public function getConfig($key)
    {
        $config = $this->getData('config');
        return isset($config[$key]) ? $config[$key] : '';
    }

    public function setConfig($key, $value)
    {
        $config = $this->getData('config');
        $config[$key] = $value;
        $this->setData('config', $config);
        return $this;
    }

    public function setPost($post)
    {
        $this->_post = $post;
        return $this;
    }

    public function getPost()
    {
        return $this->_post;
    }

    public function success()
    {
        
    }

    public function prepareGridValue($value)
    {
        return $value;
    }

    public function getEscaper()
    {
        if ($this->escaper == NULL) {
            $this->escaper = ObjectManager::getInstance()->get(\Magento\Framework\Escaper::class);
        }
        return $this->escaper;   
    }
}