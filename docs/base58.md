# `base58` class

[`src/base58.php`](https://github.com/monero-integrations/monerophp/tree/master/src/base58.php)

A PHP Base58 codec

### Methods

 - [`encode`](#encode)
 - [`decode`](#decode)

#### `encode`

Encode a hexadecimal (Base16) string to Base58

Parameters:

 - `$hex <String>` A hexadecimal (Base16) string to convert to Base58

Return: `<String>`

`"479cG5opa54beQWSyqNoWw5tna9sHUNmMTtiFqLPaUhDevpJ2YLwXAggSx5ePdeFrYF8cdbmVRSmp1Kn3t4Y9kFu7rZ7pFw"`

#### `decode`

Decode a Base58 string to hexadecimal (Base16)

Parameters:

 - `$hex <String>` A Base58 string to convert to hexadecimal (Base16)

Return: `<String>`

`"0137F8F06C971B168745F562AA107B4D172F336271BC0F9D3B510C14D3460DFB27D8CEBE561E73AC1E11833D5EA40200EB3C82E9C66ACAF1AB1A6BB53C40537C0B7A22160B0E"`

### Credits

Written by the [Monero Integrations team](https://github.com/monero-integrations/monerophp/graphs/contributors) (<support@monerointegrations.com>)

Using work from:
 - bigreddmachine [MoneroPy] (https://github.com/bigreddmachine)
 - Paul Shapiro [mymonero-core-js] (https://github.com/paulshapiro)
