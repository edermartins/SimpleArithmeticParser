<?php
/**
 * The scanner.
 *
 * This scans over an input stream.
 */
class Scanner extends StringInputStream
{

    /**
     * Sequência válida para hexadecimal
     */
    const CHARS_HEX = 'abcdefABCDEF01234567890';
    /**
     * Sequência válida para decimais
     */
    const CHARS_NUM = '01234567890';
    /**
     * Sequência válida para alfanuméricos
     */
    const CHARS_ALNUM = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
    /**
     * Sequência válida para texto
     */
    const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    /**
     * Sequência válida para operadores
     */
    const CHARS_TOKEN = '+-*/%^()=<>';

    /**
     * @var array Array de operações
     */
    static $OPERATIONS = array('+', '-', '*', '/', '%');
    /**
     * @var array Array with valid relacionamentos
     */
    static $RELATIONSHIPS = array('>', '<', '>=', '<=', '==');

    /**
     * @var string Mensagem de erro
     */
    private $message = '';

    /**
     * @return string Message with the possibles errors
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * Retorna uma sequencia de hexadecimal, para quando encontra um caracter que não pertence a este tipo de sequencia
     * @return mixed Retorna o próximo conjunto de caracteres ou false se não encontra a sequencia deste tipo
     */
    public function getHex()
    {
        return $this->charsWhile(static::CHARS_HEX);
    }

    /**
     * Retorna uma sequencia de texto, para quando encontra um caracter que não pertence a este tipo de sequencia
     * @return mixed Retorna o próximo conjunto de caracteres ou false se não encontra a sequencia deste tipo
     */
    public function getAsciiAlpha()
    {
        return $this->charsWhile(static::CHARS_ALPHA);
    }

    /**
     * Retorna uma sequencia de operadores, para quando encontra um caracter que não pertence a este tipo de sequencia
     * @return mixed Retorna o próximo conjunto de caracteres ou false se não encontra a sequencia deste tipo
     */
    public function getToken()
    {
        $token = $this->current();
        $this->next();
        /**
         * Verifica se o toke é combinado: '==', '++', etc.
         * TODO: ainda não foi implementado a operação com os operadores combinados
         */
        if($this->checkChar($token, Scanner::CHARS_TOKEN)){
            $tokenComposite = $token . $this->current();
            if($tokenComposite == '++'
                || $tokenComposite == '--'
                || $tokenComposite == '=='
                || $tokenComposite == '>='
                || $tokenComposite == '<='){
                    $token = $tokenComposite;
                    $this->next();
            }
        }else{
            $this->message = 'Operador inválido, deve ser um destes: '.Scanner::CHARS_TOKEN;
            $token = null;
        }
        return $token;
    }

    /**
     * Retorna uma sequencia de alfanumérico, para quando encontra um caracter que não pertence a este tipo de sequencia
     * @return mixed Retorna o próximo conjunto de caracteres ou false se não encontra a sequencia deste tipo
     */
    public function getAsciiAlphaNum()
    {
        return $this->charsWhile(static::CHARS_ALNUM);
    }

    /**
     * Retorna uma sequencia de decimais, para quando encontra um caracter que não pertence a este tipo de sequencia
     * @return mixed Retorna o próximo conjunto de caracteres ou false se não encontra a sequencia deste tipo
     */
    public function getNumeric()
    {
        return $this->charsWhile(static::CHARS_NUM);
    }


    /**
     * Verica se a quantidade de parenteses está correto e retorna o seu conteúdo, sem os parênteses.
     * A expressão pode conter 3 tipos de parênteses:
     * <br>Simples: 10*(<b>3+4</b>) : retornará '3+4'
     * <br>Aninhados: 10*(<b>32/(8+3)</b>) : retornará '32/(8+3)'
     * <br>Grupos: 10*(<b>32+4</b>)/(8+3): retornará '32+4'
     * @return array Array('expression' => 'expresão que está dentro do parênteses', 'ini' => posição inicia, 'size' => posição final)
     * <br><b>expression</b> será nula se não existir parênteses ou não tiver conteúdo '()'
     */
    public function getParanthesesExpression(){
        $data = $this->remainingChars(false);
        $parenthesesIni = -1;
        $parenthesesEnd = -1;
        $parentheses = 0;
        for($i=0; $i < strlen($data); $i++){
            if(substr($data,$i, 1) == '('){
                if($parenthesesIni == -1){
                    $parenthesesIni = $i;
                }
                $parentheses++;
            }elseif(substr($data,$i, 1) == ')'){
                $parenthesesEnd = $i;
                $parentheses--;
                /*
                 * Se $parentheses é igual a zero então achou o final do parênteses incial, então para o processamento
                 */
                if($parentheses == 0){
                    break;
                }
            }
        }
        if($parentheses != 0){
            $this->message = "Falta ".($parentheses < 0 ? "a abertura '('" : "o fechamento ')'");
            return null;
        }
        if($parenthesesIni == -1 || $parenthesesEnd == -1){
            $parenthesesIni = 0;
            /**
             * Subtrai 1 porque a posição inicial é zero, senão quando for executar o strlen, pega um caracter a mais
             */
            $parenthesesEnd = strlen($data)-1;
            $data = null;
        }
        /**
         * Move o ponteiro para o final da expressão, como a posição atual é o '(', então move para o final da expressão
         * + 1, que é o ')', assim o ponteiro aponta para o próximo caracter após o ')'
         */
        $this->consume($parenthesesEnd+1);
        return array(
            'expression' => substr($data, $parenthesesIni+1, $parenthesesEnd-$parenthesesIni-1),
            'ini' => $parenthesesIni+1,
            'size' => $parenthesesEnd-$parenthesesIni-1) ;
    }
}
