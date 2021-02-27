<?php
/**
 * Validator
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Validator/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection
**/

namespace Obsidian\Validation\Tests;

use Obsidian\Validation\LanguageInterface;
use Obsidian\Validation\Languages\EnglishLanguage;
use Obsidian\Validation\RuleInterface;
use Obsidian\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase {
    function setUp() {
        Validator::setDefaultLanguage(EnglishLanguage::class);
    }
    
    function tearDown() {
        unset($_FILES['test']);
    }
    
    function testAddRule() {
        $class = (new class() implements RuleInterface {
            function validate($value, string $key, array $fields, $options, bool $exists, Validator $validator) {
                if($value === true) {
                    return true;
                }
                
                return 'Given value is not boolean true';
            }
        });
        
        $arrname = explode(\DIRECTORY_SEPARATOR, get_class($class));
        $arr2name = explode('\\', array_pop($arrname));
        $name = \strtolower(array_pop($arr2name));
        
        Validator::addRule($class);
        
        $validator = Validator::make(array(
            'true-val' => $name,
            'other' => 'string'
        ));
        
        $this->assertTrue($validator->validate(array(
            'true-val' => true,
            'other' => 'helloworld'
        ), \LogicException::class));
    }
    
    function testSetDefaultLanguage() {
        $lang = (new class() implements LanguageInterface {
            function getTranslation(string $key, array $replacements = array()): string {
                return 'ok';
            }
        });
        
        Validator::setDefaultLanguage(\get_class($lang));
        
        $validator = Validator::make(array(
            'other' => 'string'
        ));
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('other ok');
        
        $validator->validate(array(
            'other' => 5
        ));
    }
    
    function testSetDefaultLanguageUnknownClass() {
        (new class() implements LanguageInterface {
            function getTranslation(string $key, array $replacements = array()): string {
                return 'ok';
            }
        });
        
        $this->expectException(\InvalidArgumentException::class);
        Validator::setDefaultLanguage('abc');
    }
    
    function testSetDefaultLanguageInvalidClass() {
        $lang = (new class() { });
        
        $this->expectException(\InvalidArgumentException::class);
        Validator::setDefaultLanguage(\get_class($lang));
    }
    
    function testSetLanguage() {
        $lang = (new class() implements LanguageInterface {
            function getTranslation(string $key, array $replacements = array()): string {
                return 'ok';
            }
        });
        
        $validator = Validator::make(array(
            'other' => 'string'
        ));
        
        $vrt = $validator->setLanguage($lang);
        $this->assertSame($validator, $vrt);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('other ok');
        
        $validator->validate(array(
            'other' => 5
        ));
    }
    
    function testThingsEmpty() {
        $rules = array(
            'justsomething' => 'activeurl|after|alphadash|array|before|between|date|dateformat|different:username|digits:5|dimensions:min_width=1280x720|file:test|float|image|ip|lowercase|mimetypes:image/*|nowhitespace|regex:/.*/i|same|size:5|uppercase|url',
            'username' => 'string|alpha|required',
            'password' => 'string|alphanum|min:6|confirmed:confirmed',
            'email' => 'email|filled',
            'read_rules' => 'present|accepted',
            'json' => 'json',
            'age' => 'integer|min:16|max:40',
            'age_string' => 'numeric|in:16,17,18,19,20',
            'deez' => 'array:integer|distinct',
            'fun' => 'function',
            'callback2' => 'callback',
            'callable' => 'callable',
            'class' => 'class:\stdClass',
            'class_object' => 'class:\\stdClass=object',
            'class_string' => 'class:\\stdClass=string',
            'class_extends' => 'class:\\PHPUnit\\Framework\\TestCase',
            'null' => 'nullable|boolean'
        );
        
        $validator = Validator::make($rules);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('justsomething does not contain a valid (or no at all) file');
        
        $validator->validate(array());
    }
    
    function testAccepted() {
        $validator = Validator::make(
            array('test' => 'accepted')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'yes'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not accepted');
        
        $this->assertFalse($validator->validate(array('test' => 0), \LogicException::class));
    }
    
    function testActiveURL() {
        $validator = Validator::make(
            array('test' => 'activeurl')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'github.com'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not an active URL');
        
        $this->assertFalse($validator->validate(array('test' => 'github.comnothing'), \LogicException::class));
    }
    
    function testAfter() {
        $validator = Validator::make(
            array('test' => 'after:2010-01-01')
        );
        
        $this->assertTrue($validator->validate(array('test' => '2010-01-02'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not bigger / after than 2010-01-01');
        
        
        $validator2 = Validator::make(
            array('test' => 'after:2010-01-01')
        );
        
        $this->assertFalse($validator2->validate(array('test' => '2009-12-31'), \LogicException::class));
    }
    
    function testAlpha() {
        $validator = Validator::make(
            array('test' => 'alpha')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'yes'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test does not contain alphabetical characters');
        
        
        $validator2 = Validator::make(
            array('test' => 'alpha')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'yes-'), \LogicException::class));
    }
    
    function testAlpaDash() {
        $validator = Validator::make(
            array('test' => 'alphadash')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'yes-'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test does not contain alphabetic, -, and _ characters');
        
        
        $validator2 = Validator::make(
            array('test' => 'alphadash')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'yes09'), \LogicException::class));
    }
    
    function testAlphaNum() {
        $validator = Validator::make(
            array('test' => 'alphanum')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'yes5'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test does not contain alphanumeric characters');
        
        
        $validator2 = Validator::make(
            array('test' => 'alphanum')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'yes.'), \LogicException::class));
    }
    
    function testArray() {
        $validator = Validator::make(
            array('test' => 'array')
        );
        
        $this->assertTrue($validator->validate(array('test' => array()), \LogicException::class));
        
        $validator2 = Validator::make(
            array('test' => 'array:string')
        );
        
        $this->assertTrue($validator2->validate(array('test' => array('hi')), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is NULL');
        
        
        $validator3 = Validator::make(
            array('test' => 'array')
        );
        
        $this->assertTrue($validator3->validate(array('test' => null), \LogicException::class));
    }
    
    function testArray2() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not an array of bool values');
        
        
        $validator4 = Validator::make(
            array('test' => 'array:bool')
        );
        
        $this->assertFalse($validator4->validate(array('test' => array(5.2)), \LogicException::class));
    }
    
    function testBefore() {
        $validator = Validator::make(
            array('test' => 'before:2010-01-01')
        );
        
        $this->assertTrue($validator->validate(array('test' => '2009-12-31'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is smaller / before than 2010-01-01');
        
        
        $validator2 = Validator::make(
            array('test' => 'before:2010-01-01')
        );
        
        $this->assertFalse($validator2->validate(array('test' => '2010-01-02'), \LogicException::class));
    }
    
    function testBetween() {
        $validator = Validator::make(
            array('test' => 'between:0,2')
        );
        
        $this->assertTrue($validator->validate(array('test' => 1), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not between 0 and 2');
        
        
        $validator2 = Validator::make(
            array('test' => 'between:0,2')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 3), \LogicException::class));
    }
    
    function testBoolean() {
        $validator = Validator::make(
            array('test' => 'boolean')
        );
        
        $this->assertTrue($validator->validate(array('test' => true), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is NULL');
        
        $validator2 = Validator::make(
            array('test' => 'boolean')
        );
        
        $this->assertFalse($validator2->validate(array('test' => null), \LogicException::class));
    }
    
    function testCallable() {
        $validator = Validator::make(
            array('test' => 'callable')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'var_dump'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not a callable');
        
        
        $validator2 = Validator::make(
            array('test' => 'callable')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testCallback(): void {
        $validator = Validator::make(
            array('test' => 'callback:?string?=?int')
        );
        
        $this->assertTrue($validator->validate(array('test' => static function (?string $a = null): ?int {}), \LogicException::class));
        
        $validator2 = Validator::make(
            array('test' => 'callback:=void')
        );
        
        $this->assertTrue($validator2->validate(array('test' => array(self::class, 'testCallback')), \LogicException::class));
    }
    
    function testCallbackWildcard(): void {
        $validator = Validator::make(
            array('test' => 'callback:,=?int')
        );
        
        $this->assertTrue($validator->validate(array('test' => static function (?string $a = null, $b = null): ?int {}), \LogicException::class));
    }
    
    function testCallbackLessParams() {
        $validator = Validator::make(
            array('test' => 'callback:?string?=int')
        );
        
        $this->assertTrue($validator->validate(array('test' => static function (): int {}), \LogicException::class));
    }
    
    function testCallbackNoCallableFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testCallbackNoOptionsFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback')
        );
        
        $this->assertFalse($validator->validate(array('test' => 'var_dump'), \LogicException::class));
    }
    
    function testCallbackMoreParamsFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:string=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a, int $b) {}), \LogicException::class));
    }
    
    function testCallbackParamTypeFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:int=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a) {}), \LogicException::class));
    }
    
    function testCallbackNotNullableParamTypeFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:?string=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a) {}), \LogicException::class));
    }
    
    function testCallbackNoReturnFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:string=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a) {}), \LogicException::class));
    }
    
    function testCallbackNoMatchingReturnFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:string=void')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a): int {}), \LogicException::class));
    }
    
    function testCallbackNoMatchingNullReturnFailure() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'callback:string=?int')
        );
        
        $this->assertFalse($validator->validate(array('test' => static function (string $a): int {}), \LogicException::class));
    }
    
    function testClassAnyString() {
        $validator = Validator::make(
            array('test' => 'class:\\stdClass')
        );
        
        $this->assertTrue($validator->validate(array('test' => \stdClass::class), \LogicException::class));
    }
    
    function testClassAnyObject() {
        $validator = Validator::make(
            array('test' => 'class:\\stdClass')
        );
        
        $this->assertTrue($validator->validate(array('test' => (new \stdClass())), \LogicException::class));
    }
    
    function testClassObject() {
        $validator = Validator::make(
            array('test' => 'class:\\stdClass=object')
        );
        
        $this->assertTrue($validator->validate(array('test' => (new \stdClass())), \LogicException::class));
    }
    
    function testClassString() {
        $validator = Validator::make(
            array('test' => 'class:\\stdClass=string')
        );
        
        $this->assertTrue($validator->validate(array('test' => \stdClass::class), \LogicException::class));
    }
    
    function testClassFail() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'class:\\stdClass')
        );
        
        $this->assertFalse($validator->validate(array('test' => 'muffin'), \LogicException::class));
    }
    
    function testClassWrongObject() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'class:\\ArrayObject')
        );
        
        $this->assertFalse($validator->validate(array('test' => (new \stdClass())), \LogicException::class));
    }
    
    function testClassInvalidTypeString() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'class:\\stdClass=string')
        );
        
        $this->assertFalse($validator->validate(array('test' => (new \stdClass())), \LogicException::class));
    }
    
    function testClassInvalidTypeObject() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'class:\\stdClass=object')
        );
        
        $this->assertFalse($validator->validate(array('test' => \stdClass::class), \LogicException::class));
    }
    
    function testClassInvalidArg() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'class:\\stdClass')
        );
        
        $this->assertFalse($validator->validate(array('test' => 5), \LogicException::class));
    }
    
    function testConfirmed() {
        $validator = Validator::make(
            array('test' => 'confirmed')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'hi', 'test_confirmation' => 'hi'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test not verified');
        
        
        $validator2 = Validator::make(
            array('test' => 'confirmed')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 3), \LogicException::class));
    }
    
    function testDate() {
        $validator = Validator::make(
            array('test' => 'date')
        );
        
        $this->assertTrue($validator->validate(array('test' => '2010-01-01'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not a valid date');
        
        
        $validator2 = Validator::make(
            array('test' => 'date')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testDateFormat() {
        $validator = Validator::make(
            array('test' => 'dateformat:d.m.Y')
        );
        
        $this->assertTrue($validator->validate(array('test' => '01.01.2010'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not a valid date in format d.m.Y');
        
        
        $validator2 = Validator::make(
            array('test' => 'dateformat:d.m.Y')
        );
        
        $this->assertFalse($validator2->validate(array('test' => '2010-01-01'), \LogicException::class));
    }
    
    function testDifferent() {
        $validator = Validator::make(
            array('test' => 'different:test2')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'var_dump', 'test2' => 'hi'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'different:test2')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'var_dump', 'test2' => 'var_dump'), \LogicException::class));
    }
    
    function testDigits() {
        $validator = Validator::make(
            array('test' => 'digits:3')
        );
        
        $this->assertTrue($validator->validate(array('test' => '500'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'digits:3')
        );
        
        $this->assertFalse($validator2->validate(array('test' => '20'), \LogicException::class));
    }
    
    function testDimensions() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $validator = Validator::make(
            array('test' => 'dimensions:min_width=10,min_height=10,width=32,height=32,max_width=40,max_height=40,ratio=1')
        );
        
        $this->assertTrue($validator->validate(array('test' => $file), \LogicException::class));
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator2 = Validator::make(
            array('test' => 'dimensions:ratio=1/1')
        );
        
        $this->assertTrue($validator2->validate(array(), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        unset($_FILES['test']);
        
        $validator = Validator::make(
            array('test' => 'dimensions:min_width=40')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions2() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:min_height=40')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions3() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:width=40')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions4() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:height=40')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions5() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:max_width=10')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions6() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:max_height=10')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions7() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:ratio=0.5')
        );
        
        $this->assertFalse($validator->validate(array('test' => $file), \LogicException::class));
    }
    
    function testDimensions8() {
        $this->expectException(\LogicException::class);
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile2.png', 'error' => 0);
        
        $validator = Validator::make(
            array('test' => 'dimensions:ratio=0.5')
        );
        
        $this->assertFalse($validator->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
    }
    
    function testDimensions9() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'dimensions:')
        );
        
        $this->assertFalse($validator->validate(array('test' => null), \LogicException::class));
    }
    
    function testDistinct() {
        $validator = Validator::make(
            array('test' => 'distinct')
        );
        
        $this->assertTrue($validator->validate(array('test' => array(0, 1)), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'distinct')
        );
        
        $this->assertFalse($validator2->validate(array('test' => array(0, 0)), \LogicException::class));
    }
    
    function testEmail() {
        $validator = Validator::make(
            array('test' => 'email')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'email@test.com'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'email')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testFile() {
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator = Validator::make(
            array('test' => 'file')
        );
        
        $this->assertTrue($validator->validate(array(), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        unset($_FILES['test']);
        
        $validator2 = Validator::make(
            array('test' => 'file')
        );
        
        $this->assertFalse($validator2->validate(array(), \LogicException::class));
    }
    
    function testFilled() {
        $validator = Validator::make(
            array('test' => 'filled')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'var_dump'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'filled')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 0), \LogicException::class));
    }
    
    function testFloat() {
        $validator = Validator::make(
            array('test' => 'float')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5.2), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'float')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testFunction() {
        $validator = Validator::make(
            array('test' => 'function')
        );
        
        $this->assertTrue($validator->validate(array('test' => static function () {}), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'function')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testImage() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $validator = Validator::make(
            array('test' => 'image')
        );
        
        $this->assertTrue($validator->validate(array('test' => $file), \LogicException::class));
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator2 = Validator::make(
            array('test' => 'image')
        );
        
        $this->assertTrue($validator2->validate(array(), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile2.png', 'error' => 0);
        
        $validator3 = Validator::make(
            array('test' => 'image')
        );
        
        $this->assertFalse($validator3->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
    }
    
    function testImage2() {
        $this->expectException(\LogicException::class);
        
        $validator4 = Validator::make(
            array('test' => 'image')
        );
        
        $this->assertFalse($validator4->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testIn() {
        $validator = Validator::make(
            array('test' => 'in:5,4')
        );
        
        $this->assertTrue($validator->validate(array('test' => '5'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'in:5,4')
        );
        
        $this->assertFalse($validator2->validate(array('test' => '1'), \LogicException::class));
    }
    
    function testInteger() {
        $validator = Validator::make(
            array('test' => 'integer')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'integer')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testIP() {
        $validator = Validator::make(
            array('test' => 'ip')
        );
        
        $this->assertTrue($validator->validate(array('test' => '192.168.1.1'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'ip')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testJSON() {
        $validator = Validator::make(
            array('test' => 'json')
        );
        
        $this->assertTrue($validator->validate(array('test' => '{"help":true}'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'json')
        );
        
        $this->assertFalse($validator2->validate(array('test' => ''), \LogicException::class));
    }
    
    function testLowercase() {
        $validator = Validator::make(
            array('test' => 'lowercase')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'ha'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'lowercase')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'HA'), \LogicException::class));
    }
    
    function testMax() {
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator = Validator::make(
            array('test' => 'max:6')
        );
        
        $this->assertTrue($validator->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
        
        $validator2 = Validator::make(
            array('test' => 'max:6')
        );
        
        $this->assertTrue($validator2->validate(array('test' => array(2, 5, 30)), \LogicException::class));
        
        $validator3 = Validator::make(
            array('test' => 'max:6')
        );
        
        $this->assertTrue($validator3->validate(array('test' => 5), \LogicException::class));
        
        $validator4 = Validator::make(
            array('test' => 'max:6')
        );
        
        $this->assertTrue($validator4->validate(array('test' => 'abcd'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator5 = Validator::make(
            array('test' => 'max:4')
        );
        
        $this->assertFalse($validator5->validate(array('test' => 5), \LogicException::class));
    }
    
    function testMax2() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'max:4')
        );
        
        $this->assertFalse($validator->validate(array('test' => 'uiasufisa'), \LogicException::class));
    }
    
    function testMimeTypes() {
        
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $validator = Validator::make(
            array('test' => 'mimetypes:image/*')
        );
        
        $this->assertTrue($validator->validate(array('test' => $file), \LogicException::class));
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator2 = Validator::make(
            array('test' => 'mimetypes:*/*')
        );
        
        $this->assertTrue($validator2->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
        
        $this->expectException(\LogicException::class);
        
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile2.png', 'error' => 0);
        
        $validator3 = Validator::make(
            array('test' => 'mimetypes:')
        );
        
        $this->assertFalse($validator3->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
    }
    
    function testMimeTypes2() {
        $file = file_get_contents(__DIR__.'/testfile.png');
        
        $this->expectException(\LogicException::class);
        
        $validator4 = Validator::make(
            array('test' => 'mimetypes:')
        );
        
        $this->assertFalse($validator4->validate(array('test' => $file), \LogicException::class));
    }
    
    function testMin() {
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator = Validator::make(
            array('test' => 'min:1')
        );
        
        $this->assertTrue($validator->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
        
        $validator2 = Validator::make(
            array('test' => 'min:1')
        );
        
        $this->assertTrue($validator2->validate(array('test' => array(2, 5, 30)), \LogicException::class));
        
        $validator3 = Validator::make(
            array('test' => 'min:1')
        );
        
        $this->assertTrue($validator3->validate(array('test' => 5), \LogicException::class));
        
        $validator4 = Validator::make(
            array('test' => 'min:1')
        );
        
        $this->assertTrue($validator4->validate(array('test' => 'abcd'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator5 = Validator::make(
            array('test' => 'min:6')
        );
        
        $this->assertFalse($validator5->validate(array('test' => 5), \LogicException::class));
    }
    
    function testMin2() {
        $this->expectException(\LogicException::class);
        
        $validator = Validator::make(
            array('test' => 'min:4')
        );
        
        $this->assertFalse($validator->validate(array('test' => 'abc'), \LogicException::class));
    }
    
    function testNoWhitespace() {
        $validator = Validator::make(
            array('test' => 'nowhitespace')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'hi'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'nowhitespace')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testNullable() {
        $validator = Validator::make(
            array('test' => 'nullable')
        );
        
        $this->assertTrue($validator->validate(array('test' => null), \LogicException::class));
        
        $validator2 = Validator::make(
            array('test' => 'nullable|numeric')
        );
        
        $this->assertTrue($validator2->validate(array('test' => null), \LogicException::class));
    }
    
    function testNumeric() {
        $validator = Validator::make(
            array('test' => 'numeric')
        );
        
        $this->assertTrue($validator->validate(array('test' => '5'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'numeric')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testPresent() {
        $validator = Validator::make(
            array('test' => 'present')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'present')
        );
        
        $this->assertFalse($validator2->validate(array(), \LogicException::class));
    }
    
    function testRegex() {
        $validator = Validator::make(
            array('test' => 'regex:/\\d+/')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'regex:/\\d+/')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'what is this'), \LogicException::class));
    }
    
    function testRequired() {
        $validator = Validator::make(
            array('test' => 'required')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'required')
        );
        
        $this->assertFalse($validator2->validate(array('test' => null), \LogicException::class));
    }
    
    function testSame() {
        $validator = Validator::make(
            array('test' => 'same:test2')
        );
        
        $this->assertTrue($validator->validate(array('test' => 5, 'test2' => 5), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'same:test2')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 5, 'test2' => 4), \LogicException::class));
    }
    
    function testSize() {
        $_FILES['test'] = array('tmp_name' => __DIR__.'/testfile.png', 'error' => 0);
        
        $validator = Validator::make(
            array('test' => 'size:2')
        );
        
        $this->assertTrue($validator->validate(array(), \LogicException::class));
        
        unset($_FILES['test']);
        
        $validator2 = Validator::make(
            array('test' => 'size:5')
        );
        
        $this->assertTrue($validator2->validate(array('test' => array(0, 1, 2, 3, 4)), \LogicException::class));
        
        $validator3 = Validator::make(
            array('test' => 'size:5')
        );
        
        $this->assertTrue($validator3->validate(array('test' => 5), \LogicException::class));
        
        $validator4 = Validator::make(
            array('test' => 'size:5')
        );
        
        $this->assertTrue($validator4->validate(array('test' => 'hello'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator5 = Validator::make(
            array('test' => 'size:5')
        );
        
        $this->assertFalse($validator5->validate(array('test' => 'hi'), \LogicException::class));
    }
    
    function testString() {
        $validator = Validator::make(
            array('test' => 'string')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'hello'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'string')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 5), \LogicException::class));
    }
    
    function testUppercase() {
        $validator = Validator::make(
            array('test' => 'uppercase')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'API'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'uppercase')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'hello'), \LogicException::class));
    }
    
    function testURL() {
        $validator = Validator::make(
            array('test' => 'url')
        );
        
        $this->assertTrue($validator->validate(array('test' => 'https://github.com'), \LogicException::class));
        
        $this->expectException(\LogicException::class);
        
        $validator2 = Validator::make(
            array('test' => 'url')
        );
        
        $this->assertFalse($validator2->validate(array('test' => 'hello'), \LogicException::class));
    }
    
    function testFailNullableRule() {
        $validator = Validator::make(array('test' => 'string'));
    
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is NULL');
        
        $this->assertTrue($validator->validate(array('test' => null)));
    }
    
    function testFailNullableRule2() {
        $validator = Validator::make(array('test' => 'between:0,1'));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is NULL');
        
        $this->assertTrue($validator->validate(array('test' => null)));
    }
    
    function testFailNullableRule3() {
        $validator = Validator::make(array('test' => 'nullable|between:0,1'));
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('test is not between 0 and 1');
        
        $this->assertTrue($validator->validate(array('test' => 5)));
    }
    
    function testInvalidRule() {
        $this->expectException(\RuntimeException::class);
        Validator::make(array('field' => 'int'))->validate(array('field' => 'int'));
    }
    
    function testLanguageFun() {
        $validator = Validator::make(array());
        
        $this->assertSame('test', $validator->language('test'));
        $this->assertSame('Is smaller / before than 1', $validator->language('formvalidator_make_before', array('{0}' => '1')));
    }
    
    function testUnknownField() {
        $validator = Validator::make(array(), true);
        
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('"ha" is an unknown field');
        
        $validator->validate(array('ha' => 'string'), \LogicException::class);
    }
}
