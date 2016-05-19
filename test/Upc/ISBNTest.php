<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\ISBN;

/**
 * 
 * @author Rafael M. Salvioni
 */
class ISBNTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '9783161484100'  => [true, true],
            '9580123456786'  => [true, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new ISBN($data, $info[0]);
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
    public function fromIsbnTest()
    {
        $b = ISBN::fromISBN('0-93-7175-59-5');
        $this->assertEquals($b->getRealData(), '978093717559');
    }
}
