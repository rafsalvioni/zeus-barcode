<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Code93;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Code93Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function validateTest()
    {
        $dataArr = [
            'TEST93+6'             => true,
            'TEST93'               => true,
            'TEST936-'             => false,
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
                new Code93($data);
                $this->assertTrue($info);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info);
            }
        }
    }
    
    /**
     * @test
     * @depends validateTest
     */
    public function infoTest()
    {
        $bc = new Code93('TEST93');
        $this->assertEquals($bc->getData(), 'TEST93');
        $this->assertEquals($bc->getEncoded()->getBinary(), '1010111101101001101100100101101011001101001101000010101010000101011101101001000101010111101');
    }
}
