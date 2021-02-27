# Validation [![CircleCI](https://circleci.com/gh/ObsidianPHP/Validation.svg?style=svg)](https://circleci.com/gh/ObsidianPHP/Validation)

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
