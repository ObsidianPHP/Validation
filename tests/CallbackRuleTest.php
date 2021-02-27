<?php
/**
 * Validation
 * Copyright 2020-2021 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/Validation
 * License: https://github.com/ObsidianPHP/Validation/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Obsidian\Validation\Tests;

use Obsidian\Validation\Rules\Callback;
use PHPUnit\Framework\TestCase;

final class CallbackRuleTest extends TestCase {
    function testPrototype() {
        $prototype = Callback::prototype(static function (?string $a = null): ?string {});
        $this->assertSame('?string?=?string', $prototype);
    }
    
    function testPrototypeNoSignature() {
        $this->expectException(\InvalidArgumentException::class);
        Callback::prototype(static function () {});
    }
    
    function testPrototypeArray() {
        $prototype = Callback::prototype(array($this, 'prototyping'));
        $this->assertSame('?string?', $prototype);
    }
    
    function prototyping(?string $a = null) {
        
    }
}
