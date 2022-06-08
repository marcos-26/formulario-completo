<?php

session_start();
ob_start();

// Incluir a conexao com BD
include_once "conexao.php";

// Receber o ID do registro
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Acessa o IF quando nao existe o ID
if (empty($id)) {
    $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário não encontrado!</p>";
    header("Location: index.php");
} else {
    // QUERY para recuperar os dados do registro
    $query_usuario = "SELECT id, foto_usuario FROM usuarios WHERE id=:id LIMIT 1";
    $result_usuario = $conn->prepare($query_usuario);
    $result_usuario->bindParam(':id', $id, PDO::PARAM_INT);
    $result_usuario->execute();

    // Verificar se encontrou o registro no banco de dados
    if (($result_usuario) and ($result_usuario->rowCount() != 0)) {
        $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);
        //var_dump($row_usuario);
    } else {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário não encontrado!</p>";
        header("Location: index.php");
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Celke - Editar foto</title>
</head>

<body>
    <a href="index.php">Listar</a><br>
    <a href="upload.php">Cadastrar</a><br>    

    <h2>Editar Foto</h2>

    <?php
    echo "<a href='visualizar.php?id=$id'>Visualizar</a><br><br>";
    // Receber os dados do formulario
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);    

    // Verificar se o usuario clicou no botao
    if(!empty($dados['EditarFoto'])){
        // Receber a foto
        $arquivo = $_FILES['foto_usuario'];
        //var_dump($arquivo);
        // Verificar se o usuario esta enviando a foto
        if((isset($arquivo['name'])) and (!empty($arquivo['name']))){
            // Criar a QUERY editar no banco de dados
            $query_edit_usuario = "UPDATE usuarios SET foto_usuario=:foto_usuario, modified = NOW() WHERE id=:id";
            $edit_usuario = $conn->prepare($query_edit_usuario);
            $edit_usuario->bindParam(':foto_usuario', $arquivo['name'], PDO::PARAM_STR);
            $edit_usuario->bindParam(':id', $id, PDO::PARAM_INT);

            // Verificar se editou com sucesso
            if($edit_usuario->execute()){
                // Diretorio onde o arquivo sera salvo
                $diretorio = "imagens/$id/";

                // Verificar se o diretorio existe
                if((!file_exists($diretorio)) and (!is_dir($diretorio))){
                    // Criar o diretorio
                    mkdir($diretorio, 0755);
                }

                // Upload do arquivo
                $nome_arquivo = $arquivo['name'];
                if(move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo)){
                    // Verificar se existe o nome da imagem salva no banco de dados e o nome da imagem salva no banco de dados he diferente do nome da imagem que o usuario esta enviando
                    if(((!empty($row_usuario['foto_usuario'])) or ($row_usuario['foto_usuario'] != null)) and ($row_usuario['foto_usuario'] != $arquivo['name'])){
                        $endereco_imagem = "imagens/$id/". $row_usuario['foto_usuario'];
                        if(file_exists($endereco_imagem)){
                            unlink($endereco_imagem);
                        }
                    }

                    $_SESSION['msg'] = "<p style='color: green;'>Foto editada com sucesso!</p>";
                    header("Location: visualizar.php?id=$id");
                }else{
                    echo "<p style='color: #f00;'>Erro: Usuário não editado com sucesso!</p>";
                }
            }else{
                echo "<p style='color: #f00;'>Erro: Usuário não editado com sucesso!</p>";
            }
        }else{
            echo "<p style='color: #f00;'>Erro: Necessário selecionar uma imagem!</p>";
        }
    }
    ?>

    <form name="edit_foto" method="POST" action="" enctype="multipart/form-data">
        <label>Foto: </label>
        <input type="file" name="foto_usuario" id="foto_usuario"><br><br>

        <input type="submit" value="Salvar" name="EditarFoto">

    </form>


</body>

</html>