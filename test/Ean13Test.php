<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Ean13;

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
                new Ean13($data, $info[0]);
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
        $bc = new Ean13('7501031311309');
        $this->assertEquals($bc->getData(), '7501031311309');
        $this->assertEquals($bc->getChecksum(), '9');
        $this->assertEquals($bc->getData(false), '750103131130');
        $this->assertEquals($bc->getEncoded(), '10101100010100111001100101001110111101011001101010100001011001101100110100001011100101110100101');
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
        $this->assertEquals($bc->getData(false), '750103131130');
        $this->assertEquals($bc->getEncoded(), '10101100010100111001100101001110111101011001101010100001011001101100110100001011100101110100101');
    }
}
