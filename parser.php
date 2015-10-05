<!DOCTYPE html>
<html lang="en">
<head>
    <title>Parse Aritmético em PHP </title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<div class="container">
    <h1>Result</h1>
    <pre>
        <?php
        include 'autoloader.php';

        $expressionString = filter_input(INPUT_POST, 'expression', FILTER_SANITIZE_SPECIAL_CHARS);

        if($expressionString){

            echo "<br>Processando a expressão: '$expressionString'<br>";
            try {

                $calc = new ExpressionTree($expressionString);

                echo "<br>Análise prefix: '{$calc->showPreFix()}'";

                echo "<br>Análise postfix: '{$calc->showPostFix()}'";

                echo "<br>Análise infix: '{$calc->showInFix()}'";

                echo "<br><br>Resultado:  '{$calc->evaluate()}'";
            }catch (Exception $e){
                echo "<br>{$e->getMessage()}";
            }
        }else{
            echo "Expressão inválida";
        }
        ?>
    </pre>
    <br>
    <button class="btn btn-primary" onclick="voltar()">Voltar</button>
</div>
<script>
    function voltar() {
        window.history.back();
    }
</script>
</body>
<footer>
    <br>
    <div class="panel panel-default">
        <div class="panel-footer">
            Feito por: <a href="http://www.linkedin.com/in/edermartins">Eder Jani Martins</a> or <a href="https://github.com/edermartins/SimpleArithmeticParser">github project</a>
        </div>
    </div>
</footer>
</html>
