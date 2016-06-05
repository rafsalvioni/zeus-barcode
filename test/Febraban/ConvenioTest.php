<?php

namespace ZeusTest\Barcode\Febraban;

use Zeus\Barcode\Febraban\Convenio;

/**
 * 
 * @author Rafael M. Salvioni
 */
class ConvenioFebrabanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '83680000000-9 86500048100-8 56041708261-0 00100993938-8' => [true, true],
            '836700000000 817400481005 460423771316 001009939388'     => [true, true],
            '8360000000901500481008804135179100100993938'             => [false, true],
            '836500000002 765500481005 007077778715 001009939388'     => [true, true],
            '83600000000839700481002004712368100100993938'            => [true, true],
            '8460000001145610290614448590120160120999999'             => [false, true],
            '84610000001-3 28571029061-7 44485901201-5 60218999999-1' => [true, true],
            '84600000001142910290614448590120160318999999'            => [true, true],
            '846800000016 108810290610 444859012015 604199999995'     => [true, true],
            '84630000001142910290614448590120160318999999'            => [true, false],
            '74600000001142910290614448590120160318999999'            => [true, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Convenio($data, $info[0]);
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
        $linha = '836700000000 901500481006 880413517918 001009939388';
        $bc = new Convenio($linha);
        $this->assertEquals($bc->getDataToDisplay(), $linha);
        $this->assertEquals($bc->getSegmento(), '3');
        $this->assertEquals($bc->getValor(), 90.15);
        $this->assertEquals($bc->getChecksum(), 7);
        $this->assertEquals($bc->getCampoLivre(), '804135179100100993938');
        
        $bc = Convenio::builder(6, 39585.7843);
        $this->assertEquals($bc->getSegmento(), '6');
        $this->assertEquals($bc->getValor(), 39585.78);
    }
}
