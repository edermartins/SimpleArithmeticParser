<?php

/**
 * @author Eder Jani Martins
 * Class TreeLeaf Classe fim, armazena apenas o valor
 */
class TreeLeaf extends AbstractTree
{
    public function __construct($value){
        $this->setValue($value);
    }

    /**
     * @return string Retorna o valor
     */
    public function toString()
    {
        return $this->getValue();
    }

}