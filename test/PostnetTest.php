<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Postnet;

/**
 * 
 * @author Rafael M. Salvioni
 */
class PostnetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '801221905'  => [true, false],
            '8012219052' => [true, true],
            ''           => [false, false],
            '2'          => [true, false],
            '2345D'      => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Postnet($data, $info[0]);
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
        $bc = new Postnet('801221905', false);
        $this->assertEquals($bc->getData(), '8012219052');
        $this->assertEquals($bc->getRealData(), '801221905');
        $this->assertEquals($bc->getChecksum(), '2');
        $this->assertEquals($bc->getEncoded()->getBinary(), '1100101100000011001010010100011101001100001010001011');
    }
}
