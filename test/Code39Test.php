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
            "\x00\x01\x4f\x38\x0a" => true,
            "\x00\x01\x4f\x38\x80" => false,
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
        $this->assertEquals($bc->getEncoded(), '100101101101011010010101101011001010110110110010101010100110101101101001101010100101101101');
        
        $bc = new Code39Ext('$%&b@');
        $this->assertEquals($bc->getData(), '$%&b@');
        $this->assertEquals($bc->getEncoded(), '10010110110101001001010010101011001011010010010100101101011001010100100101001010110110010101001010010010101101001011010100100100101001101010110100101101101');
        
        $bc = new Code39Mod43('12345', false);
        $this->assertEquals($bc->getData(), '12345F');
        $this->assertEquals($bc->getChecksum(), 'F');
        $this->assertEquals($bc->getEncoded(), '1001011011010110100101011010110010101101101100101010101001101011011010011010101011011001010100101101101');
        
        $bc = new Code39ExtMod43('$%&b@', false);
        $this->assertEquals($bc->getData(), '$%&b@T');
        $this->assertEquals($bc->getChecksum(), 'T');
        $this->assertEquals($bc->getEncoded(), '100101101101010010010100101010110010110100100101001011010110010101001001010010101101100101010010100100101011010010110101001001001010011010101101010110110010100101101101');
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
}
