<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle_theme') {
    $_SESSION['theme'] = ($_SESSION['theme'] == 'dark') ? 'light' : 'dark';
    header("Location: inlog.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = $_POST['reg_gebruikersnaam'];
    $password = $_POST['reg_wachtwoord'];

    if ($username != '' && $password != '') {
        $query = $pdo->prepare("SELECT * FROM gebruikers WHERE gebruikersnaam = :username");
        $query->execute([':username' => $username]);
        $userExists = $query->fetch();

        if ($userExists) {
            $error = "Deze gebruikersnaam bestaat al!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert = $pdo->prepare("INSERT INTO gebruikers (gebruikersnaam, wachtwoord) VALUES (:username, :password)");
            $insert->execute([
                ':username' => $username,
                ':password' => $hashed_password
            ]);
            $success = "Account aangemaakt! Je kunt nu inloggen.";
        }
    } else {
        $error = "Vul alle velden in.";
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['gebruikersnaam'];
    $password = $_POST['wachtwoord'];

    if ($username != '' && $password != '') {
        $query = $pdo->prepare("SELECT * FROM gebruikers WHERE gebruikersnaam = :username");
        $query->execute([':username' => $username]);
        $user = $query->fetch();

        if ($user && password_verify($password, $user['wachtwoord'])) {
            $_SESSION['user'] = $user['gebruikersnaam'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Onjuiste gebruikersnaam of wachtwoord.";
        }
    } else {
        $error = "Vul alle velden in.";
    }
}

$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full bg-main<?= $suffix ?> text-main<?= $suffix ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Inloggen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col min-h-screen justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-main<?= $suffix ?> text-main<?= $suffix ?>">

    <div class="w-full max-w-md space-y-8">

        <div class="text-center space-y-2 relative">
            <h1 class="text-4xl font-extrabold tracking-wider bg-gradient-to-red from-white via-blue-300 to-blue-500 bg-clip-text drop-shadow-[0_2px_10px_rgba(59,130,246,0.3)]">
                TECH<span class="text-blue-500">SHOP</span>
            </h1>
            <a href="inlog.php?action=toggle_theme" class="absolute right-0 top-0 text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-130">
                ☯︎
            </a>
        </div>

        <?php if ($error != ''): ?>
            <div class="p-4 rounded-xl bg-red-950/50 border border-red-800 text-red-400 text-sm font-medium text-center mx-auto">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
            <div class="p-4 rounded-xl bg-green-950/50 border border-green-800 text-green-400 text-sm font-medium text-center mx-auto">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="bg-card<?= $suffix ?> p-8 rounded-2xl border border<?= $suffix ?> shadow-2xl shadow-blue-950/10">
            <h2 class="text-2xl font-bold mb-6 text-center tracking-wide">Inloggen</h2>

            <form action="inlog.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Gebruikersnaam:</label>
                    <input type="text" name="gebruikersnaam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Wachtwoord:</label>
                    <input type="password" name="wachtwoord" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500 transition-colors">
                </div>
                <button type="submit" name="login" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center shadow-lg shadow-blue-600/20">
                    Inloggen
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border<?= $suffix ?> flex flex-col sm:flex-row justify-center items-center gap-4 sm:gap-8 text-sm text-gray-400">
                <p>Wil je een account? <a href="reg.php" class="text-blue-400 hover:underline font-medium">Registreer hier</a></p>
                <p class="hidden sm:block text-gray-600">|</p>
                <p>Of ga direct verder als <a href="gastlogin.php" class="text-blue-400 hover:underline font-medium">Gast</a></p>
            </div>

        </div>
    </div>

    <footer class="w-full bg-card<?= $suffix ?> border-t border<?= $suffix ?> py-6 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted<?= $suffix ?>">
            <p>&copy; <?= date("Y"); ?> TechShop. Alle rechten voorbehouden.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-gray-300 transition-colors">Algemene Voorwaarden</a>
                <a href="#" class="hover:text-gray-300 transition-colors">Privacybeleid</a>
                <a href="mailto:haroun.9707@gmail.com" class="hover:text-gray-300 transition-colors">Contact</a>
            </div>
        </div>
    </footer>
</body>

</html>