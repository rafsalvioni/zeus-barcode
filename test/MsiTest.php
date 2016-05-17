<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Msi\Msi;
use Zeus\Barcode\Msi\MsiMod10;
use Zeus\Barcode\Msi\MsiMod11;

/**
 * 
 * @author Rafael M. Salvioni
 */
class MsiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '80523'     => [true, true],
            '805238'    => [true, true],
            '76544y210' => [false, false],
            '8'         => [true, false],
            ''          => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Msi($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }

    /**
     * @test
     * @depends validationTest
     */
    public function withChecksumTest()
    {
        $bc = new MsiMod10('80523');
        $this->assertEquals($bc->getData(), '80523');
        $this->assertEquals($bc->getChecksum(), '3');
        $this->assertEquals($bc->getRawData(), '8052');
        $this->assertEquals($bc->getEncoded(), '1101101001001001001001001001001101001101001001101001001001101101001');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new MsiMod11('80523', false);
        $this->assertEquals($bc->getData(), '805238');
        $this->assertEquals($bc->getChecksum(), '8');
        $this->assertEquals($bc->getRawData(), '80523');
        $this->assertEquals($bc->getEncoded(), '1101101001001001001001001001001101001101001001101001001001101101101001001001001');
    }
    
    /**
     * @test
     */
    public function conversionTest()
    {
        $bc = new Msi('8052');
        $this->assertEquals($bc->withChecksumMod10()->getChecksum(), '3');
        $this->assertEquals($bc->withChecksumMod11()->getChecksum(), '7');
        $this->assertEquals($bc->withChecksumMod1110()->getChecksum(), '71');
        $this->assertEquals($bc->withChecksum2Mod10()->getChecksum(), '39');
        $this->assertEquals($bc->withChecksum2Mod10()->withoutChecksum()->getData(), '8052');
    }
}
