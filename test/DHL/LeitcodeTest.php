<?php

namespace ZeusTest\Barcode\DHL;

use ZeusTest\Barcode\Code2of5\Interleaved25Test;
use Zeus\Barcode\DHL\Leitcode;

/**
 * 
 * @author Rafael M. Salvioni
 */
class LeitcodeTest extends Interleaved25Test
{
    /**
     * @test
     */
    public function validationTest()
    {
        parent::validationTest();
        $dataArr = [
            '21348075016401'   => [true, true],
            '21348075016403'   => [true, false],
            '2134807501640'    => [false, true],
            '2134807501640198' => [false, false],
            '0123456798'       => [false, true],
            '123456w'          => [false, false],
            '2'                => [false, true]
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Leitcode($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
    
    /**
     * @test
     */
    public function infoTest()
    {
        $bc = Leitcode::builder('21348', '75', '16', '40');
        $this->assertEquals($bc->getZipCode(), '21348');
        $this->assertEquals($bc->getStreetCode(), '075');
        $this->assertEquals($bc->getHouseNumber(), '016');
        $this->assertEquals($bc->getProductCode(), '40');
        $this->assertEquals($bc->getChecksum(), '1');
        
        $this->assertEquals($bc->withZipCode('35')->getZipCode(), '00035');
        $this->assertEquals($bc->withStreetCode('5')->getStreetCode(), '005');
        $this->assertEquals($bc->withHouseNumber('890')->getHouseNumber(), '890');
        $this->assertEquals($bc->withProductCode('90')->getProductCode(), '90');
        
        $this->assertEquals($bc->getDataToDisplay(), '21348.075.016.40 1');

        try {
            $bc->withZipCode('9035x');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $bc->withStreetCode('9035');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $bc->withHouseNumber('90x');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $bc->withProductCode('999');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
    }
}
