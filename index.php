<?php
require __DIR__."/vendor/autoload.php";

if(isset($_FILES['arquivo'])){
    try{
        $leitorArquivo = new \Leandroferreirama\PagamentoCnab240Retorno\Aplicacao\LeitorRetorno();
        $array = $leitorArquivo->recepcionarArquivo($_FILES['arquivo']);

        if(! is_null($array)){
            foreach($array as $lote){
                echo "<hr>LOTE: <br>Codigo banco: {$lote['codigo_banco']}<br>agencia: {$lote['agencia']}<br>Conta: {$lote['conta']}<br>DV: {$lote['contaDv']}<br><hr>CONTEUDO:<bR>";
                foreach($lote['detalhes'] as $conteudo){
                    echo "<hr>Segmento: {$conteudo['segmento']} | Data: {$conteudo['data_pagamento']} | Valor: {$conteudo['valor_pagamento']} | Seu nº: {$conteudo['seu_numero']} | Ocorrencia: {$conteudo['ocorrencia']} | Resultado: {$conteudo['resultado']}<hr>";
                }
            }
        }
    } catch(Exception $exception){
        echo $exception->getMessage();
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload de arquivo</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="arquivo[]"multiple>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>