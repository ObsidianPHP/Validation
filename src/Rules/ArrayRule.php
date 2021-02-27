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
 * Name: `array` - Type Rule
 *
 * This rule ensures a specific field is an array, or an array with only the specified type. Usage: `array` or `array:TYPE`
 */
class ArrayRule implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\is_array($value)) {
            return 'formvalidator_make_array';
        }
        
        if(!empty($options)) {
            foreach($value as $val) {
                $type = \gettype($val);
                if($type === 'double') {
                    $type = 'float'; // @codeCoverageIgnore
                }
                
                if($type !== $options) {
                    return array('formvalidator_make_array_subtype', array('{0}' => $options));
                }
            }
        }
        
        return true;
    }
}
