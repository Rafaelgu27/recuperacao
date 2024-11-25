<?php
// Configuração para MySQL
define('MYSQL_HOST', 'localhost');
define('MYSQL_DB', 'mysql_database');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'root');

// Conexão com MySQL
try {
    $mysql = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com MySQL: " . $e->getMessage());
}

// Função para executar consulta no MySQL
function executeMySQLQuery($query)
{
    global $mysql;
    try {
        if (trim($query) === '') {
            return ["erro" => "A consulta não pode estar vazia."];
        }
        
        $stmt = $mysql->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ["erro" => $e->getMessage()];
    }
}

// Verificar se o email existe
function checkEmailExists($email)
{
    $query = "SELECT * FROM usuarios WHERE email = :email";
    global $mysql;
    $stmt = $mysql->prepare($query);
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna o usuário se encontrado, senão retorna false
}

// Processamento de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mysqlResult = null;

    // Verificar se o email já existe
    if ($email) {
        $mysqlResult = checkEmailExists($email);

        if ($mysqlResult) {
            $message = "O email '$email' já está cadastrado!";
        } else {
            $message = "Este email não está cadastrado. Você pode registrar agora.";
        }
    }

    // Se o formulário de registro for enviado
    if (isset($_POST['register_email'])) {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email_to_register'] ?? '';
        if ($nome && $email) {
            // Inserir o novo usuário
            $stmt = $mysql->prepare("INSERT INTO usuarios (nome, email) VALUES (:nome, :email)");
            $stmt->execute(['nome' => $nome, 'email' => $email]);
            $message = "Email registrado com sucesso!";
        } else {
            $message = "Por favor, preencha todos os campos para registrar o email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar e Registrar Email</title>
</head>
<body>
    <h1>Verificar e Registrar Email</h1>
    
    <!-- Formulário de Verificação de Email -->
    <form method="POST">
        <h2>Verifique seu Email</h2>
        <input type="email" name="email" placeholder="Digite seu email" required>
        <button type="submit">Verificar</button>
    </form>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (isset($mysqlResult) && !$mysqlResult): ?>
        <!-- Se o email não existir, exibe o formulário para registrar -->
        <h2>Registrar Novo Email</h2>
        <form method="POST">
            <input type="text" name="nome" placeholder="Seu nome" required><br>
            <input type="email" name="email_to_register" placeholder="Email para registrar" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br>
            <button type="submit" name="register_email">Registrar</button>
        </form>
    <?php endif; ?>

</body>
</html>
