<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Code39\Code39;
use Zeus\Barcode\Code39\Code39Mod43;
use Zeus\Barcode\Code39\Code39Ext;
use Zeus\Barcode\Code39\Code39ExtMod43;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Code39Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateCode39Test()
    {
        $dataArr = [
            'BARCODE1%'  => true,
            'BARCODE1%@' => false,
            ''           => false,
            'barcode'    => false,
            'ABCDEFGHI'  => true,
            '0123456789' => true,
            ' -.$/+%'    => true,
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Code39($data);
                $this->assertTrue($info);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info);
            }
        }
    }

    /**
     * @test
     * @depends validateCode39Test
     */
    public function validateCode39ExtTest()
    {
        $dataArr = [
            'BARCODE1%'            => true,
            'BARCODE1%@'           => true,
            ''                     => false,
            'barcode'              => true,
            'ABCDEFGHI'            => true,
            '0123456789'           => true,
            ' -.$/+%'              => true,
            "\x000\x01\x4f\x38\x0a" => true,
            "\x000\x01\x4f\x38\x80" => false,
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Code39Ext($data);
                $this->assertTrue($info);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info);
            }
        }
    }
    
    /**
     * @test
     * @depends validateCode39ExtTest
     */
    public function validateMod43Test()
    {
        try {
            $bc = new Code39Mod43('BARCODE1%P', true);
            $bc = new Code39ExtMod43('BARCODE1%.', true);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->assertTrue(false);
        }
    }
    
    /**
     * @test
     * @depends validateMod43Test
     */
    public function infoTest()
    {
        $bc = new Code39('12345');
        $this->assertEquals($bc->getData(), '12345');
        $this->assertEquals($bc->getEncoded()->getBinary(), '100010111011101011101000101011101011100010101110111011100010101010100011101011101110100011101010100010111011101');
        
        $bc = new Code39Ext('$%&b@');
        $this->assertEquals($bc->getData(), '$%&b@');
        $this->assertEquals($bc->getEncoded()->getBinary(), '10001011101110101000100010100010101011100010111010001000101000101110101110001010100010001010001010111011100010101000101000100010101110100010111010100010001000101000111010101110100010111011101');
        
        $bc = new Code39Mod43('12345', false);
        $this->assertEquals($bc->getData(), '12345F');
        $this->assertEquals($bc->getChecksum(), 'F');
        $this->assertEquals($bc->getEncoded()->getBinary(), '1000101110111010111010001010111010111000101011101110111000101010101000111010111011101000111010101011101110001010100010111011101');
        
        $bc = new Code39ExtMod43('$%&b@', false);
        $this->assertEquals($bc->getData(), '$%&b@T');
        $this->assertEquals($bc->getChecksum(), 'T');
        $this->assertEquals($bc->getEncoded()->getBinary(), '100010111011101010001000101000101010111000101110100010001010001011101011100010101000100010100010101110111000101010001010001000101011101000101110101000100010001010001110101011101010111011100010100010111011101');
    }
    
    /**
     * @test
     */
    public function convertionTest()
    {
        $bc = new Code39('BARCODE1%');
        $this->assertInstanceOf(Code39Mod43::class, $bc->withChecksum());
        $this->assertInstanceOf(Code39ExtMod43::class, $bc->withChecksum()->toExtended());
        $this->assertInstanceOf(Code39Ext::class, $bc->toExtended());
        $this->assertInstanceOf(Code39ExtMod43::class, $bc->toExtended()->withChecksum());
        $this->assertTrue($bc->withoutChecksum() === $bc);
        
        $bc = $bc->withChecksum();
        $this->assertTrue($bc->withChecksum() === $bc);
        
        $bc = $bc->toExtended();
        $this->assertTrue($bc->toExtended() === $bc);
    }
    
    /**
     * @test
     */
    public function factoryTest()
    {
        $tests = [
            'ABC123'     => [Code39::class, false, false],
            'ABC123@'    => [Code39Ext::class, false, false],
            'BARCODE1%P' => [Code39Mod43::class, true, false],
            'BARCODE1%'  => [Code39Mod43::class, false, true],
            '$%&b@'      => [Code39ExtMod43::class, false, true],
        ];
        
        foreach ($tests as $data => $info) {
            $this->assertInstanceOf($info[0], Code39::factory($data, $info[1], $info[2]));
        }
    }
}
