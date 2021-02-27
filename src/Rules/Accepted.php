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
 * Name: `accepted`
 *
 * This rule ensures a specific field is accepted (value: `yes`, `on`, `1` or `true`).
 */
class Accepted implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\in_array($value, array('yes', 'on', 1, true, '1', 'true'), true)) {
            return 'formvalidator_make_accepted';
        }
        
        return true;
    }
}
