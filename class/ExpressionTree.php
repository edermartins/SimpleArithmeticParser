<?php

class ExpressionTree
{
    /**
     * @var TreeNode Raíz da árvore binária
     */
    private $treeNode = null;

    /**
     * @param string $expression String contendo uma expressão aritmética básica, com ou sem espaços
     */
    public function __construct( $expression ){
        $this->treeNode = $this->parser(new Scanner($expression));
    }

    /**
     * Faz a análise da expressão aritmética básica
     * @param Scanner &$input Referência para uma epressão, objeto Scanner
     * @param int $subTreeNode Quando a conta tem operadores que precedência, que é o caso de *, / e ^, eles são passados
     * para o final da setença via este parâmetro
     * @return TreeLeaf|TreeNode Retorna a árvore da expressão
     * @throws Exception Em caso de expressão inválida, lança Exceção
     */
    private function parser(&$input, $subTreeNode=0){
        $treeNode = '';
        if($input->valid()) {
            $firstSequence = $input->getNumeric();
            if ($firstSequence) {
                /**
                 * First sequence is a number, the next one must be a token
                 */
                if($input->valid()) {
                    $token = $input->getToken();
                    if (!$token) {
                        throw new Exception("Depois de uma sequência numérica é obrigatório um operador ou '('");
                    }
                    $treeNode = $this->tokenAnalyzer($input, $token, $firstSequence);
                }else{
                    $treeNode = TreeNodeFactory::makeLeaf($firstSequence);
                }
            } else {
                $token = $input->getToken();
                if (!$token) {
                    throw new Exception("Expressão inválida, recebeu um caracter inválido!");
                }
                $treeNode = $this->tokenAnalyzer($input, $token, $subTreeNode);
            }
        }
        return $treeNode;
    }

    /**
     * Analisa os tokens: +,-,*,/,^ e (
     * @param Scanner $input ponteiro para a Classe que conte a expressão e funcionalidade para iteração
     * @param string $token Um operador matemático ou (
     * @param string|TreeNode $value Valor numérico ou um TreeNode
     * @return null|TreeNode Retorna um TreeNode ou lança exceção
     * @throws Exception
     */
    private function tokenAnalyzer(&$input, $token, $value=''){
        if($value){
            if($this->validOperation($token)){
                return $this->operatorAnalyzer($input, $token, $value);
            }elseif( $this->validRelationship()){
                return $this->relationshipAnalyzer($input, $token, $value);
            }else{
                throw new Exception("Depois de um número é necessário um token de operador ou relacionamento");
            }
        }else{
            if( $token == '(' ){
                $input->unconsume(1);
                $treeNodeNested = $this->parenthesesAnalyzer($input);
                /**
                 * Um conjunto de parênteses foi processado, então verifica se há mais dados para processar
                 */
                if($input->valid()){
                    /**
                     * Depois de um ')' é obrigatório um operador
                     */
                    $token = $input->getToken();
                    if($token){
                        $treeNode = $this->operatorAnalyzer($input, $token, $treeNodeNested);
                    }else{
                        throw new Exception("Depois de fechar um parênteses ')' é obrigatório um operador!");
                    }
                }else{
                    $treeNode = $treeNodeNested;
                }
                return $treeNode;
            }else{
                throw new Exception("Expressão inválida na posição {$input->position()}!");
            }
        }
    }

    /**
     * Analisa os operadores
     * @param Scanner $input ponteiro para a Classe que conte a expressão e funcionalidade para iteração
     * @param string $operator Strign com o operador
     * @param string|TreeNode $value Valor numérico ou uma TreeNode para o caso de aninhamento
     * @return TreeNode Árvore com parte da expressão
     * @throws Exception
     */
    private function operatorAnalyzer(&$input, $operator, $value){
        if($operator == '+'
            || $operator == '-'){

            $treeNode = TreeNodeFactory::makeTree($operator);
            $treeNode->setLeft($value instanceof TreeNode ? $value : TreeNodeFactory::makeLeaf($value));
            $treeNode->setRight($this->parser($input));
        }elseif($operator == '*'
            || $operator == '/'){

            $treeNodeNested = TreeNodeFactory::makeTree($operator);
            $treeNodeNested->setLeft($value instanceof TreeNode ? $value : TreeNodeFactory::makeLeaf($value));
            $nextSequence = $input->getNumeric();
            if($nextSequence){
                $treeNodeNested->setRight(TreeNodeFactory::makeLeaf($nextSequence));
            }else{
                $nextSequence = $input->getToken();
                if($nextSequence == '('){
                    $input->unconsume(1);
                    $treeNodeNested->setRight($this->parenthesesAnalyzer($input));
                }else{
                    throw new Exception("Depois de um operador '*' é obrigatório um número ou '('");
                }
            }
            if($input->valid()){
                $treeNode = $this->parser($input, $treeNodeNested);
            }else{
                $treeNode = $treeNodeNested;
            }
        }
        return $treeNode;
    }

