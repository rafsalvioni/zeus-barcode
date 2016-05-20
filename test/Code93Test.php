<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Code93;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Code93Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateTest()
    {
        $dataArr = [
            'TEST93+6'             => [true, true],
            'TEST93'               => [false, true],
            'TEST936-'             => [true, false],
            ''                     => [false, false],
            'barcode'              => [false, true],
            'ABCDEFGHI'            => [false, true],
            '0123456789'           => [false, true],
            ' -.$/+%'              => [false, true],
            "\x00\x01\x4f\x38\x0a" => [false, true],
            "\x00\x01\x4f\x38\x80" => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Code93($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
    
    /**
     * @test
     * @depends validateTest
     */
    public function infoTest()
    {
        $bc = new Code93('TEST93', false);
        $this->assertEquals($bc->getData(), 'TEST93+6');
        $this->assertEquals($bc->getRealData(), 'TEST93');
        $this->assertEquals($bc->getChecksum(), '+6');
        $this->assertEquals($bc->getBarSet()->getBinary(), '1010111101101001101100100101101011001101001101000010101010000101011101101001000101010111101');
    }
}
