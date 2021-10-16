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

$validator = Validator::make(
    array(
        'username' => 'string|required|min:5|max:75',
        'email' => 'email'
    )
);

// does not fail
var_dump($validator->validate(array(
    'username' => 'github',
    'email' => 'noreply@github.com'
))); // bool(true)

// fails
$validator->validate(array(
    'username' => 5,
    'email' => 'noreply@github.com'
)); // throws InvalidArgumentException
```
