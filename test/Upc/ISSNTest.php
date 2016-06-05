<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\ISSN;

/**
 * 
 * @author Rafael M. Salvioni
 */
class ISSNTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '9772049363002' => [true, true],
            '9752049363002' => [true, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new ISSN($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertEquals($bc->getData(), $data);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
    
    /**
     * @test
     */
    public function fromTest()
    {
        $bc = ISSN::fromISSN('0378-5955');
        $this->assertEquals($bc->getRealData(), '977037859500');
    }
}
