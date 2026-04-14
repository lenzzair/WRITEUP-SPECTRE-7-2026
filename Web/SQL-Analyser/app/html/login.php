<?php
session_start();

$mysqli = new mysqli("127.0.0.1", "sqli_user", "sqli_pass", "sqli_lab", 3306);

if ($mysqli->connect_error) {
    die('Erreur connexion: ' . $mysqli->connect_error);
}

if (isset($_POST['username'], $_POST['password'])) {

  $message = '';
  $username = $_POST['username'];
  $password = $_POST['password'];
  $result = false;
  $sql_error = false;

  $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
  try{

    $result = $mysqli->query($sql);

  } catch (mysqli_sql_exception $e){

    $sql_error = $e->getMessage();
  }




  if ($result && $result->num_rows > 0) {
    $message = 'Connexion validée !';

  }else {
    $message = 'Identifiant invalide !';
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Spectre-7 | SQL Analysis</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/css_template.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<header class="header d-flex justify-content-between align-items-center px-4 py-3 border-bottom border-secondary">
    <div class="d-flex align-items-center">
        <!-- <img src="logo.png" class="spectre-logo me-3"> -->
        <h1 class="h5 mb-0">Spectre-7 · SQL Analyzer</h1>
    </div>
    <span class="text-muted small">auth_module</span>
</header>

<main class="container py-5">

    <div class="challenge-box">

        <h2 class="challenge-title">Authentication Analysis</h2>


        <div class="alert <?= ($message === 'Connexion validée !') ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>

        <hr class="border-secondary">

        <h5 class="text-white">Executed SQL Query</h5>
        <pre class="bg-black text-info p-3 rounded small">
<?= htmlspecialchars($sql) ?>
        </pre>

  <h5 class="text-white mt-4">Database Response</h5>

<?php if ($sql_error): ?>
    <pre class="bg-black text-danger p-3 rounded small">
<?= htmlspecialchars($sql_error) ?>
    </pre>

<?php elseif ($result): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <pre class="bg-black text-success p-3 rounded small">
<?= print_r($row, true) ?>
        </pre>
    <?php endwhile; ?>

<?php else: ?>
    <pre class="bg-black p-3 rounded small">
0 row returned
    </pre>
<?php endif; ?>

        </pre>

        <p class="small mt-3">
            Internal debugging interface — validation disabled.
        </p>
    </div>

    <div class="footer text-center mt-5">
        Spectre-7 
    </div>

</main>
</body>
</html>

