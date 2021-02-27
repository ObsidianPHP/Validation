<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/validation
 * License: https://github.com/ObsidianPHP/validation/blob/master/LICENSE
 */

namespace Obsidian\Validation\Rules;

use Obsidian\Validation\RuleInterface;
use Obsidian\Validation\Validator;

/**
 * Name: `between`
 *
 * This rule ensures a specific field is a value between two options, inclusive. Usage: `before:VALUE_MIN,VALUE_MAX`
 */
class Between implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $n = \explode(',', $options);
        if($n[0] > $value || $value > $n[1]) {
            return array('formvalidator_make_between', array('{0}' => $n[0], '{1}' => $n[1]));
        }
        
        return true;
    }
}
