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
 * Name: `file`
 *
 * This rule ensures a specific field is a (successful)  file upload. Usage: `file:FIELD_NAME`
 */
class File implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!isset($_FILES[$key]) || !\file_exists($_FILES[$key]['tmp_name']) || $_FILES[$key]['error'] !== 0) {
            return 'formvalidator_make_invalid_file';
        }
        
        return true;
    }
}
