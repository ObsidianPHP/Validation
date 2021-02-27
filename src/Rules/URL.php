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
 * Name: `url`
 *
 * This rule ensures a specific field is a valid URL.
 */
class URL implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\filter_var($value, \FILTER_VALIDATE_URL)) {
            return 'formvalidator_make_url';
        }
        
        return true;
    }
}
