<?php
// Inicia a sessão para guardar o estado do jogo entre os reloads da página
session_start();

// 1. Banco de palavras simples
$banco_palavras = ["PHP", "HTML", "FORCA", "LOGICA", "PROGRAMACAO", "WEBSITE", "VARIAVEL"];

// 2. Função para iniciar ou reiniciar o jogo
function iniciarJogo($banco) {
    $_SESSION['palavra_secreta'] = strtoupper($banco[array_rand($banco)]); // Escolhe uma palavra aleatória em maiúsculo
    $_SESSION['tentativas'] = []; // Guarda as letras que o usuário já digitou
    $_SESSION['erros'] = 0; // Contador de erros
    $_SESSION['max_erros'] = 6; // Limite de erros antes de perder
}

// Se o jogo ainda não foi iniciado ou o usuário clicou em "Reiniciar"
if (!isset($_SESSION['palavra_secreta']) || isset($_POST['reiniciar'])) {
    iniciarJogo($banco_palavras);
}

$mensagem = "";

// 3. Processa a letra enviada pelo formulário
if (isset($_POST['letra'])) {
    $letra = strtoupper(trim($_POST['letra']));

    // Validação básica do lado do servidor
    if (strlen($letra) === 1 && ctype_alpha($letra)) {
        
        // Verifica se a letra já foi jogada antes
        if (in_array($letra, $_SESSION['tentativas'])) {
            $mensagem = "Você já tentou a letra '$letra'!";
        } else {
            // Adiciona a letra ao histórico de tentativas
            $_SESSION['tentativas'][] = $letra;

            // Se a letra NÃO existir na palavra secreta, conta um erro
            if (strpos($_SESSION['palavra_secreta'], $letra) === false) {
                $_SESSION['erros']++;
            }
        }
    } else {
        $mensagem = "Por favor, digite uma letra válida.";
    }
}

// 4. Monta a palavra mascarada (ex: _ _ A _ O) e verifica se ganhou
$palavra_mascarada = "";
$ganhou = true;

// Transforma a palavra secreta em um array de letras
$letras_da_palavra = str_split($_SESSION['palavra_secreta']);

foreach ($letras_da_palavra as $letra_correta) {
    // Se a letra já foi tentada, mostra ela, se não, mostra o traço "_"
    if (in_array($letra_correta, $_SESSION['tentativas'])) {
        $palavra_mascarada .= $letra_correta . " ";
    } else {
        $palavra_mascarada .= "_ ";
        $ganhou = false; // Se faltar pelo menos uma letra, ele ainda não ganhou
    }
}

// 5. Verifica se o jogador perdeu (atingiu o limite de erros)
$perdeu = ($_SESSION['erros'] >= $_SESSION['max_erros']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Forca em PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .palavra {
            font-size: 2rem;
            letter-spacing: 5px;
            margin: 20px 0;
            font-weight: bold;
            color: #333;
        }
        .status {
            margin: 15px 0;
            color: #666;
        }
        .letras-tentadas {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 20px;
        }
        .alerta {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .resultado {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
        }
        .ganhou { color: #2ecc71; }
        .perdeu { color: #e74c3c; }
        
        input[type="text"] {
            width: 40px;
            padding: 10px;
            font-size: 1.2rem;
            text-align: center;
            text-transform: uppercase;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #2980b9; }
        .btn-reiniciar { background-color: #95a5a6; margin-top: 15px;}
        .btn-reiniciar:hover { background-color: #7f8c8d; }
    </style>
</head>
<body>

<div class="container">
    <h1>Jogo da Forca</h1>

    <div class="palavra"><?php echo $palavra_mascarada; ?></div>

    <div class="status">
        Erros: <?php echo $_SESSION['erros']; ?> de <?php echo $_SESSION['max_erros']; ?>
    </div>

    <div class="letras-tentadas">
        Letras tentadas: <?php echo implode(", ", $_SESSION['tentativas']); ?>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="alerta"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <?php if ($ganhou): ?>
        <div class="resultado ganhou"> Parabéns! Você venceu!</div>
    <?php elseif ($perdeu): ?>
        <div class="resultado perdeu"> Fim de Jogo! A palavra era: <?php echo $_SESSION['palavra_secreta']; ?></div>
    <?php else: ?>
        <form method="POST" action="">
            <label for="letra">Digite uma letra: </label>
            <input type="text" id="letra" name="letra" maxlength="1" required autofocus autocomplete="off">
            <button type="submit">Jogar</button>
        </form>
    <?php endif; ?>

    <form method="POST" action="">
        <button type="submit" name="reiniciar" class="btn-reiniciar">Reiniciar Jogo</button>
    </form>
</div>

</body>
</html>