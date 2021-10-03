# Validation [![CI status](https://github.com/ObsidianPHP/Validation/workflows/CI/badge.svg)](https://github.com/ObsidianPHP/Validation/actions)

Validates input against string rulesets.

# Installation

Installation is done entirely through composer.
```
composer require obsidian/validation
```

# Example

```php
use Obsidian\Validation\Validator;

$nofail = Validator::make(
    array(
        'username' => 'string|required|min:5|max:75',
        'email' => 'email'
    )
);

var_dump($nofail->validate(array(
    'username' => 'git',
    'email' => 'noreply@github.com'
))); // bool(true)
```
