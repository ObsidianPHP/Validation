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
 * Name: `confirmed`
 *
 * This rule ensures a specific field is confirmed (the fields array contains another field with the same value with the key `$key_FIELDNAME`, FIELDNAME defaults to `confirmation`). Usage: `confirmed` or `confirmed:FIELDNAME`
 */
class Confirmed implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(empty($options)) {
            $options = 'confirmation';
        }
        
        $keyopt = $key.'_'.$options;
        if(!isset($fields[$keyopt]) || $fields[$key] !== $fields[$keyopt]) {
            return 'formvalidator_make_confirmed';
        }
        
        return true;
    }
}
