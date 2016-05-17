<?php

namespace Zeus\Barcode\Febraban;

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\FixedLengthInterface;

/**
 * Classe abstrata para criação de códigos de barra padrão Febraban.
 * 
 * Febraban is a brazilian organization that controls bank's rules and methods.
 * The package's classe's methods and documentation are writed in portuguese.
 *
 * @author Rafael M. Salvioni
 * @see https://www.febraban.org.br/7Rof7SWg6qmyvwJcFwF7I0aSDf9jyV/sitefebraban/Codbar4-v28052004.pdf
 */
abstract class AbstractFebraban extends Interleaved implements
    FixedLengthInterface
{
    /**
     * Armazena a representação dos dados no padrão Febraban
     * 
     * @var string
     */
    protected $linhaDigitavel;
            
    /**
     * Calculo módulo 11.
     * 
     * @param string $data
     * @return int
     */
    protected static function modulo11($data)
    {
        $data   = \str_split($data);
        $sum    = 0;
        $weight = 2;
        
        while (!empty($data)) {
            $sum += $weight++ * (int)\array_pop($data);
            if ($weight > 9) {
                $weight = 2;
            }
        }
        
        $dac = 11 - ($sum % 11);
        if ($dac == 0 || $dac == 1 || $dac == 10 || $dac == 11) {
            return 1;
        }
        return $dac;
    }
    
    /**
     * Cálculo módulo 10.
     * 
     * @param string $data
     * @return int
     */
    protected static function modulo10($data)
    {
        $data   = \str_split($data);
        $sum    = 0;
        $weight = 2;
        
        while (!empty($data)) {
            $prod   = (string)($weight * (int)\array_pop($data));
            $sum   += $prod{0} + (isset($prod{1}) ? $prod{1} : 0);
            $weight = $weight == 2 ? 1 : 2;
        }
        
        $mod = ($sum % 10);
        return $mod == 0 ? 0 : 10 - $mod;
    }
    
    /**
     * Returns 44.
     * 
     * @return int
     */
    public function getLength()
    {
        return 44;
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (\strlen($data) == $this->getLength()) {
            return parent::checkData($data);
        }
        return false;
    }
    
    /**
     * Gera a representação numérica do código de barras.
     * 
     * @return string
     */
    abstract protected function montaLinhaDigitavel();

    /**
     * Retorna a linha digitável.
     * 
     * @return string
     */
    public function getPrintableData()
    {
        if (empty($this->linhaDigitavel)) {
            $this->linhaDigitavel = $this->montaLinhaDigitavel();
        }
        return $this->linhaDigitavel;
    }

    /**
     * Retorna o valor cobrado constante no código de barras.
     * 
     * @return float
     */
    abstract public function getValor();
    
    /**
     * Retorna o campo livre do código de barras.
     * 
     * @return string
     */
    abstract public function getCampoLivre();
}
