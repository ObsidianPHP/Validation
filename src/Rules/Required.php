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
 * Name: `required`
 *
 * This rule ensures a specific (upload) field is present and not empty.
 */
class Required implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(
            (!$exists || \is_null($value) || (\is_string($value) === true && \trim($value) === '')) &&
            (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== 0)
        ) {
            return 'formvalidator_make_required';
        }
        
        return true;
    }
}
