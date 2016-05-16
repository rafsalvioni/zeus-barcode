<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Ean8;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Ean8Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '96385074'  => [true, true],
            '95050003'  => [true, true],
            '9031101'   => [false, true],
            '0123450'   => [false, false],
            '401234955' => [false, false],
            '76544y210' => [false, false],
            '8'         => [true, false],
            ''          => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Ean8($data, $info[0]);
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
        $bc = new Ean8('55123457');
        $this->assertEquals($bc->getData(), '55123457');
        $this->assertEquals($bc->getChecksum(), '7');
        $this->assertEquals($bc->getDataWithoutChecksum(), '5512345');
        $this->assertEquals($bc->getEncoded(), '1010110001011000100110010010011010101000010101110010011101000100101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Ean8('5512345', false);
        $this->assertEquals($bc->getData(), '55123457');
        $this->assertEquals($bc->getChecksum(), '7');
        $this->assertEquals($bc->getDataWithoutChecksum(), '5512345');
        $this->assertEquals($bc->getEncoded(), '1010110001011000100110010010011010101000010101110010011101000100101');
    }
}
