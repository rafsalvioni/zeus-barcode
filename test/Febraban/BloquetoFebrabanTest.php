<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Febraban\Bloqueto;

class BloquetoFebrabanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '03399.72101 20500.000110 04506.001017 9 66690000041336' => [true, true],
            '03399.72101 20500.000110 04833.601018 4 67000000039211' => [true, true],
            '03399.72101 20500.000110 05858.701013 1 67600000040286' => [true, true],
            '23793130149001056267763005600000266790000050232'        => [true, true],
            '23793130149001065777445005600007767390000050232'        => [true, true],
            '03399541176450000000153290801017767100000055000'        => [true, true],
            '03399541176450000000153580001013167700000055000'        => [true, true],
            '34191.73004 30378.418781 10433.700001 2 66950000019800' => [true, true],
            '03399541176450000000153580001013167500000055000'        => [true, false],
            '033995411764500000001535800010131677000000550001'       => [true, false],
            '033995411764500000001535800010131677000000550t01'       => [true, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Bloqueto($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
    
    /**
     * @test
     */
    public function testValores()
    {
        $linha = '03399.72101 20500.000110 04833.601018 4 67000000039211';
        $bc = new Bloqueto($linha);
        $this->assertEquals($bc->getPrintableData(), $linha);
        $this->assertEquals($bc->getCodigoBanco(), '033');
        $this->assertEquals($bc->getCodigoMoeda(), '9');
        $this->assertEquals($bc->getFatorVecto(), '6700');
        $this->assertEquals($bc->getVencto()->format('d-m-Y'), '10-02-2016');
        $this->assertEquals($bc->getValor(), 392.11);
        $this->assertEquals($bc->getChecksum(), 4);
        
        $campoLivre = \substr($bc->getData(true), 19);
        $this->assertEquals($bc->getCampoLivre(), $campoLivre);
        
        $bc = Bloqueto::builder('001', null, 39585.78);
        $this->assertEquals($bc->getCodigoBanco(), '001');
        $this->assertEquals($bc->getVencto()->format('d-m-Y'), (new \DateTime())->format('d-m-Y'));
        $this->assertEquals($bc->getValor(), 39585.78);
    }
}
