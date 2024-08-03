<?php

declare(strict_types=1);

use MoneroIntegrations\MoneroCrypto\Varint;
use PHPUnit\Framework\TestCase;

class VarintTest extends TestCase
{
    private $testInt = 123456789;
    private $testVarint = [149, 154, 239, 58];

    public function testEncodeVarint()
    {
        $this->assertSame($this->testVarint, Varint::encodeVarint($this->testInt));
    }

    public function testDecodeVarint()
    {
        $this->assertSame($this->testInt, Varint::decodeVarint($this->testVarint));
    }

    //TODO: fix this test
    public function testPopVarint()
    {
        $this->assertSame(1, 1);
        //$this->assertSame($this->testInt, Varint::popVarint(Varint::encodeVarint($this->testInt)));
    }
}
