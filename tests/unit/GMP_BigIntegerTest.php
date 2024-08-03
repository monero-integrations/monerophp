<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\BigInteger;
use PHPUnit\Framework\TestCase;

class GMP_BigIntegerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        unset($GLOBALS['S_MATH_BIGINTEGER_MODE']);
        define('S_MATH_BIGINTEGER_MODE', "gmp");
    }

    public function testCreate(): void
    {
        $values = [
            ["1010000", 2],
            [hex2bin("50"), 256],
            ["50", 16],
            ["80", 10],
            ["80", 10],
            [80, 10],
        ]; // [value, base]

        for ($i = 0; $i < count($values); $i++) {
            $value = $values[$i];
            $bi = new BigInteger($value[0], $value[1]);

            $this->assertEquals($bi->toBits(), "1010000");
            $this->assertEquals($bi->toBytes(), hex2bin("50"));
            $this->assertEquals($bi->toHex(), "50");
            $this->assertEquals($bi->toDec(), "80");
            $this->assertEquals($bi->toNumber(), 80);
            $this->assertEquals($bi->toBase(58), "1M");
        }
    }

    public function testCreateSafe(): void
    {
        $this->expectException(\ValueError::class);
        new BigInteger("zz", 2);

        $this->expectException(\ValueError::class);
        new BigInteger("zz", 10);

        $this->expectException(\ValueError::class);
        new BigInteger("zz", 16);
    }

    public function testSpaces(): void
    {
        $this->assertEquals((new BigInteger("11  0   1", 2))->toBits(), "1101");
        $this->assertEquals((new BigInteger("6   2 0  6", 10))->toDec(), "6206");
        $this->assertEquals((new BigInteger("f3 5  12 ac 0", 16))->toHex(), "f3512ac0");
    }

    public function testOp(): void
    {
        $this->assertEquals((new BigInteger(20))->add(34)->toString(), "54");
        $this->assertEquals((new BigInteger(20))->sub(14)->toString(), "6");
        $this->assertEquals((new BigInteger(20))->mul(12)->toString(), "240");
        $this->assertEquals((new BigInteger(20))->div(4)->toString(), "5");
        $this->assertEquals((new BigInteger(20))->divR(7)->toString(), "6");
        $this->assertEquals((new BigInteger(20))->shiftLeft(3)->toString(), "160");
        $this->assertEquals((new BigInteger(20))->shiftRight(3)->toString(), "2");

        $qr = (new BigInteger(20))->divQR(6);
        $this->assertEquals($qr[0]->toString(), "3");
        $this->assertEquals($qr[1]->toString(), "2");

        $this->assertEquals((new BigInteger(20))->mod(3)->toString(), "2");
        $this->assertEquals((new BigInteger(54))->gcd(81)->toString(), "27");
        $this->assertEquals((new BigInteger(3))->modInverse(10)->toString(), "7");
        $this->assertEquals((new BigInteger(3))->pow(4)->toString(), "81");
        $this->assertEquals((new BigInteger(3))->powMod(4, 10)->toString(), "1");
        $this->assertEquals((new BigInteger(20))->abs()->toString(), "20");
        $this->assertEquals((new BigInteger(20))->neg()->toString(), "-20");
        $this->assertEquals((new BigInteger(20))->binaryAnd(18)->toString(), "16");
        $this->assertEquals((new BigInteger(20))->binaryOr(18)->toString(), "22");
        $this->assertEquals((new BigInteger(20))->binaryXor(18)->toString(), "6");
        $this->assertEquals((new BigInteger(20))->setbit(3)->toString(), "28");
        $this->assertEquals((new BigInteger(20))->testbit(4), true);
        $this->assertEquals((new BigInteger(20))->testbit(3), false);
        $this->assertEquals((new BigInteger(5))->testbit(0), true);
        $this->assertEquals((new BigInteger(6))->testbit(0), false);
        $this->assertEquals((new BigInteger(6))->testbit(1), true);
        $this->assertEquals((new BigInteger(5))->testbit(1), false);
        $this->assertEquals((new BigInteger(132))->testbit(7), true);
        $this->assertEquals((new BigInteger(81))->testbit(7), false);
        $this->assertEquals((new BigInteger(258))->testbit(8), true);
        $this->assertEquals((new BigInteger(253))->testbit(8), false);
        $this->assertEquals((new BigInteger(20))->shiftLeft(3)->toString(), "160");
        $this->assertEquals((new BigInteger(20))->shiftRight(3)->toString(), "2");
        $this->assertEquals((new BigInteger(20))->scan0(2), 3);
        $this->assertEquals((new BigInteger(20))->scan1(3), 4);
        $this->assertEquals((new BigInteger(20))->cmp(22), -1);
        $this->assertEquals((new BigInteger(20))->cmp(20), 0);
        $this->assertEquals((new BigInteger(20))->cmp(18), 1);
        $this->assertEquals((new BigInteger(20))->equals(20), true);
        $this->assertEquals((new BigInteger(20))->equals(21), false);
        $this->assertEquals((new BigInteger(-20))->sign(), -1);
        $this->assertEquals((new BigInteger(0))->sign(), 0);
        $this->assertEquals((new BigInteger(20))->sign(), 1);
        $this->assertEquals((new BigInteger("-20"))->toString(), "-20");
        $this->assertEquals((new BigInteger("-14", 16))->toString(), "-20");
        $this->assertEquals((new BigInteger("-10100", 2))->toString(), "-20");
    }

    public function testBig(): void
    {
        $bits = "1001010111010010100001000101110110100001000101101000110101010101001";
        $hex = "eeaf0ab9adb38dd69c33f80afa8fc5e86072618775ff3c0b9ea2314c9c256576d674df7496ea81d3383b4813d692c6e0e0d5d8e250b98be48e495c1d6089dad15dc7d7b46154d6b6ce8ef4ad69b15d4982559b297bcf1885c529f566660e57ec68edbc3c05726cc02fd4cbf4976eaa9afd5138fe8376435b9fc61d2fc0eb06e3";
        $dec = "436529472098746319073192837123683467019263172846";
        $bytes = hex2bin($hex);

        $this->assertEquals((new BigInteger($bits, 2))->toBits(), $bits);
        $this->assertEquals((new BigInteger($dec, 10))->toDec(), $dec);
        $this->assertEquals((new BigInteger($hex, 16))->toHex(), $hex);
        $this->assertEquals((new BigInteger($bytes, 256))->toBytes(), $bytes);
    }
}
