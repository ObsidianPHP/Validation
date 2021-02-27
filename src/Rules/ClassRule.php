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
 * Name: `class` - Type Rule
 *
 * This rule ensures a specific field is a string containing a valid class name or a class instance.
 * The options value ensures the class is either of that type, or extending it or implementing it.
 *
 * You can ensure that only class names get passed by appending `=string`, or only objects by `=object`.
 *
 * Usage: `class:CLASS_NAME` or `class:CLASS_NAME=string` or `class:CLASS_NAME=object`
 */
class ClassRule implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $is_string = \is_string($value);
        $is_object = \is_object($value);
        
        if(!$is_string && !$is_object) {
            return 'formvalidator_make_class';
        }
        
        $options = \explode('=', $options);
        $class = \ltrim($options[0], '\\');
        
        if(!empty($options[1]) && $options[1] === 'string' && !$is_string) {
            return 'formvalidator_make_class_stringonly';
        }
        
        if(!empty($options[1]) && $options[1] === 'object' && !$is_object) {
            return 'formvalidator_make_class_objectonly';
        }
        
        if($is_string && !\class_exists($value)) {
            return 'formvalidator_make_class';
        }
        
        if(!\is_a($value, $class, true) && !\is_subclass_of($value, $class, true)) {
            return array('formvalidator_make_class_inheritance', array('{0}' => $class));
        }
        
        return true;
    }
}
