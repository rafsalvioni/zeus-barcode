<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Interleaved25;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Interleaved25Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '12345670'  => [true, true],
            '12345674'  => [true, false],
            '012345670' => [true, false],
            '01234567'  => [false, false],
            '1234567'   => [false, true],
            '123456w'   => [false, false],
            '2'         => [false, true]
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Interleaved25($data, $info[0]);
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
        $bc = new Interleaved25('12345670');
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getData(false), '1234567');
        $this->assertEquals($bc->getEncoded(), '1010110100101011001101101001010011010011001010101010011001101101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Interleaved25('1234567', false);
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getData(false), '1234567');
        $this->assertEquals($bc->getEncoded(), '1010110100101011001101101001010011010011001010101010011001101101');
    }
}
