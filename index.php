<?php
define('MYSQL_HOST', 'localhost');
define('MYSQL_DB', 'mysql_database');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'root');

try {
    $mysql = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com MySQL: " . $e->getMessage());
}

function buscarProdutos($nome)
{
    global $mysql;
    $query = "SELECT * FROM produtos WHERE nome LIKE :nome";
    $stmt = $mysql->prepare($query);
    $stmt->execute(['nome' => '%' . $nome . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cadastrar_produto'])) {
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $preco = $_POST['preco'] ?? '';

        if ($nome && $preco) {
            $stmt = $mysql->prepare("INSERT INTO produtos (nome, descricao, preco) VALUES (:nome, :descricao, :preco)");
            $stmt->execute(['nome' => $nome, 'descricao' => $descricao, 'preco' => $preco]);
            $mensagem = "Produto cadastrado com sucesso!";
        } else {
            $mensagem = "Por favor, preencha todos os campos!";
        }
    }

    if (isset($_POST['buscar_produto'])) {
        $nomeBusca = $_POST['nome_busca'] ?? '';
        $produtos = buscarProdutos($nomeBusca);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro e Busca de Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .product-list {
            margin-top: 20px;
        }
        .product-item {
            background-color: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Cadastro e Busca de Produtos</h1>

    <div class="form-container">
        <h2>Cadastrar Produto</h2>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome do Produto" required>
            <textarea name="descricao" placeholder="Descrição do Produto"></textarea>
            <input type="number" step="0.01" name="preco" placeholder="Preço do Produto" required>
            <button type="submit" name="cadastrar_produto">Cadastrar Produto</button>
        </form>
    </div>

    <?php if (isset($mensagem)): ?>
        <p><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <div class="form-container">
        <h2>Buscar Produto</h2>
        <form method="POST">
            <input type="text" name="nome_busca" placeholder="Digite o nome do produto para buscar" required>
            <button type="submit" name="buscar_produto">Buscar Produto</button>
        </form>
    </div>

    <?php if (isset($produtos) && count($produtos) > 0): ?>
        <div class="product-list">
            <h2>Produtos Encontrados:</h2>
            <?php foreach ($produtos as $produto): ?>
                <div class="product-item">
                    <strong><?php echo htmlspecialchars($produto['nome']); ?></strong><br>
                    <?php echo htmlspecialchars($produto['descricao']); ?><br>
                    Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($produtos)): ?>
        <p>Nenhum produto encontrado com o nome "<?php echo htmlspecialchars($nomeBusca); ?>"</p>
    <?php endif; ?>
</body>
</html>
