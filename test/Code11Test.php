<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Code11;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Code11Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '123-455'  => [true, true],
            '123-4552' => [true, true],
            '123-45'   => [false, true],
            '456d09k0' => [false, false],
            '8'        => [true, false],
            ''         => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Code11($data, $info[0]);
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
    public function infoTest()
    {
        $bc = new Code11('123-455');
        $this->assertEquals($bc->getData(), '123-455');
        $this->assertEquals($bc->getChecksum(), '5');
        $this->assertEquals($bc->getRawData(), '123-45');
        $this->assertEquals($bc->getEncoded(), '1011001011010110100101101100101010110101011011011011010110110101011001');
        $this->assertEquals($bc->toDoubleCheck()->getChecksum(), '52');
        
        $bc = new Code11('123-45', false);
        $this->assertEquals($bc->getData(), '123-4552');
        $this->assertEquals($bc->getChecksum(), '52');
        $this->assertEquals($bc->getRawData(), '123-45');
        $this->assertEquals($bc->toSingleCheck()->getChecksum(), '5');

    }
}
