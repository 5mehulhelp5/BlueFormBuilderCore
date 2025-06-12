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

interface ElementInterface
{
    public function beforeSave();

    public function afterSave();

    public function prepareValue($value);

    public function setPost($post);

    public function getPost();

    public function setValue($value);

    public function getValue();

    public function setHtmlValue($value);

    public function getHtmlValue();

    public function setEmailHtmlValue($value);

    public function getEmailHtmlValue();

    public function setForm(\Cytracon\BlueFormBuilderCore\Model\Form $form);

    public function getForm();

    public function setSubmission(\Cytracon\BlueFormBuilderCore\Model\Submission $submission);

    public function getSubmission();

    public function getBuilderElement();

    public function success();
}