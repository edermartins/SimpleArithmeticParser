# PHP Parser para expressões aritméticas

Este é um sistema simples para realizar parse de operações de aritmética básica.
Utiliza "Composite Design Pattern" nas classes: AbstractTree, TreeNode e TreeLeaf.

Não é um sistema de fácil compreenção, pois utiliza recursividade extensivamente.



## Composite Design Pattern

É um padrão para utilizar recursividade em diagramas de classes.
Veja [Composite](https://pt.wikipedia.org/wiki/Composite) em português
ou este outro aqui em inglês [Composite Design Pattern](https://sourcemaking.com/design_patterns/composite).

```
                 +--------------+
                 | AbstractTree |<---------------+
                 +--------------+                |
                 |              |                |
                 +--------------+                |
                         ^                       |
                         |                       |
       +-----------------+-------------+         |
       |                               |         |
+--------------+              +--------------+   |
|   TreeLeaf   |              |   TreeNode   |<>-+
+--------------+              +--------------+
|              |              |              |
+--------------+              +--------------+
```

## Factory Pattern

Utiliza uma factory super simples. O Factory Pattern simplica o usdo de classes,
abstraindo a complexidade do uso, um conjunto de interfaces que recebem ou não
parâmetros.

```
+-----------------+             +----------+
| TreeNodeFactory |---+---------| TreeNode |
+-----------------+   |         +----------+
|                 |   |         |          |
+-----------------+   |         +----------+
                      |
                      |         +----------+
                      +---------| TreeLeaf |
                                +----------+
                                |          |
                                +----------+

```


Leia um pouco sobre [Factory](https://pt.wikipedia.org/wiki/Abstract_Factory) e [Factory Method] (https://pt.wikipedia.org/wiki/Factory_Method).


## Funcionalidades para ler a expressão

A função scanner oferece recursos para ler números, operadores e é
capaz de identificar se a string já terminou

```
+--------------+              +--------------+
|   Scanner    |------------->| StringInput  |
+--------------+              +--------------+
|              |              |              |
+--------------+              +--------------+
```

## Análize da expressão

Analisa cada caracter, para identificar se a sequencia é válida ou não.
Segue montando a árvore com os comandos e oferece 3 formas de visão:
- Prefix
- Postfix
- Infix

```
+----------------+              +-----------------+
| ExpressionTree |--------------| TreeNodeFactory |
+----------------+              +-----------------+
|                |              |                 |
+----------------+              +-----------------+
```

### Exemplo de uso:

```
$expressionString = '2+8*(10+(4*3))';

$calc = new ExpressionTree($expressionString);

echo "<br>Input as prefix expression: '{$calc->showPreFix()}'";

echo "<br>Input as postfix expression: '{$calc->showPostFix()}'";

echo "<br>Input as infix expression: '{$calc->showInFix()}'";

echo "<br><br>Value:  '{$calc->evaluate()}'";
```

Para entender um pouco mais sobre o assunto, acesse [Analisador sintático LR](https://pt.wikipedia.org/wiki/Analisador_sint%C3%A1tico_LR).

### index.html

Ofere uma página, bem simples onde você pode digitar uma expressão válida e,
ao clicar no botão, submete para a 'parser.php' que faz a intância da classe
'ExpressionTree' que pode exibir a expressão nas três formas sitadas acima e
também avalia a expressão, mostrando o seu resultado.

# TODO

- Considerar os operadores ++ e -- e os operadores de comparação ==, <, >, >= e <.
- Algumas mensagens de erro parecem não fazerem sentido, analisar o código onde ocorrem

