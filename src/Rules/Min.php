<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/Validation
 * License: https://github.com/ObsidianPHP/Validation/blob/master/LICENSE
 */

namespace Obsidian\Validation\Rules;

use Obsidian\Validation\RuleInterface;
use Obsidian\Validation\Validator;

/**
 * Name: `min`
 *
 * This rule ensures a specific field is/has:
 *   - numeric: equal/greater than the specified value
 *   - file: filesize is equal/greater than the specified value (in kibibytes)
 *   - array: equal/more elements than specified value
 *   - string: equal/more characters than specified value
 *
 * Usage: `min:VALUE`
 */
class Min implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(isset($_FILES[$key]) && \file_exists($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] === 0) {
            $v = \round((\filesize($_FILES[$key]['tmp_name']) / 1024));
        } else {
            if(!$exists) {
                return false;
            }
            
            if(\is_array($value)) {
                $v = \count($value);
            } elseif(\is_numeric($value)) {
                $v = $value;
            } else {
                $v = \mb_strlen($value);
            }
        }
        
        if($v < $options) {
            if(\is_string($value)) {
                return array('formvalidator_make_min_string', array('{0}' => $options));
            } else {
                return array('formvalidator_make_min', array('{0}' => $options));
            }
            
        }
        
        return true;
    }
}
