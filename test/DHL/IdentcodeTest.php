<?php

namespace ZeusTest\Barcode\DHL;

use ZeusTest\Barcode\Code2of5\Interleaved25Test;
use Zeus\Barcode\DHL\Identcode;

/**
 * 
 * @author Rafael M. Salvioni
 */
class IdentcodeTest extends Interleaved25Test
{
    /**
     * @test
     */
    public function validationTest()
    {
        parent::validationTest();
        $dataArr = [
            '563102430313'    => [true, true],
            '563102430314'    => [true, false],
            '56310243031'     => [false, true],
            '012345679876'    => [false, false],
            '012345679876892' => [false, true],
            '123456w'         => [false, false],
            '2'               => [false, true]
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Identcode($data, $info[0]);
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
        $bc = Identcode::builder('56', '310', '243031');
        $this->assertEquals($bc->getMailCenter(), '56');
        $this->assertEquals($bc->getCustomerCode(), '310');
        $this->assertEquals($bc->getDeliveryNumber(), '243031');
        $this->assertEquals($bc->getChecksum(), '3');
        
        $this->assertEquals($bc->withMailCenter('35')->getMailCenter(), '35');
        $this->assertEquals($bc->withCustomerCode('5')->getCustomerCode(), '005');
        $this->assertEquals($bc->withDeliveryNumber('890')->getDeliveryNumber(), '000890');
        
        $this->assertEquals($bc->getDataToDisplay(), '56.310 243.031 3');
        
        try {
            $bc->withMailCenter('354');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $bc->withCustomerCode('87d3');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $bc->withDeliveryNumber('765rt42');
            $this->assertTrue(false);
        } catch (\Zeus\Barcode\Exception $ex) {
            $this->assertTrue(true);
        }
    }
}
