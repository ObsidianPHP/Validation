<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/Validation
 * License: https://github.com/ObsidianPHP/Validation/blob/master/LICENSE
 */

namespace Obsidian\Validation;

use Obsidian\Validation\Languages\EnglishLanguage;

/**
 * The Validator.
 * Type Rules are non-exclusive (that means specifying two type rules means either one is passing).
 */
class Validator {
    /** @var array */
    protected $rules = array();
    
    /** @var bool */
    protected $strict;
    
    /** @var LanguageInterface */
    protected $lang;
    
    /** @var string */
    protected static $defaultLanguage = EnglishLanguage::class;
    
    /** @var RuleInterface[] */
    protected static $rulesets;
    
    /** @var RuleInterface[] */
    protected static $typeRules = array();
    
    /**
     * Constructor
     * @param  array  $rules
     * @param  bool   $strict
     */
    protected function __construct(array $rules, bool $strict) {
        $this->rules = $rules;
        $this->strict = $strict;
        
        $lang = static::$defaultLanguage;
        $this->lang = new $lang();
        
        if(static::$rulesets === null) {
            static::initRules();
        }
    }
    
    /**
     * Create a new Validator instance.
     * @param  array  $rules   The validation rules.
     * @param  bool   $strict  Whether unknown fields make validation fail.
     * @return Validator
     */
    static function make(array $rules, bool $strict = false): self {
        return (new static($rules, $strict));
    }
    
    /**
     * Adds a new rule.
     * @param RuleInterface  $rule
     * @return void
     * @throws \InvalidArgumentException
     */
    static function addRule(RuleInterface $rule) {
        if(static::$rulesets === null) {
            static::initRules();
        }
        
        $arrname = \explode('\\', \get_class($rule));
        $name = \array_pop($arrname);
        
        $rname = \str_replace(array('rule', ':'), '', \strtolower($name));
        static::$rulesets[$rname] = $rule;
        
        if(\stripos($name, 'rule') !== false) {
            static::$typeRules[] = $rname;
        }
    }
    
    /**
     * Sets the default language for the Validator.
     * @param string  $language
     * @return void
     * @throws \InvalidArgumentException
     */
    static function setDefaultLanguage(string $language) {
        if(!\class_exists($language, true)) {
            throw new \InvalidArgumentException('Unknown language class');
        } elseif(!\in_array(LanguageInterface::class, \class_implements($language), true)) {
            throw new \InvalidArgumentException('Invalid language class (not implementing language interface)');
        }
        
        static::$defaultLanguage = $language;
    }
    
    /**
     * Sets the language for the Validator.
     * @param LanguageInterface  $language
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setLanguage(LanguageInterface $language): self {
        $this->lang = $language;
        return $this;
    }
    
    /**
     * Determines if the data passes the validation rules, or throws.
     * @param array   $fields
     * @param string  $throws
     * @return bool
     */
    function validate(array $fields, string $throws = \InvalidArgumentException::class): bool {
        $errors = array();
        $usedFields = $fields;
        
        foreach($this->rules as $key => $rule) {
            $set = \explode('|', $rule);
            
            $exists = \array_key_exists($key, $fields);
            $value = ($exists ? $fields[$key] : null);
            
            unset($usedFields[$key]);
            
            $passedLang = false;
            $failedOtherRules = false;
            
            $nullable = false;
            foreach($set as $r) {
                $r = \explode(':', $r, 2);
                if($r[0] === 'nullable') {
                    $nullable = true;
                    continue 1;
                } elseif(!isset(static::$rulesets[$r[0]])) {
                    throw new \RuntimeException('Validation Rule "'.$r[0].'" does not exist');
                }
                
                $return = static::$rulesets[$r[0]]->validate(
                    $value,
                    $key,
                    $fields,
                    ($r[1] ?? null),
                    $exists,
                    $this
                );
                $passed = \is_bool($return);
                
                if(\in_array($r[0], static::$typeRules, true)) {
                    if($passed) {
                        $passedLang = true;
                    } elseif(!$passedLang) {
                        $passed = false;
                    }
                } elseif(!$passed) {
                    $failedOtherRules = true;
                }
                
                if(!$passed) {
                    if(\is_array($return)) {
                        $errors[$key] = $this->language($return[0], $return[1]);
                    } else {
                        $errors[$key] = $this->language($return);
                    }
                }
            }
            
            if($passedLang && !$failedOtherRules) {
                unset($errors[$key]);
            }
            
            if($exists && \is_null($value)) {
                if(!$nullable) {
                    $errors[$key] = $this->language('formvalidator_make_nullable');
                } elseif(isset($errors[$key])) {
                    unset($errors[$key]);
                }
            }
            
            if(!empty($errors[$key])) {
                throw new $throws($key.' '.\lcfirst($errors[$key]));
            }
        }
        
        if($this->strict) {
            /** @noinspection LoopWhichDoesNotLoopInspection */
            foreach($usedFields as $key => $_) {
                $msg = $this->language('formvalidator_unknown_field');
                throw new $throws('"'.$key.'" '.\lcfirst($msg));
            }
        }
        
        return empty($errors);
    }
    
    /**
     * Return the error message based on the language key (language based).
     * @param  string  $key
     * @param  array   $replacements
     * @return string
     */
    function language(string $key, array $replacements = array()): string {
        return $this->lang->getTranslation($key, $replacements);
    }
    
    /**
     * @return void
     */
    protected static function initRules() {
        static::$rulesets = array();
        
        $rules = \glob(__DIR__.'/Rules/*.php');
        foreach($rules as $rule) {
            $name = \basename($rule, '.php');
            if($name === 'Nullable') {
                continue;
            }
            
            $class = '\\Obsidian\\Validation\\Rules\\'.$name;
            static::addRule((new $class()));
        }
    }
}
