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
 * Name: `alphadash`
 *
 * This rule ensures a specific field contains only alpha, dash and underscores characters.
 */
class AlphaDash implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\is_string($value) || \preg_match('/[^A-Za-z\-_]/u', $value)) {
            return 'formvalidator_make_alpha_dash';
        }
        
        return true;
    }
}
