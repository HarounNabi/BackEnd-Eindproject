<?php
session_start();
include_once 'db/db.php';

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
?>
<!DOCTYPE html>
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Inloggen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="flex flex-col min-h-screen justify-center items-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="w-full max-w-4xl space-y-8">
        <div class="flex justify-start">
            <img src="logo.png" alt="TechShop Logo" class="h-16 w-auto object-contain">
        </div>

        <?php if ($error != ''): ?>
            <div class="p-4 rounded-xl bg-red-950/50 border border-red-800 text-red-400 text-sm font-medium text-center max-w-md mx-auto">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
            <div class="p-4 rounded-xl bg-green-950/50 border border-green-800 text-green-400 text-sm font-medium text-center max-w-md mx-auto">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            <div class="bg-gray-950 p-6 md:p-8 rounded-2xl border border-gray-800 shadow-xl">
                <h2 class="text-2xl font-bold mb-6 text-white tracking-wide">Inloggen</h2>
                <form action="inlog.php" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Gebruikersnaam:</label>
                        <input type="text" name="gebruikersnaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Wachtwoord:</label>
                        <input type="password" name="wachtwoord" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <button type="submit" name="login" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center">
                        Inloggen
                    </button>
                </form>
                <div class="mt-6 pt-6 border-t border-gray-800 text-center">
                    <p class="text-sm text-gray-400">Of ga direct verder als <a href="gastlogin.php" class="text-blue-400 hover:underline font-medium">Gast</a></p>
                </div>
            </div>

            <div class="bg-gray-950 p-6 md:p-8 rounded-2xl border border-gray-800 shadow-xl">
                <h2 class="text-2xl font-bold mb-6 text-white tracking-wide">Account Aanmaken</h2>
                <form action="inlog.php" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Gebruikersnaam:</label>
                        <input type="text" name="reg_gebruikersnaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Wachtwoord:</label>
                        <input type="password" name="reg_wachtwoord" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <button type="submit" name="register" class="w-full cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center">
                        Registreren
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>

</html>