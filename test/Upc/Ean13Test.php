<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\Ean13;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Ean13Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '5901234123457' => [true, true],
            '9780735200449' => [true, true],
            '002210034567'  => [false, true],
            '7123456789011' => [false, false],
            '412345678908'  => [false, false],
            '8x60603226779' => [false, false],
            '8'             => [true, false],
            ''              => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new Ean13($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertEquals($bc->getProductCode(), \substr($data, 7, 5));
                $this->assertEquals($bc->withProductCode('56')->getProductCode(), '00056');
                $this->assertEquals($bc->isUpcaCompatible(), $data{0} == '0');
                if ($bc->isUpcaCompatible()) {
                    $this->assertStringStartsWith(\substr($data, 1), $bc->toUpca()->getData());
                }
                else {
                    try {
                        $bc->toUpca();
                        $this->assertTrue(false);
                    }
                    catch (\Zeus\Barcode\Exception $ex) {
                        $this->assertTrue(true);
                    }
                }
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
        $bc = new Ean13('7501031311309');
        $this->assertEquals($bc->getData(), '7501031311309');
        $this->assertEquals($bc->getChecksum(), '9');
        $this->assertEquals($bc->getRealData(), '750103131130');
        $this->assertEquals($bc->getEncoded()->getBinary(), '10101100010100111001100101001110111101011001101010100001011001101100110100001011100101110100101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Ean13('750103131130', false);
        $this->assertEquals($bc->getData(), '7501031311309');
        $this->assertEquals($bc->getChecksum(), '9');
        $this->assertEquals($bc->getRealData(), '750103131130');
        $this->assertEquals($bc->getEncoded()->getBinary(), '10101100010100111001100101001110111101011001101010100001011001101100110100001011100101110100101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function fieldsTest()
    {
        $bc = new Ean13('750103131130', false);
        $this->assertEquals($bc->getSystemCode(), '750');
        $this->assertEquals($bc->getManufacturerCode(), '1031');
        $this->assertEquals($bc->getProductCode(), '31130');
        
        $bc = new Ean13('990010313113', false);
        $this->assertEquals($bc->getSystemCode(), '99');
        $this->assertEquals($bc->getManufacturerCode(), '00103');
        $this->assertEquals($bc->getProductCode(), '13113');
    }
    
    /**
     * @test
     * @depends fieldsTest
     */
    public function withTest()
    {
        $bc = new Ean13('750103131130', false);
        
        $bc = $bc->withSystemCode('99');
        $this->assertEquals($bc->getSystemCode(), '99');
        $bc = $bc->withManufacurerCode('104');
        $this->assertEquals($bc->getManufacturerCode(), '00104');
        $this->assertEquals($bc->getProductCode(), '31130');
        
        $bc = $bc->withSystemCode('750');
        $this->assertEquals($bc->getSystemCode(), '750');
        $bc = $bc->withManufacurerCode('154');
        $this->assertEquals($bc->getManufacturerCode(), '0154');
        $this->assertEquals($bc->getProductCode(), '31130');
    }
    
    /**
     * @test
     * @depends withTest
     * @expectedException \Zeus\Barcode\Upc\Ean13Exception
     */
    public function systemCodeErrorTest()
    {
        $bc = new Ean13('991103131130', false);
        $bc = $bc->withSystemCode('750');
    }
}
