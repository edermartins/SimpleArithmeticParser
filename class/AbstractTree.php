<?php

/**
 * Class AbstractTreeNode
 * Classe abstrata para criação de uma árvore binária que irá armazenar as expressão aritmética
 *
 * @author  Eder Jani Martins
 */
abstract class AbstractTree
{
    /**
     * @var string O valor representa a parte numérica da expressão
     */
    private $value = null;
    /**
     * @var TreeNode|TreeLeaf Nó esquerdo
     */
    private $left = null;
    /**
     * @var TreeNode|TreeLeaf Nó direito
     */
    private $right = null;
    /**
     * @var string Operador: +, -, *, /, ^
     */
    private $operator = null;

    /**
     * @return string Retorna o operador
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator Atribui o operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string Retorna o valor
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value Atribui o valor
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return TreeNode|TreeLeaf Retorna o nó esquerdo
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param TreeNode|TreeLeaf $left atribui o nó esquerdo
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return TreeNode|TreeLeaf Retorna o nó direito
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param TreeNode|TreeLeaf $right Atribui o nó direito
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return string Para ser inplementada na classe, para retornar o operador ou valor
     */
    abstract public function toString();
}