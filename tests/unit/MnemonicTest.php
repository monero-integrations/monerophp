<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\Mnemonic;
use PHPUnit\Framework\TestCase;

class MnemonicTest extends TestCase
{
    private $testSeed = ["sighting", "pavements", "mocked", "dilute", "lunar", "king", "bygones", "niece", "tonic", "noises", "ostrich", "ecstatic", "hoax", "gawk", "bays", "wiring", "total", "emulate", "update", "bypass", "asked", "pager", "geometry", "haystack", "geometry"];
    private $testWordset = "english";
    private $testPrefixLength = 3;
    private $testSeedChecksum = "geometry";
    private $testSeedEncoded = "f2750ee6e1f326f485fdc34ac517a69cbd9c72c5766151626039f0eeab40e109";

    public function testChecksum()
    {
        $this->assertSame($this->testSeedChecksum, Mnemonic::checksum($this->testSeed, $this->testPrefixLength));
    }

    public function testValidateChecksum()
    {
        $this->assertTrue(Mnemonic::validateChecksum($this->testSeed, $this->testPrefixLength));
    }

    public function testSwapEndian()
    {
        $word = "12345678";
        $expectedSwapped = "78563412";
        $this->assertSame($expectedSwapped, Mnemonic::swapEndian($word));
    }

    public function testEncode()
    {
        $this->assertSame(array_slice($this->testSeed, 0, count($this->testSeed) - 1), Mnemonic::encode($this->testSeedEncoded, $this->testWordset));
    }

    public function testEncodeWithChecksum()
    {
        $this->assertSame($this->testSeed, Mnemonic::encodeWithChecksum($this->testSeedEncoded, $this->testWordset));
    }

    public function testDecode()
    {
        $this->assertSame($this->testSeedEncoded, Mnemonic::decode($this->testSeed, $this->testWordset));
    }

    public function testGetWordsetByName()
    {
        $expectedWordsetEnglishName = "English";
        $this->assertSame($expectedWordsetEnglishName, Mnemonic::getWordsetByName($this->testWordset)['english_name']);
    }

    public function testFindWordsetByMnemonic()
    {
        $this->assertSame($this->testWordset, Mnemonic::findWordsetByMnemonic($this->testSeed));
    }

    public function testGetWordsetList()
    {
        $expectedWordsetList = ["chinese_simplified", "dutch", "english", "english_old", "esperanto", "french", "german", "italian", "japanese", "lojban", "portuguese", "russian", "spanish"];
        $this->assertSame($expectedWordsetList, Mnemonic::getWordsetList());
    }

    public function testGetWordsets()
    {
        $wordsetCount = 13;
        $this->assertEquals($wordsetCount, count(Mnemonic::getWordsets()));
    }
}
