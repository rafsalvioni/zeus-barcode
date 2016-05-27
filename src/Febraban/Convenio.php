<?php

namespace Zeus\Barcode\Febraban;

/**
 * Implementa um código de barras padrão Febraban utilizado na arrecadação
 * de convênios e serviços públicos.
 *
 * @author Rafael M. Salvioni
 * @see https://www.febraban.org.br/7Rof7SWg6qmyvwJcFwF7I0aSDf9jyV/sitefebraban/Codbar4-v28052004.pdf
 */
class Convenio extends AbstractFebraban
{
    /**
     * Identificador do campo Valor
     * 
     */
    const VALOR        = 4;
    /**
     * Identificador do campo Segmento
     * 
     */
    const SEGMENTO     = 1;
    /**
     * Identificador do campo Livre (p/ segmentos 9)
     * 
     */
    const EMPRESA      = 15;
    /**
     * Identificador do campo Livre (p/ segmentos 9)
     * 
     */
    const CAMPO_LIVRE1 = 19;
    /**
     * Identificador do campo Livre  (p/ os outros segmentos)
     * 
     */
    const CAMPO_LIVRE2 = 23;
    
    /**
     * Armazena a quantidade de caracteres para cada campo
     * 
     * @var array
     */
    protected static $tamanhoCampos = [
        self::VALOR        => 11,
        self::SEGMENTO     => 1,
        self::EMPRESA      => 4,
        self::CAMPO_LIVRE1 => 25,
        self::CAMPO_LIVRE2 => 21,
    ];

    /**
     * Cria uma instância da classe a partir dos parâmetros informados.
     * 
     * @param int|string $segmento
     * @param number $valor
     * @param string $campoLivre
     * @return self
     */
    public static function builder($segmento, $valor = 0, $campoLivre = null)
    {
        $data = '896' . self::zeroLeftPadding('', 40);
        
        $me   = new self($data, false);
        return $me->comSegmento($segmento)
                  ->comValor($valor)
                  ->comCampoLivre($campoLivre);
    }

    /**
     * Permite que a instância seja criada a partir da representação
     * numérica do código de barras.
     * 
     * Neste caso, os dígitos verificadores de grupo também serão checados.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @throws ConvenioException Se a linha digitavel for invalida
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (\strlen($data) >= 48) {
            $linha = \preg_replace('/[ -]/', '', $data);
            $linha = \str_split($linha, 12);
            $data  = '';
            
            foreach ($linha as &$grupo) {
                $data .= \substr($grupo, 0, -1);
            }
            
            parent::__construct($data, true);
            $linha = \implode(' ', $linha);
            
            if ($this->getDataToDisplay() != $linha) {
                throw new ConvenioException('Linha inválida!');
            }
        }
        else {
            parent::__construct($data, $hasChecksum);
        }
    }

    /**
     * Retorna o ID do segmento.
     * 
     * 1. Prefeituras;
     * 2. Saneamento;
     * 3. Energia Elétrica e Gás;
     * 4. Telecomunicações;
     * 5. Órgãos Governamentais;
     * 6. Carnes e Assemelhados ou demais Empresas / Órgãos que serão identificadas através do CNPJ.
     * 7. Multas de trânsito
     * 9. Uso exclusivo do banco 
     * 
     * @return string
     */
    public function getSegmento()
    {
        return $this->getCampo(self::SEGMENTO);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o segmento
     * informado.
     * 
     * 1. Prefeituras;
     * 2. Saneamento;
     * 3. Energia Elétrica e Gás;
     * 4. Telecomunicações;
     * 5. Órgãos Governamentais;
     * 6. Carnes e Assemelhados ou demais Empresas / Órgãos que serão identificadas através do CNPJ.
     * 7. Multas de trânsito
     * 9. Uso exclusivo do banco 
     * 
     * @param string $segmento
     * @return Convenio
     */
    public function comSegmento($segmento)
    {
        return $this->withCampo(self::SEGMENTO, $segmento);
    }

    /**
     * 
     * @return float
     */
    public function getValor()
    {
        $valor = (int)$this->getCampo(self::VALOR);
        $valor = $valor / 100;
        return $valor;
    }

    /**
     * Retorna um objeto criado a partir do objeto atual com o valor
     * informado.
     * 
     * @param number $valor
     * @return Convenio
     */
    public function comValor($valor)
    {
        $valor = $valor * 100;
        return $this->withCampo(self::VALOR, $valor);
    }
    
    /**
     * 
     * @return string
     */
    public function getCampoLivre()
    {
        if ($this->getSegmento() == '9') {
            $campo = self::CAMPO_LIVRE1;
        }
        else {
            $campo = self::CAMPO_LIVRE2;
        }
        return $this->getCampo($campo);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o campo livre
     * informado.
     * 
     * @param string $campoLivre
     * @return Convenio
     */
    public function comCampoLivre($campoLivre)
    {
        if ($this->getSegmento() == '9') {
            $campo = self::CAMPO_LIVRE1;
        }
        else {
            $campo = self::CAMPO_LIVRE2;
        }
        return $this->withCampo($campo, $campoLivre);
    }
    
    /**
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return 3;
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $id = empty($this->data) ? $data{2} : $this->data{2};
        switch ($id) {
            case '6':
            case '7':
                return self::modulo10($data);
            default:
                return self::modulo11($data);
        }
    }
    
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        if (\preg_match('/^8[1-9][6-9]/', $data)) {
            return parent::checkData($data, $hasChecksum);
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    protected function montaLinhaDigitavel()
    {
        $linha = \str_split($this->getData(), 11);
        foreach ($linha as $i => $grupo) {
            $linha[$i] .= $this->calcChecksum($grupo);
        }
        return \implode(' ', $linha);
    }
    
    /**
     * 
     */
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();
        $this->setOption('textposition', 'top');
        $this->setOption('fontsize', 3);
    }

    /**
     * Retorna um valor de campo contido nos dados do código de barras.
     * 
     * @param int $campo Constantes da classe
     * @return string
     */
    protected function getCampo($campo)
    {
        return $this->getDataPart($campo, self::$tamanhoCampos[$campo]);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual substituindo o valor
     * do campo especificado.
     * 
     * $valor será completado com zeros a esquerda utilizando o tamnho do campo
     * registrado.
     * 
     * @param int $campo Constantes da classe
     * @param string|int Novo valor
     * @return Bloqueto
     */
    protected function withCampo($campo, $valor)
    {
        $data = $this->withDataPart($valor, $campo, self::$tamanhoCampos[$campo]);
        return new self($data, false);
    }
}

class ConvenioException extends Exception {}
