<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\ISMN;

/**
 * 
 * @author Rafael M. Salvioni
 */
class ISMNTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '9790260000438'  => [true, true],
            '9780260000438'  => [true, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new ISMN($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertEquals($bc->getData(), $data);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
