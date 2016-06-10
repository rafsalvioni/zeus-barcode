<?php

namespace Zeus\Barcode\Febraban;

/**
 * Implementa um código de barras padrão Febraban utilizado para pagamentos
 * diversos.
 *
 * @author Rafael M. Salvioni
 * @see http://download.itau.com.br/bankline/cobranca_cnab240.pdf
 */
class Bloqueto extends AbstractFebraban
{
    /**
     * Identificador do campo Cód. do Banco
     * 
     */
    const BANCO        = 0;
    /**
     * Identificador do campo Moeda
     * 
     */
    const MOEDA        = 3;
    /**
     * Identificador do campo Fator de Vencimento
     * 
     */
    const FATOR_VENCTO = 5;
    /**
     * Identificador do campo Valor
     * 
     */
    const VALOR        = 9;
    /**
     * Identificador do campo Livre
     * 
     */
    const CAMPO_LIVRE  = 19;
    
    /**
     * Armazena a quantidade de caracteres para cada campo
     * 
     * @var array
     */
    protected static $tamanhoCampos = [
        self::BANCO        => 3,
        self::MOEDA        => 1,
        self::FATOR_VENCTO => 4,
        self::VALOR        => 10,
        self::CAMPO_LIVRE  => 25,
    ];

    /**
     * Armazena a data base para cálculo do Fator de Vencimento
     * 
     * @var \DateTime
     */
    private static $dataBase;
    
    /**
     * Cria uma instância da classe a partir dos parâmetros informados.
     * 
     * @param string|int $banco Cód. do Banco
     * @param \DateTime $vencto Vencimento. Se nulo, não possui vencimento
     * @param float $valor
     * @param string $campoLivre
     * @return self
     */
    public static function builder(
        $banco, \DateTime $vencto = null, $valor = 0, $campoLivre = null
    ){
        $data = '0009' . self::zeroLeftPadding('', 39);
        $me   = new self($data, false);
        
        return $me->comCodigoBanco($banco)
                  ->comValor($valor)
                  ->comVencto($vencto)
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
     * @throws BloquetoFebrabanException Se a linha digitavel for invalida
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (\strlen($data) >= 45) {
            $linha = \preg_replace('/[ .-]/', '', $data);
            $data  = \substr($linha, 0, 4) .
                     \substr($linha, 32) .
                     \substr($linha, 4, 5) .
                     \substr($linha, 10, 10) .
                     \substr($linha, 21, 10);
            
            parent::__construct($data, true);
            
            if (\preg_replace('/[^\d]+/', '', $this->getDataToDisplay()) != $linha) {
                throw new BloquetoException('Linha inválida!');
            }
        }
        else {
            parent::__construct($data, $hasChecksum);
        }
        
        if (empty(self::$dataBase)) {
            self::$dataBase = new \DateTime('1997-10-07');
        }
    }

    /**
     * Retorna o código do banco.
     * 
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->getField(self::BANCO);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o código de banco
     * informado.
     * 
     * @param string|int $codBanco
     * @return Bloqueto
     */
    public function comCodigoBanco($codBanco)
    {
        return $this->withField(self::BANCO, $codBanco);
    }

    /**
     * Retorna o código da moeda.
     * 
     * @return string
     */
    public function getCodigoMoeda()
    {
        return $this->getField(self::MOEDA);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o código de moeda
     * informado.
     * 
     * @param string|int $codMoeda
     * @return Bloqueto
     */
    public function comCodigoMoeda($codMoeda)
    {
        return $this->withField(self::MOEDA, $codMoeda);
    }

    /**
     * Retorna o fator de vencimento.
     * 
     * @return string
     */
    public function getFatorVecto()
    {
        return $this->getField(self::FATOR_VENCTO);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o fator de vencimento
     * informado.
     * 
     * @param string|int $fator
     * @return Bloqueto
     */
    public function comFatorVencto($fator)
    {
        return $this->withField(self::FATOR_VENCTO, $fator);
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getVencto()
    {
        $fator = (int)$this->getFatorVecto();
        if ($fator > 0) {
            $vecto = clone self::$dataBase;
            $vecto->modify("+$fator days");
            return $vecto;
        }
        return null;
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o vencimento
     * informado.
     * 
     * Se $vencto for nulo significa que não possui vencimento.
     * 
     * @param \DateTime $vencto
     * @return Bloqueto
     */
    public function comVencto(\DateTime $vencto = null)
    {
        $diff = $vencto ?
                (int)self::$dataBase->diff($vencto)->days :
                0;
        return $this->comFatorVencto($diff);
    }

    /**
     * 
     * @return float
     */
    public function getValor()
    {
        $valor = (int)$this->getField(self::VALOR);
        $valor = $valor / 100;
        return $valor;
    }

    /**
     * Retorna um objeto criado a partir do objeto atual com o valor
     * informado.
     * 
     * @param number $valor
     * @return Bloqueto
     */
    public function comValor($valor)
    {
        $valor = (int)($valor * 100);
        return $this->withField(self::VALOR, $valor);
    }
    
    /**
     * 
     * @return string
     */
    public function getCampoLivre()
    {
        return $this->getField(self::CAMPO_LIVRE);
    }
    
    /**
     * Retorna um objeto criado a partir do objeto atual com o campo livre
     * informado.
     * 
     * @param string $campoLivre
     * @return Bloqueto
     */
    public function comCampoLivre($campoLivre)
    {
        return $this->withField(self::CAMPO_LIVRE, $campoLivre);
    }
    
    /**
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return 4;
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::modulo11($data);
    }
    
    /**
     * 
     * @return string
     */
    protected function montaLinhaDigitavel()
    {
        $campoLivre = $this->getCampoLivre();
        $linha      = [];
        
        $linha[]    = \substr($this->data, 0, 4) .
                      \substr($campoLivre, 0, 3) .
                      \substr($campoLivre, 3, 2);
        
        $linha[0]  .= self::modulo10($linha[0]);
        $linha[0]   = \substr_replace($linha[0], '.', 5, 0);
                 
        $linha[]    = \substr($campoLivre, 5, 6) .
                      \substr($campoLivre, 11, 1) .
                      \substr($campoLivre, 12, 3);
        
        $linha[1]  .= self::modulo10($linha[1]);
        $linha[1]   = \substr_replace($linha[1], '.', 5, 0);
        
        $linha[]    = \substr($campoLivre, 15, 1) .
                      \substr($campoLivre, 16, 6) .
                      \substr($campoLivre, 22);
        
        $linha[2]  .= self::modulo10($linha[2]);
        $linha[2]   = \substr_replace($linha[2], '.', 5, 0);
        
        $linha[]    = $this->getChecksum();
        
        $linha[]    = $this->getFatorVecto() .
                      \substr($this->data, 9, 10);
        
        return \implode(' ', $linha);
    }
    
    /**
     * 
     */
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();
        $this->setOption('showtext', false);
    }
    
    /**
     * 
     * @param int $field
     * @return int
     */
    protected function getFieldLength($field)
    {
        return self::$tamanhoCampos[$field];
    }
}

/**
 * Class's exception
 * 
 */
class BloquetoException extends Exception {}
