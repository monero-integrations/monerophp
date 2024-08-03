[![PHPCS PSR-12](https://img.shields.io/badge/PHPCS-PSR–12-226146.svg)](https://www.php-fig.org/psr/psr-12/) [![PHPStan ](.github/phpstan.svg)](https://phpstan.org/)

# Monero-Crypto

A Monero cryptography library written in modern PHP 8 by the [Monero Integrations team](https://monerointegrations.com) and [contributors](
https://github.com/monero-integrations/monerophp/graphs/contributors).

## Features

This library implements/interfaces various cryptographic functions used in Monero, such as:

- Monero's base58 encoding
- Monero's mnemonic seeds
- Keccak hash function
- Cryptonote functions on the Edwards25519 curve
- Variably-sized integers (Varint)

Higher-level abstractions are additionally provided for things like generating Monero private/public keys, subaddresses, etc.

## Preview
![Preview](https://user-images.githubusercontent.com/4107993/38056594-b6cd6e14-3291-11e8-96e2-a771b0e9cee3.png)

## Getting Started

The minimum PHP version required is 8.1.0. Please make sure you also have [Composer](https://getcomposer.org/) installed.

You can check your PHP version by running:

```bash
php -v
```

### Extensions

The `bcmath` extension is required.

It is **strongly recommended** to use the `gmp` extension for about 100x faster calculations (as opposed to BCMath).

To check what extensions are installed, run:

```bash
php -m
```

### Installation

#### From Packagist

```bash
composer require monero-integrations/monero-crypto
```

#### From Source

```bash
git clone https://github.com/monero-integrations/monerophp.git
cd monerophp
composer install
```

### Usage

From here, you can use the library in your PHP project. For example:

```php
require 'vendor/autoload.php';

// To get a list of available mnemonic wordlists
use MoneroIntegrations\MoneroCrypto\Mnemonic;
$wordlists = Mnemonic::getWordsetList();

echo "Available wordlists: " . implode(', ', $wordlists) . PHP_EOL;
```

## Documentation

Documentation is still a work-in-progress, but the library is well-documented with PHPDoc comments.

Current documentation can be found in the [`/docs`](./docs/) folder.

## Development

The project uses several development tools to ensure code quality and consistency:

1. PHP CodeSniffer: Used to check the code style against the PSR-12 standard.
2. PHPStan: Static analysis tool to find bugs and improve code quality.
3. PHPUnit: Testing framework for running unit tests.
4. Laravel Pint: Code style fixer for PSR-12 compliance.

### Running Tests

To ensure everything is working correctly, you can run the tests and code quality checks using Composer scripts:

#### Lint Code

```bash
composer lint
```

#### Test Lint

Run linting on your code and test files:

```bash
composer test:lint
```

#### Analyze Code with PHPStan

```bash
composer test:phpstan
```

#### Run Unit Tests

```bash
composer test:unit
```

#### Run All Tests and Checks

This will run linting, PHPStan analysis, and unit tests:

```bash
composer test
```

### Standards

We follow the PSR-12 coding standard. Please make sure your code adheres to these guidelines. You can use Laravel Pint to automatically fix code style issues.

### Contributions

We welcome contributions! If you have an idea or fix, please follow these steps:

1. Fork the repository
2. Create a branch with your changes
3. Make your changes
4. Submit a pull request (PR) with a clear description of the changes

Please ensure your code passes all tests and adheres to our coding standards before submitting a pull request.

For any questions or issues, feel free to reach out to the maintainers or open an issue on GitHub.

## License

This library is licensed under the MIT License. See the [LICENSE](./LICENSE) file for more information.