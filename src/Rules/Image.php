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
 * Name: `image`
 *
 * This rule ensures a specific upload field is an image. Usage: `image:FIELD_NAME`
 */
class Image implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        if(isset($_FILES[$key])) {
            if(!\file_exists($_FILES[$key]['tmp_name'])) {
                return 'formvalidator_make_image';
            }
            
            $size = \getimagesize($_FILES[$key]['tmp_name']);
        } else {
            if(!$exists) {
                return false;
            }
            
            $size = @\getimagesizefromstring($value);
        }
        
        if(!$size) {
            return 'formvalidator_make_image';
        }
        
        return true;
    }
}
