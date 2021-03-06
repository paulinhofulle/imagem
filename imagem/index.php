<?php

include('conexao.php');

if (isset($_GET['deletar'])) {

    $id = intval($_GET['deletar']);
    $sql_query = $mysqli->query("SELECT * FROM arquivos WHERE id = '$id'") or die($mysqli->error);
    $arquivo = $sql_query->fetch_assoc();

    if(unlink($arquivo['pasta'])) {
        $deu_certo = $sql_query = $mysqli->query("DELETE FROM arquivos WHERE id = '$id'") or die($mysqli->error);
        if($deu_certo){
            echo "<p>Arquivo excluído com sucesso!</p>";
        }
    }
}

if (isset($_FILES['arquivo'])) {
    $arquivo = $_FILES['arquivo'];

    if($arquivo['error']){
        die('Falha ao enviar arquivo');
    }

    $maxMb = 3145728;
    
    if($arquivo['size'] > $maxMb) {
        die('Arquivo muito grande, no máximo 3MB');
    }

    $pasta = 'arquivos/';
    $nomeDoArquivo = $arquivo['name'];
    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

    if($extensao != "jpg" && $extensao != "png"){
        die("Tipo de arquivo não aceito!");
    }

    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;

    $deu_certo = move_uploaded_file($arquivo["tmp_name"], $path);
    if($deu_certo){
        $mysqli->query("INSERT INTO arquivos (nome, pasta) VALUES('$nomeDoArquivo', '$path')") or die($mysqli->error);
        //echo "<p>Arquivo enviado com sucesso! Para acessá-lo <a target=\"_blank\" href=\"arquivos/$novoNomeDoArquivo.$extensao\">clique aqui</a></p>";
    }
    else {
        echo "<p>Falha ao enviar arquivo! </p>";
    }
}

$sql_query = $mysqli->query("SELECT * FROM arquivos") or die($mysqli->error);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imagem</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data" action="">
        <p>
            <label for="">Selecione o Arquivo</label>
            <input name="arquivo" type="file">
        </p>
        <button type="submit">Enviar Arquivo</button>
    </form>

    <table border="1">
        <thead>
            <th>Preview</th>
            <th>Arquivo</th>
        </thead>

        <tbody>
            <?php
                while($arquivo = $sql_query->fetch_assoc()) {
            ?>
            <tr>
                <td><img height="300" src="<?php echo $arquivo['pasta'];?>" alt=""></td>
                <td><a target="_blank" href="<?php echo $arquivo['pasta'];?>"><?php echo $arquivo['nome']; ?></a></td>
                <th><a href="index.php?deletar=<?php echo $arquivo['id'];?>">Deletar</a></th>
            </tr>
            <?php 
                }
            ?>
        </tbody>
    </table>
</body>
</html>