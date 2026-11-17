<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle_theme') {
    $_SESSION['theme'] = ($_SESSION['theme'] == 'dark') ? 'light' : 'dark';
    header("Location: reg.php");
    exit();
}

$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full bg-main<?= $suffix ?> text-main<?= $suffix ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Account Aanmaken</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col min-h-screen justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-main<?= $suffix ?> text-main<?= $suffix ?> transition-colors duration-200">

    <div class="w-full max-w-md space-y-8">

        <div class="text-center space-y-2 relative">
            <h1 class="text-4xl font-extrabold tracking-wider bg-gradient-to-red from-white via-blue-100 to-blue-500 bg-clip-text drop-shadow-[0_2px_10px_rgba(59,130,246,0.3)]">
                TECH<span class="text-blue-500">SHOP</span>
            </h1>
            <a href="reg.php?action=toggle_theme" class="absolute right-0 top-0 text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-130">
                ☯︎
            </a>
        </div>

        <div class="bg-card<?= $suffix ?> p-8 rounded-2xl border border<?= $suffix ?> shadow-2xl shadow-emerald-950/5">
            <h2 class="text-2xl font-bold mb-6 text-center tracking-wide">Account Aanmaken</h2>

            <form action="inlog.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Gebruikersnaam:</label>
                    <input type="text" name="reg_gebruikersnaam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Wachtwoord:</label>
                    <input type="password" name="reg_wachtwoord" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <button type="submit" name="register" class="w-full cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center shadow-lg shadow-emerald-600/20">
                    Registreren
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border<?= $suffix ?> text-center text-sm text-gray-400">
                <p>Heb je al een account? <a href="inlog.php" class="text-blue-400 hover:underline font-medium">Hier inloggen</a></p>
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