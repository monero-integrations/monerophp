## Mnemonic Class

### Namespace

```php
namespace MoneroIntegrations\MoneroCrypto;
```

### Class: Mnemonic

The `Mnemonic` class provides methods for encoding, decoding, validating checksums, and managing wordsets for Monero wallets.

#### Method: `checksum`

Given a mnemonic seed word list, return the seed checksum.

```php
/**
 * Given a mnemonic seed word list, return the seed checksum.
 *
 * @param array<string> $words
 * @param int $prefix_len
 * @return string
 */
public static function checksum(array $words, int $prefix_len): string
```

##### Example:

```php
$seed = [
    "sighting", "pavements", "mocked", "dilute", 
    "lunar", "king", "bygones", "niece", "tonic", 
    "noises", "ostrich", "ecstatic", "hoax", "gawk", 
    "bays", "wiring", "total", "emulate", "update", 
    "bypass", "asked", "pager", "geometry", "haystack", 
    "geometry"
];
$prefixLen = 3;
$checksum = Mnemonic::checksum($seed, $prefixLen); // Returns "geometry"
```

#### Method: `validateChecksum`

Given a mnemonic seed word list, check if checksum word is valid.

```php
/**
 * Given a mnemonic seed word list, check if checksum word is valid.
 *
 * @param array<string> $words
 * @param int $prefix_len
 * @return bool
 */
public static function validateChecksum(array $words, int $prefix_len): bool
```

Example:

```php
$isValid = Mnemonic::validateChecksum($seed, $prefixLen); // Returns true
```

#### Method: `swapEndian`

Given an 8 byte word (or shorter), pads to 8 bytes (adds 0 at left) and reverses endian byte order.

```php
/**
 * Given an 8 byte word (or shorter), pads to 8 bytes (adds 0 at left) and reverses endian byte order.
 *
 * @param string $word
 * @return string
 */
public static function swapEndian(string $word): string
```

Example:

```php
$word = "12345678";
$swapped = Mnemonic::swapEndian($word); // Returns "78563412"
```

#### Method: `encode`

Given a hexadecimal key string (seed), return its mnemonic representation.

```php
/**
 * Given a hexadecimal key string (seed), return its mnemonic representation.
 *
 * @param string $seed
 * @param string|null $wordset_name
 * @return array<string>
 */
public static function encode(string $seed, ?string $wordset_name = null): array
```

Example:

```php
$seedHex = "f2750ee6e1f326f485fdc34ac517a69cbd9c72c5766151626039f0eeab40e109";
$encodedMnemonic = Mnemonic::encode($seedHex, "english"); // Returns an array of mnemonic words
```

#### Method: `encodeWithChecksum`

Given a hexadecimal key string (seed), return its mnemonic representation plus an extra checksum word.

```php
/**
 * Given a hexadecimal key string (seed), return its mnemonic representation plus an extra checksum word.
 *
 * @param string $seed
 * @param string|null $wordset_name
 * @return array<string>
 */
public static function encodeWithChecksum(string $seed, ?string $wordset_name = null): array
```

Example:

```php
$seedHex = "f2750ee6e1f326f485fdc34ac517a69cbd9c72c5766151626039f0eeab40e109";
$mnemonicWithChecksum = Mnemonic::encodeWithChecksum($seedHex, "english"); // Returns an array of mnemonic words with checksum
```

#### Method: `decode`

Given a mnemonic word list, return a hexadecimal encoded string (seed).

```php
/**
 * Given a mnemonic word list, return a hexadecimal encoded string (seed).
 *
 * @param array<string> $wlist
 * @param string|null $wordset_name
 * @return string
 * @throws Exception if decoding fails.
 */
public static function decode(array $wlist, ?string $wordset_name = null): string
```

Example:

```php
$mnemonicWords = ["sighting", "pavements", "mocked", ...];
$decodedSeedHex = Mnemonic::decode($mnemonicWords, "english"); // Returns the hexadecimal seed string
```

#### Method: `getWordsetByName`

Given a wordset identifier, returns the full wordset.

```php
/**
 * Given a wordset identifier, returns the full wordset.
 *
 * @param string|null $name
 * @return array<string>
 * @throws Exception if the wordset name is invalid.
 */
public static function getWordsetByName(?string $name = null): array
```

Example:

```php
$wordsetDetails = Mnemonic::getWordsetByName("english"); // Returns details of the English wordset
```

#### Method: `findWordsetByMnemonic`

Given a mnemonic array of words, returns the name of the matching wordset.

```php
/**
 * Given a mnemonic array of words, returns the name of the matching wordset.
 *
 * @param array<string> $mnemonic
 * @return string|null
 * @throws Exception if more than one wordset matches the mnemonic.
 */
public static function findWordsetByMnemonic(array $mnemonic): ?string
```

Example:

```php
$matchedWordset = Mnemonic::findWordsetByMnemonic($mnemonicWords); // Returns "english" if the mnemonic matches
```

#### Method: `getWordsetList`

Return a list of available wordset names.

```php
/**
 * Return a list of available wordset names.
 *
 * @return array<string>
 */
public static function getWordsetList(): array
```

Example:

```php
$wordsetList = Mnemonic::getWordsetList(); // Returns an array of available wordset names
```

#### Method: `getWordsets`

Return a list of available wordsets with details.

```php
/**
 * Return a list of available wordsets with details.
 *
 * @return array<string, array<string, string|int|array<string>>>
 */
public static function getWordsets(): array
```

Example:

```php
$allWordsets = Mnemonic::getWordsets(); // Returns an array of all available wordsets with details
```

### Interfaces

#### Interface: `Wordset`

The `Wordset` interface is implemented by classes representing different wordsets.

```php
interface Wordset {
    public static function name(): string;
    public static function englishName(): string;
    public static function prefixLength(): int;
    public static function words(): array;
}
```

This interface defines methods that must be implemented by each wordset class. It provides information about the name, English name, prefix length, and word list of a wordset.