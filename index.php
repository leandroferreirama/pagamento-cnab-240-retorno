<?php
if($_FILES && !empty($_FILES['arquivo']['name'])){
    $fileUpload = $_FILES["arquivo"];

    var_dump($fileUpload);
    echo '<hr>';
    echo $fileUpload['type'];

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
        <input type="file" name="arquivo">
        <button type="submit">Enviar</button>
    </form>
</body>
</html>