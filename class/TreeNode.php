<?php

/**
 * @author Eder Jani Martins
 * Class TreeNode Implementa o nó binário (com esquerda e direita definidos, é nesta classe que o operador é definido
 */
class TreeNode extends AbstractTree
{
    public function __construct($operation){
        $this->setOperator($operation);
    }

    /**
     * @return string Retorna o operador
     */
    public function toString()
    {
        return $this->getOperator();
    }
}