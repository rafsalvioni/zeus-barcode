<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\UpcA;

/**
 * 
 * @author Rafael M. Salvioni
 */
class UpcATest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '075678164125' => [true, true, false],
            '042100005264' => [true, true, true],
            '03600029145'  => [false, true, false],
            '022100345675' => [false, false, true],
            '61052176530'  => [false, false, false],
            '0223x4545453' => [true, false, false],
            '8'            => [true, false, false],
            ''             => [false, false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new UpcA($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertEquals($bc->isUpcECompatible(), $info[2]);
                $this->assertEquals($bc->getManufacturerCode(), \substr($data, 1, 5));
                $this->assertEquals($bc->getProductCode(), \substr($data, 6, 5));
                $this->assertEquals($bc->withManufacturerCode('345')->getManufacturerCode(), '00345');
                $this->assertEquals($bc->withProductCode('5677')->getProductCode(), '05677');
                if ($info[2]) {
                   $this->assertStringStartsWith($data, $bc->toUpcE()->toUpcA()->getData());
                }
                $this->assertStringStartsWith('0' . $data, $bc->toEan13()->getData());
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
