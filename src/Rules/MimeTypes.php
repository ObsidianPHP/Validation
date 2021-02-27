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
 * Name: `mimetypes`
 *
 * This rule ensures a specific upload field is of specific mime type (comma separated). Valid options (examples): `image/*`, `*­/*`, `image/png`. Usage: `mimetypes:MIME_TYPE`
 */
class MimeTypes implements RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
        $finfo = \finfo_open(\FILEINFO_MIME);
        
        if(isset($_FILES[$key])) {
            if(!\file_exists($_FILES[$key]['tmp_name'])) {
                return 'formvalidator_make_invalid_file';
            }
            
            $mime = \finfo_file($finfo, $_FILES[$key]['tmp_name']);
        } else {
            if(!$exists) {
                return false;
            }
            
            $mime = \finfo_buffer($finfo, $value);
        }
        
        \finfo_close($finfo);
        
        if(!$mime) {
            return 'formvalidator_make_invalid_file'; // @codeCoverageIgnore
        }
        
        $mime = \explode(';', $mime);
        $mime = \array_shift($mime);
        
        $val = \explode(',', $options);
        $result = \explode('/', $mime);
        
        foreach($val as $mimet) {
            $mimee = \explode('/', $mimet);
            
            if(
                \count($mimee) === 2 &&
                \count($result) === 2 &&
                ($mimee[0] === "*" || $mimee[0] === $result[0]) &&
                ($mimee[1] === "*" || $mimee[1] === $result[1])
            ) {
                return true;
            }
        }
        
        return 'formvalidator_make_invalid_file';
    }
}
