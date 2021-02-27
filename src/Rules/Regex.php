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
 * Name: `regex`
 *
 * This rule ensures a specific field passed the regex validation. Usage: `regex:REGEX_WITH_DELIMITERS`
 */
class Regex implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(\preg_match($options, $value) === 0) {
            return 'formvalidator_make_regex';
        }
        
        return true;
    }
}
