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
    $query_usuario = "SELECT id, nome_usuario, endereco, telefone, CPF, email_usuario FROM usuarios WHERE id=:id LIMIT 1";
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
    <title>Celke - Editar </title>
</head>

<body>
    <a href="index.php">Listar</a><br>
    <a href="upload.php">Cadastrar</a><br>

    <h2>Editar Usuário</h2>

    <?php
    echo "<a href='visualizar.php?id=$id'>Visualizar</a><br><br>";
    // Receber os dados do formulario
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Verificar se o usuario clicou no botao
    if (!empty($dados['EditarUsuario'])) {
        //var_dump($dados);
        // Criar a QUERY editar no banco de dados
        $query_edit_usuario = "UPDATE usuarios SET nome_usuario=:nome_usuario, endereco=:endereco, telefone=:telefone, CPF=:CPF, email_usuario=:email_usuario, modified = NOW() WHERE id=:id";
        $edit_usuario = $conn->prepare($query_edit_usuario);
        $edit_usuario->bindParam(':nome_usuario', $dados['nome_usuario'], PDO::PARAM_STR);
        $edit_usuario->bindParam(':endereco', $dados['endereco'], PDO::PARAM_STR);
        $edit_usuario->bindParam(':telefone', $dados['telefone'], PDO::PARAM_STR);
        $edit_usuario->bindParam(':CPF', $dados['CPF'], PDO::PARAM_STR);
        $edit_usuario->bindParam(':email_usuario', $dados['email_usuario'], PDO::PARAM_STR);
        $edit_usuario->bindParam(':id', $id, PDO::PARAM_INT);

        // Verificar se editou com sucesso
        if ($edit_usuario->execute()) {
            $_SESSION['msg'] = "<p style='color: green;'>Usuário editado com sucesso!</p>";
            header("Location: visualizar.php?id=$id");
        } else {
            echo "<p style='color: #f00;'>Erro: Usuário não editado com sucesso!</p>";
        }
    }
    ?>

    <form name="edit_usuario" method="POST" action="">
        <?php
        $nome_usuario = "";
        if (isset($row_usuario['nome_usuario'])) {
            $nome_usuario = $row_usuario['nome_usuario'];
        }
        ?>
        <label>Nome: </label>
        <input type="text" name="nome_usuario" id="nome_usuario" placeholder="Nome completo" value="<?php echo $nome_usuario; ?>" autofocus required><br><br>

        <?php
        $endereco = "";
        if (isset($row_usuario['endereco'])) {
            $nome_usuario = $row_usuario['endereco'];
        }
        ?>
        <label>Endereço: </label>
        <input type="text" name="endereco" id="endereco" placeholder="Endereço completo" value="<?php echo $nome_usuario; ?>" autofocus required><br><br>

        <?php
        $telefone = "";
        if (isset($row_usuario['telefone'])) {
            $nome_usuario = $row_usuario['telefone'];
        }
        ?>
        <label>Telefone: </label>
        <input type="text" name="telefone" id="telefone" placeholder="Numero completo" value="<?php echo $nome_usuario; ?>" autofocus required><br><br>

        <?php
        $CPF = "";
        if (isset($row_usuario['CPF'])) {
            $nome_usuario = $row_usuario['CPF'];
        }
        ?>
        <label>CPF: </label>
        <input type="text" name="CPF" id="CPF" placeholder="Numero completo" value="<?php echo $nome_usuario; ?>" autofocus required><br><br>

        <?php
        $email_usuario = "";
        if (isset($row_usuario['email_usuario'])) {
            $email_usuario = $row_usuario['email_usuario'];
        }
        ?>
        <label>E-mail: </label>
        <input type="email" name="email_usuario" id="email_usuario" placeholder="O melhor e-mail" value="<?php echo $email_usuario; ?>" required><br><br>

        <input type="submit" value="Salvar" name="EditarUsuario">

    </form>

</body>

</html>