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
 * Name: `dateformat`
 *
 * This rule ensures a specific field is a date in a specific format. Usage: `dateformat:FORMAT`
 */
class DateFormat implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $dt = \date_parse_from_format($options, $value);
        if(!$dt || $dt['error_count'] > 0) {
            return array('formvalidator_make_date_format', array('{0}' => $options));
        }
        
        return true;
    }
}
