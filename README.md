# Coding Standards

A standard set of linting rules for PHP projects.

## Installation

You can then install the package via composer:

```bash
composer require clntdev/coding-standards --dev
```

## Standard composer scripts

Below are the scripts that you should add to your `composer.json` to simplify working with these tools.

>Note `doc-check` can be removed from the below scripts if not in use

```
"scripts": {
  "php-lint": "vendor/bin/parallel-lint --exclude vendor --exclude node_modules --exclude sdk/vendor .",
  "phpcs": "vendor/bin/phpcs YOUR_DIRECTORIES --standard=./vendor/clntdev/coding-standards/phpcs.xml",
  "doc-check": "vendor/bin/php-doc-check YOUR_DIRECTORIES",
  "lint": [
    "@composer php-lint",
    "@composer phpcs",
    "@composer doc-check",
  ],
  "cbf": "vendor/bin/phpcbf YOUR_DIRECTORIES --standard=./vendor/clntdev/coding-standards/phpcs.xml",
}
```
