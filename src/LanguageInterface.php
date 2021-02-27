<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/Validation
 * License: https://github.com/ObsidianPHP/Validation/blob/master/LICENSE
 */

namespace Obsidian\Validation;

/**
 * The language interface defines a strict way to get a language translation for a string (denoted by key).
 */
interface LanguageInterface {
    /**
     * Get a translation string, denoted by key. Replace the `$replacements` keys by their values in that string.
     * @param string  $key
     * @param array   $replacements
     * @return string  If not found, it must return the key.
     */
    function getTranslation(string $key, array $replacements = array()): string;
}
