<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\Base58;
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase
{
    private $testDecoded = "12426a2b555065c79b8d6293c5dd18c25a25bae1a8b8c67ceac7484133e6798579bba3444bd48d5d9fcffa64d805e3977b07e2d420a2212df3d612a5dbcc67653844ded707";
    private $testEncoded = "4495qPNxDGAT241zya1WdwG5YU6RQ6s7ZgGeQryFtooAMMxoqx2N2oNTjP5NTqDf9GMaZ52iS2Q6xhnWyW16cdi47MCsVRg";

    public function testEncode()
    {
        $this->assertSame($this->testEncoded, Base58::encode($this->testDecoded));

        $this->expectException(TypeError::class);
        Base58::encode("invalid");
    }

    public function testDecode()
    {
        $this->assertSame($this->testDecoded, Base58::decode($this->testEncoded));

        $this->expectException(Exception::class);
        Base58::decode("invalid");
    }
}
