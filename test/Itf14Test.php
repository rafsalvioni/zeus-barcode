<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Itf14;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Itf14Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '00012345678905' => [true, true],
            '12345678905'    => [true, true],
            '00012345678904' => [true, false],
            '9001234567890'  => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Itf14($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
