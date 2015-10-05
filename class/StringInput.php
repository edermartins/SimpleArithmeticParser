<?php

/**
 * @author Eder Jani Martins
 * Class StringInputStream Oferece um conjunto de funcionalidade para ler caracteres em uma string
 */
class StringInputStream
{

    /**
     * The string data we're parsing.
     */
    private $data;

    /**
     * The current integer byte position we are in $data
     */
    private $char;

    /**
     * Length of $data; when $char === $data, we are at the end-of-file.
     */
    private $EOF;

    /**
     * TODO: Parse dos erros não foi implementado ainda
     */
    public $errors = array();

    /**
     * Construtor recebe uma expressão aritmética em formato string
     * @param $data Expressão que será analisada
     */
    public function __construct($data)
    {
        /**
         * Remove todos os espaços
         */
        $data = str_replace(' ', '', $data);

        $this->data = $data;
        $this->char = 0;
        $this->EOF = strlen($data);
    }

    /**
     * Retorna o caracter atual
     *
     * @return string Caracter atual
     */
    protected function current()
    {
        if ($this->valid()) {
            return $this->data[$this->char];
        }
        return false;
    }

    /**
     * Avança o ponteiro em uma posição
     * @return bool|string Se a análise não chegou ao fim retorna um caracter, se chegou retorna <b>false</b>
     */
    protected function next()
    {
        $this->char++;
        if ($this->valid()) {
            return $this->current();
        }
        return false;
    }

    /**
     * Reinicia o ponteiro para 0
     */
    protected function rewind()
    {
        $this->char = 0;
    }

    /**
     * Verifica se o ponteiro ainda é válido, ou seja, se não chegou ao fim
     * @return bool Verifica se o ponteiro está em uma posição válida, antes do fim.
     */
    public function valid()
    {
        if ($this->char < $this->EOF) {
            return true;
        }
        return false;
    }

    /**
     * Recupera todos os caracteres da posicação atual até o final.
     * <br>Se <b>$move</b> for igual a <b>true</b> move o ponteiro, se for <b>false</b>, retorna os carateres
     * restantes, mas não moce o ponteiro
     *
     * @param bool|false $move
     *              <br>se <b>true</b> retorna a string e move o ponteiro para o fim,
     *              <br>se <b>false</b> retorna o texto mas não move o ponteiro
     * @return string Todos os caracteres da posição atual até o final da string, se a posição for inválida, retorna <b>null</b>
     */
    protected function remainingChars($move=true)
    {
        if ($this->char < $this->EOF) {
            $data = substr($this->data, $this->char);
            if ($move) {
                $this->char = $this->EOF;
            }
            return $data;
        }
        return '';
    }

    /**
     * Le caracteres de um mesmo tipo que estejam em sequência, baseado uma sequência que será utilzada como máscara.
     * <br>Ex.: <b>$bytes</b>='0123456789' e a expressão de entrada for '34+8', esta função irá retornar a sequência
     * '34', pois o caractere '+' não pertence à másca <b>$bytes</b>='0123456789'.
     * @param string $bytes Sequência de caracteres que será utilizado como máscara
     * @param int $max Opcionalmente pode indicar a quantidade máxima de caracteres a serem lidos
     * @return mixed Retorna a sequencia de caracteres ou <b>false</b> se a posição for inválida
     */
    protected function charsWhile($bytes, $max = null)
    {
        if ($this->char >= $this->EOF) {
            return false;
        }

        if ($max === 0 || $max) {
            $len = strspn($this->data, $bytes, $this->char, $max);
        } else {
            $len = strspn($this->data, $bytes, $this->char);
        }
        $string = (string)substr($this->data, $this->char, $len);
        $this->char += $len;

        return $string;
    }

    /**
     * Não faz nada além de mover o ponteiro para trás.
     * @param int $howMany Quantidade de posições que serão movidas para tras.
     */
    public function unconsume($howMany = 1)
    {
        if (($this->char - $howMany) >= 0) {
            $this->char = $this->char - $howMany;
        }else{
            $this->char = 0;
        }
    }

    /**
     * Não faz nada além de mover o ponteiro para frente.
     * @param int $howMany Quantidade de posições que serão movidas para frente.
     */
    public function consume($howMany = 1)
    {
        if (($this->char + $howMany) <= $this->EOF) {
            $this->char = $this->char + $howMany;
        }else{
            $this->char = $this->EOF;
        }
    }

    /**
     * Retorna o próximo caracter sem mover o ponteiro
     * @return mixed Retorna o próximo caracter ou <b>false</b> se a posição atual não for válida
     */
    protected function peek()
    {
        if (($this->char) <= $this->EOF) {
            return $this->data[$this->char];
        }
        return false;
    }

    /**
     * @return string Retorna a posição atual do ponteiro (lembrando que a primeira poisição é zero)
     */
    public function position()
    {
        return $this->char;
    }

    /**
     * Verifica se um caracter pertence a uma sequência. Ex.: char = 'A', bytes = 'ABCDEFGHIJKLMNOPQRSTUVXZ', busca 'A'
     * na sequencia <b>bytes</b>
     * @param $char Caracter a ser verificado
     * @param $bytes Sequencia de caracteres que servirão de base apra a pesquisa
     * @return mixed Retorna a posição do caracter ou <b>false</b> se não achar
     */
    protected function checkChar($char, $bytes){
        $len = strspn($char, $bytes);
        return $len;
    }

    /**
     * Mostra a expressão original
     * @return string String contendo a expressão passada ao construtor
     */
    public function data(){
        return $this->data;
    }
}