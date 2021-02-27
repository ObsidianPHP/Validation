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
 * Name: `in`
 *
 * This rule ensures a specific field is one of the specified values (comma separated). Usage: `in:VALUE_1,VALUE_2,...`
 */
class In implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $n = \explode(',', $options);
        if(!\in_array($value, $n, true)) {
            return array('formvalidator_make_in', array('{0}' => $options));
        }
        
        return true;
    }
}
