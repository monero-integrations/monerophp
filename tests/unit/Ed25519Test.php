<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\Ed25519;

use MoneroIntegrations\MoneroCrypto\BigInteger;
use MoneroIntegrations\MoneroCrypto\Point;
use PHPUnit\Framework\TestCase;

class Ed25519Test extends TestCase
{
    /** @var Ed25519 */
    private $ed25519;

    private $testHashInput = 'hello';
    private $testHashOutput = '9b71d224bd62f3785d96d46ad3ea3d73319bfbc2890caadae2dff72519673ca72323c3d99ba5c11d7c7acc6e14b8c5da0c4663475c2e5c3adef46f73bcdec043';

    private $testInvInput = "723700557733226221397318656304299424085711635937990760600195093828545425098900";
    private $testInvOutput = "51412408364886896381606858466048826617607629358300571105541481376187170082541";

    private $testHintInput = "100";
    private $testHintOutput = "5249739319241077611146023738646316455244634195485061850472191081926985295157468499557719456094638265400023549952640721037286035956983479601959945106531843";

    private $testEncodedB = "5866666666666666666666666666666666666666666666666666666666666666";
    private $testBitInput = "fffffffffffffffffffffffffffffffX";

    private $testPublicKeyInput = "92109952688507622152061303160978719049325098095664035905533631729165308545803";
    private $testPublicKeyOutput = "22b93c70919f3bb0023e2dd172db4b0d1ed9980fa1edf8cfdd39181047f66639";

    private $testEdwardsOutput = "c9a3f86aae465f0e56513864510f3997561fa2c9e85ea21dc2292309f3cd6022";

    protected function setUp(): void
    {
        $this->ed25519 = new Ed25519();
    }

    // Tests for Point class
    public function testPoint(): void
    {
        $point = new Point(new BigInteger(1), new BigInteger(2));
        $this->assertEquals('1', $point->x->toString());
        $this->assertEquals('2', $point->y->toString());

        $point2 = new Point(new BigInteger(2), new BigInteger(1));
        $this->assertEquals('2', $point2->x->toString());
        $this->assertEquals('1', $point2->y->toString());

        $this->assertNotEquals($point, $point2);
    }

    public function testH(): void
    {
        $hash = Ed25519::H($this->testHashInput);
        $this->assertEquals($this->testHashOutput, bin2hex($hash));
    }

    public function testExpMod(): void
    {
        $base = new BigInteger('2');
        $exp = new BigInteger('3');
        $mod = new BigInteger('5');
        $result = Ed25519::expMod($base, $exp, $mod);
        $this->assertEquals('3', $result->toString());
    }

    public function testInv(): void
    {
        $number = new BigInteger($this->testInvInput);
        $result = $this->ed25519->inv($number);
        $this->assertEquals($this->testInvOutput, $result->toString());
    }

    public function testXrecover(): void
    {
        $Bx = $this->ed25519->xRecover($this->ed25519->B->y);
        $this->assertEquals($this->ed25519->B->x->toString(), $Bx->toString());
    }

    public function testEdwardsAndEncodePoint(): void
    {
        $result = $this->ed25519->edwards($this->ed25519->B, $this->ed25519->B);
        $result = $this->ed25519->encodepoint($result);
        $this->assertEquals($this->testEdwardsOutput, $result);

        $point = $this->ed25519->B;
        $result = $this->ed25519->encodepoint($point);
        $this->assertEquals($this->testEncodedB, $result);
    }

    public function testScalarMult(): void
    {
        $result = $this->ed25519->scalarmult($this->ed25519->B, new BigInteger(0));
        $this->assertEquals($this->ed25519->identityPoint, $result);

        $result = $this->ed25519->scalarmult($this->ed25519->B, new BigInteger(1));
        $this->assertEquals($this->ed25519->B, $result);

        $result = $this->ed25519->scalarmult($this->ed25519->B, new BigInteger(2));
        $this->assertEquals($this->ed25519->edwards($this->ed25519->B, $this->ed25519->B), $result);
    }

    public function testPublicKey(): void
    {
        $sk = new BigInteger($this->testPublicKeyInput);
        $pk = $this->ed25519->publickey($sk);

        $this->assertEquals($this->testPublicKeyOutput, $pk);
    }

    public function testEncodeInt(): void
    {
        $result = $this->ed25519->encodeint(new BigInteger(100));
        $this->assertEquals(hex2bin('64'), $result);
    }

    public function testBit(): void
    {
        $result = $this->ed25519->bit($this->testBitInput, new BigInteger(255));
        $this->assertEquals(0, $result);

        $result = $this->ed25519->bit($this->testBitInput, new BigInteger(254));
        $this->assertEquals(1, $result);
    }

    public function testHint(): void
    {
        $result = $this->ed25519->Hint(new BigInteger($this->testHintInput));
        $expected = new BigInteger($this->testHintOutput);
        $this->assertEquals($expected->toString(), $result);
    }

    public function testIsOnCurve(): void
    {
        $point = new Point($this->ed25519->B->x, $this->ed25519->B->y);
        $this->assertTrue($this->ed25519->isOnCurve($point));

        $point = new Point($this->ed25519->B->x, $this->ed25519->B->y->add(new BigInteger(1)));
        $this->assertFalse($this->ed25519->isOnCurve($point));
    }

    public function testDecodeInt(): void
    {
        $point = $this->ed25519->decodeint(hex2bin('64'));
        $this->assertEquals('100', $point->toString());
    }

    public function testDecodePoint(): void
    {
        $point = $this->ed25519->decodepoint($this->testEncodedB);
        $this->assertEquals($this->ed25519->B, $point);

        $this->expectException(TypeError::class);
        $point = $this->ed25519->decodepoint("invalid");
    }

    // check valid
}
