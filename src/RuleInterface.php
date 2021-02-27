<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/validation
 * License: https://github.com/ObsidianPHP/validation/blob/master/LICENSE
 */

namespace Obsidian\Validation;

/**
 * The validation rule interface every rule has to implement.
 */
interface RuleInterface {
    /**
     * This method validates the value using the rule's implementation.
     * @param mixed      $value      The value of the field to validate.
     * @param string     $key        The key of the field.
     * @param array      $fields     The fields.
     * @param mixed      $options    Any rule options.
     * @param bool       $exists     If the field exists ($value is null on false).
     * @param Validator  $validator  The Validator instance
     * @return bool|string|array  Return false to "skip" the rule. Return true to mark the rule as passed. `array` means `[ $key, $replacements ]`. `string` is just `$key`.
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator);
}
