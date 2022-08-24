# Leitor Arquivo de Retorno de Pagamento
Leitor de arquivo de retorno no padrão CNAB240, o pacote retorna um array contendo os dados do(s) arquivos formatados
abstraindo a leitura do arquivo.

## Bancos Homologados
1. Bradesco
2. Itaú

## Segmentos Suportados
1. A (transferências mesmo banco, TED e PIX)
2. J (Boletos de Cobrança)

## Como usar
### HTML

O componente aceita tanto a leitura de um arquivo, quanto um array de arquivos.

```
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
```

### PHP
Para este exemplo eu apenas mando imprimir as opções
```
try{
    $leitorArquivo = new \Leandroferreirama\PagamentoCnab240Retorno\Aplicacao\LeitorRetorno();
    $listaLotes = $leitorArquivo->recepcionarArquivo($_FILES['arquivo']);

    if(! is_null($listaLotes)){
        foreach($listaLotes as $lote){
            ### INCLUIR O TRATAMENTO DE IDENTIFICAÇÃO DA CONTA
            echo "<hr>LOTE: <br>Codigo banco: {$lote['codigo_banco']}<br>agencia: {$lote['agencia']}<br>Conta: {$lote['conta']}<br>DV: {$lote['contaDv']}<br><hr>CONTEUDO:<bR>";
            foreach($lote['detalhes'] as $conteudo){
                ### INCLUIR O TRATAMENTO DOS RETORNOS DOS ITENS
                echo "<hr>Segmento: {$conteudo['segmento']} | Data: {$conteudo['data_pagamento']} | Valor: {$conteudo['valor_pagamento']} | Seu nº: {$conteudo['seu_numero']} | Ocorrencia: {$conteudo['ocorrencia']} | Resultado: {$conteudo['resultado']}<hr>";
            }
        }
    }
} catch(Exception $exception){
    echo $exception->getMessage();
}
```