    /**
     * Analisa os operadores de relacionamento
     * @param Scanner $input ponteiro para a Classe que conte a expressão e funcionalidade para iteração
     * @param string $relation Strign com o operador de comparação
     * @param string|TreeNode $value Valor numérico ou uma TreeNode para o caso de aninhamento
     * @return TreeNode Árvore com parte da expressão
     * @throws Exception
     */
    private function relationshipAnalyzer(&$input, $relation, $value){
        //TODO: Not implemented yet
    }

    /**
     * Analisa uma expressão dentro de parênteses
     * @param Scanner $input ponteiro para a Classe que conte a expressão e funcionalidade para iteração
     * @return null|TreeNode <b>null</b> se não tiver nada dentro do parênteses ou a árvore com a expressão
     * @throws Exception
     */
    private function parenthesesAnalyzer(&$input){
        $parentheses = $input->getParanthesesExpression();
        /**
         * Advance the size of the inside expression plus '(' and ')'
         */
        $treeNode = null;
        if($parentheses['expression'] != null) {
            $parenthesesScanner = new Scanner($parentheses['expression']);
            $treeNode = $this->parser($parenthesesScanner);
        }
        return $treeNode;
    }

    /**
     * Verifica se o token é um operador válido
     * @param string $token String com um operador
     * @return bool <b>true</b> se é válido ou <b>false</b> senão
     */
    private function validOperation($token){
        if(in_array($token, Scanner::$OPERATIONS)){
            return true;
        }
        return false;
    }

    /**
     * Verifica se o token é um operador de comparação válido
     * @param string $token String com um operador
     * @return bool <b>true</b> se é válido ou <b>false</b> senão
     */
    private function validRelationship($token){
        if(in_array($token, Scanner::$RELATIONSHIPS)){
            return true;
        }
        return false;
    }

    /**
     * Mostra a expressão através do método de análise postfix
     * @return string String da expressão
     */
    public function showPostFix(){
        return trim($this->showPostFixNode( $this->treeNode ));
    }

    /**
     * Análise postfix
     * @param TreeNode $treeNode
     * @return string String da expressão
     */
    private function showPostFixNode( $treeNode )
    {
        $output = '';
        if ($treeNode != null) {
            $output = $this->showPostFixNode($treeNode->getLeft());
            $output .= $this->showPostFixNode($treeNode->getRight());
            $output .= "{$treeNode->toString()} ";
        }
        return $output;
    }

    /**
     * Mostra a expressão através do método de análise pretfix
     * @return string String da expressão
     */
    public function showPreFix(){
        return trim($this->showPreFixNode( $this->treeNode ));
    }

    /**
     * Análise prefix
     * @param TreeNode $treeNode
     * @return string String da expressão
     */
    private function showPreFixNode( $treeNode )
    {
        $output = '';
        while ($treeNode != null) {
            $output .= "{$treeNode->toString()} ";
            $output .= $this->showPreFixNode($treeNode->getLeft());
            $treeNode = $treeNode->getRight();
        }
        return $output;
    }

    /**
     * Mostra a expressão através do método de análise infix com parênteses
     * @return string String da expressão
     */
    public function showInFix(){
        return trim($this->showInFixNode( $this->treeNode ));
    }

    /**
     * Análise infix
     * @param TreeNode $treeNode
     * @return string String da expressão
     */
    private function showInFixNode( $treeNode )
    {
        $output = '';
        if ( $treeNode != null )
        {
            if ( $treeNode instanceof TreeNode ){
                $output .= "( ";
            }
            $output .= $this->showInFixNode( $treeNode->getLeft() );
            $output .=  "{$treeNode->toString()} ";
            $output .= $this->showInFixNode ( $treeNode->getRight() );
            if ( $treeNode instanceof TreeNode ){
                $output .=  ") ";
            }
        }
        return $output;
    }

    /**
     * Avalia a expressão e retorna o seu valor (resolve a expessão)
     * @param TreeNode|TreeLeaf $treeNode Arvore que será analizada
     * @return float|int|null Resultado da expressão
     */
    public function evaluate( $treeNode=null )
    {
        $result = null;
        if($treeNode==null){
            $result = $this->treeNode == null ? 0 : $this->evaluate( $this->treeNode ) ;
        }else{
            if ( $treeNode instanceof TreeLeaf )
                /**
                 * SE é uma folha, então so tem o valor numérico
                 */
                $result = $treeNode->getValue();
            else
            {
                /**
                 * Aqui é nó binário, então existe o lado esquerdo, direito e o operador
                 */
                $operator = $treeNode->getOperator();

                /**
                 * Chama recursivamente cada lado do nó (que podem ser outros nós)
                 */
                $valueLeft  = $this->evaluate( $treeNode->getLeft() );
                $valueRight = $this->evaluate( $treeNode->getRight() );

                /**
                 * Executa as operações
                 */
                switch ( $operator )
                {
                    case '+':  $result = $valueLeft + $valueRight;  break;
                    case '-':  $result = $valueLeft - $valueRight;  break;
                    case '*':  $result = $valueLeft * $valueRight;  break;
                    case '/':  $result = $valueLeft / $valueRight;  break;
                    case '^':  $result = $valueLeft ^ $valueRight;  break;
                }
            }
        }
        return $result;
    }
}