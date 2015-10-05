<?php
/**
 * @author Eder Jani Martins
 * Class TreeNodeFactory Abstrai a complexidade das classes AbstractTree, TreeNode e TreeLeaf, que implementam um
 * Composite Pattern. Retorna a instância sem a necessidade do cliente conhecer as classes recursivas.
 */
class TreeNodeFactory
{
	static function makeLeaf($value){
		return new TreeLeaf($value);
	}

    static function makeTree($operator){
        return new TreeNode($operator);
    }
}