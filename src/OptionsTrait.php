<?php

namespace Zeus\Barcode;

/**
 * Trait to encapsulate options routines.
 *
 * @author Rafael M. Salvioni
 */
trait OptionsTrait
{
    /**
     * Options hash
     * 
     * @var array
     */
    protected $options = [];
    
    /**
     * 
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $option = \strtolower($option);
        return isset($this->options[$option]) ?
               $this->options[$option] :
               null;
    }

    /**
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 
     * @param string $option
     * @param mixed $value
     * @return self
     * @throws Exception
     */
    public function setOption($option, $value)
    {
        $option = \strtolower($option);
        if (\array_key_exists($option, $this->options)) {
            $type = \gettype($this->options[$option]);
            if (\gettype($value) == $type) {
                $this->options[$option] = $value;
                return $this;
            }
            throw new Exception("Option \"$option\" should be a $type value");
        }
        throw new Exception("Unknown option \"$option\"");
    }
    
    /**
     * Allows set options using a object property notation.
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $method = "set$name";
        if (\method_exists($this, $method)) {
            $this->$method($value);
        }
        else {
            $this->setOption($name, $value);
        }
    }
    
    /**
     * Gets options as object property.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }
    
    /**
     * Checks if a option is set using object property notation.
     * 
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $name = \strtolower($name);
        return isset($this->options[$name]);
    }
}
