<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\Ean8;

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
        $this->assertEquals($bc->getRealData(), '5512345');
        $this->assertEquals($bc->getEncoded()->getBinary(), '1010110001011000100110010010011010101000010101110010011101000100101');
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
        $this->assertEquals($bc->getRealData(), '5512345');
        $this->assertEquals($bc->getEncoded()->getBinary(), '1010110001011000100110010010011010101000010101110010011101000100101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function fieldsTest()
    {
        $bc = new Ean8('7501031', false);
        $this->assertEquals($bc->getSystemCode(), '750');
        $this->assertEquals($bc->getItemCode(), '1031');
        
        $bc = new Ean8('9900103', false);
        $this->assertEquals($bc->getSystemCode(), '99');
        $this->assertEquals($bc->getItemCode(), '00103');
    }
    
    /**
     * @test
     * @depends fieldsTest
     */
    public function withTest()
    {
        $bc = new Ean8('7501031', false);
        
        $bc = $bc->withSystemCode('99');
        $this->assertEquals($bc->getSystemCode(), '99');
        $bc = $bc->withItemCode('104');
        $this->assertEquals($bc->getItemCode(), '00104');
        
        $bc = $bc->withSystemCode('750');
        $this->assertEquals($bc->getSystemCode(), '750');
        $bc = $bc->withItemCode('154');
        $this->assertEquals($bc->getItemCode(), '0154');
    }
    
    /**
     * @test
     * @depends withTest
     * @expectedException \Zeus\Barcode\Upc\Ean8Exception
     */
    public function systemCodeErrorTest()
    {
        $bc = new Ean8('9911031', false);
        $bc = $bc->withSystemCode('750');
    }
}
