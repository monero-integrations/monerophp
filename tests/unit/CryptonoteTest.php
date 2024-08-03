<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\Cryptonote;
use MoneroIntegrations\MoneroCrypto\MoneroNetwork;
use PHPUnit\Framework\TestCase;

class CryptonoteTest extends TestCase
{
    private $testDecodedKeccak = "6fd43e7cffc31bb581d7421c8698e29aa2bd8e7186a394b85299908b4eb9b175";
    private $testEncodedKeccak = "0b6a7d8d740055460014f9c3e31063c5bb8e254cb9fee970294a0d0064429ca8";

    private $testHexSeed = "92df69221844dc2a77389e2c3ebae5fa32517c04826c870fb952171fb192cc08";
    private $testPubViewKey = "a21d0a8ceacdb5e6a5d8bba57741123671c83a3b34187fe23324112b0c7b7760";
    private $testPrivateViewKey = "e7cbc9050333b9ed07843ce835547c07c8a8d6b7acc10bebb60d7d6fe1a82f05";
    private $testPubSpendKey = "765d0b9d61e8ca67b8902f6b133ae7bcdde5419c72ab6244ed7400789db7be99";
    private $testPrivateSpendKey = "282a30d36de33cc65eb7804126a7644c2cce00ce2aac171a7c24496ba2764701";

    private $testAddressNetByte = "12";
    private $testAddress = "467GHbWjuFoJMDwegvhEJAYbFAyZ6Y2GqCXgebZd96CDShSESH5nCKEfaaER1vCYKTA7BQkyE5gBGeqRRcX8Fe1cBu3mLYj";
    private $testAddressBad = "467GHbWjuFoJMDwegvhEJAYbFAyZ6Y2GqCXgebZd96CDShSESH5nCKEfaaER1vCYKTA7BQkyE5gBGeqRRcX8Fe1cBu3mLzj";

    private $testPaymentId = "ef64f0a81b022a81";
    private $testIntegratedAddress = "4FowJQLEWXKJMDwegvhEJAYbFAyZ6Y2GqCXgebZd96CDShSESH5nCKEfaaER1vCYKTA7BQkyE5gBGeqRRcX8Fe1cHDPkStRLntqFZqGwKS";

    /** @var Cryptonote */
    private $cr;

    public function setUp(): void
    {
        $this->cr = new Cryptonote(MoneroNetwork::mainnet);
    }

    public function testKeccak256(): void
    {
        $result = Cryptonote::keccak_256($this->testDecodedKeccak);
        $this->assertEquals($this->testEncodedKeccak, $result);
    }

    public function testGenNewHexSeed(): void
    {
        $result = $this->cr->gen_new_hex_seed();
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }

    public function testScReduce(): void
    {
        $result = $this->cr->sc_reduce("65");
        $this->assertEquals("65", $result);
    }


    public function testEncodeAddress(): void
    {
        $result = $this->cr->encode_address($this->testPubSpendKey, $this->testPubViewKey);
        $this->assertEquals($this->testAddress, $result);
    }

    public function testVerifyChecksum(): void
    {
        $result = $this->cr->verify_checksum($this->testAddress);
        $this->assertTrue($result);

        $result = $this->cr->verify_checksum($this->testAddressBad);
        $this->assertFalse($result);
    }

    public function testDecodeAddress(): void
    {
        $result = $this->cr->decode_address($this->testAddress);
        $this->assertIsArray($result);

        $this->assertEquals($this->testAddressNetByte, $result["networkByte"]);
        $this->assertEquals($this->testPubSpendKey, $result["spendKey"]);
        $this->assertEquals($this->testPubViewKey, $result["viewKey"]);
    }

    public function testIntegratedAddrFromKeys(): void
    {
        $result = $this->cr->integrated_addr_from_keys($this->testPubSpendKey, $this->testPubViewKey, $this->testPaymentId);
        $this->assertEquals($this->testIntegratedAddress, $result);
    }
}
