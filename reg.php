<?php
session_start();
include_once 'db/db.php';
?>

<!DOCTYPE html>
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Account Aanmaken</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/css">
        :has(#theme-toggle-input:checked) .light-bg-white {
            background-color: #ffffff;
        }

        :has(#theme-toggle-input:checked) .light-bg-gray-50 {
            background-color: #f9fafb;
        }

        :has(#theme-toggle-input:checked) .light-text-gray-900 {
            color: #111827;
        }

        :has(#theme-toggle-input:checked) .light-text-gray-700 {
            color: #374151;
        }

        :has(#theme-toggle-input:checked) .light-text-black {
            color: #000000;
        }

        :has(#theme-toggle-input:checked) .light-border-gray-200 {
            border-color: #e5e7eb;
        }

        :has(#theme-toggle-input:checked) .light-hover-bg-gray-100:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-900 text-gray-100 light-bg-gray-50 light-text-gray-900 transition-colors duration-200">

    <div class="w-full max-w-md space-y-8">

        <div class="text-center space-y-2">
            <h1 class="text-4xl font-extrabold tracking-wider bg-gradient-to-red from-white via-blue-100 to-blue-500 bg-clip-text drop-shadow-[0_2px_10px_rgba(59,130,246,0.3)]">
                TECH<span class="text-blue-500">SHOP</span>
            </h1>
        </div>

        <div class="bg-gray-950 p-8 rounded-2xl border border-gray-800 shadow-2xl shadow-emerald-950/5">
            <h2 class="text-2xl font-bold mb-6 text-center text-white tracking-wide">Account Aanmaken</h2>

            <form action="inlog.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Gebruikersnaam:</label>
                    <input type="text" name="reg_gebruikersnaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Wachtwoord:</label>
                    <input type="password" name="reg_wachtwoord" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <button type="submit" name="register" class="w-full cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center shadow-lg shadow-emerald-600/20">
                    Registreren
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-800 text-center text-sm text-gray-400">
                <p>Heb je al een account? <a href="inlog.php" class="text-blue-400 hover:underline font-medium">Hier inloggen</a></p>
            </div>

        </div>
    </div>

    <footer class="w-full bg-gray-950 light-bg-white border-t border-gray-800 light-border-gray-200 py-6 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500 light-text-gray-600">
            <p>&copy; <?= date("Y"); ?> TechShop. Alle rechten voorbehouden.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-gray-300 light:hover:text-gray-900 transition-colors">Algemene Voorwaarden</a>
                <a href="#" class="hover:text-gray-300 light:hover:text-gray-900 transition-colors">Privacybeleid</a>
                <a href="mailto:haroun.9707@gmail.com" class="hover:text-gray-300 light:hover:text-gray-900 transition-colors">Contact</a>
            </div>
        </div>
    </footer>

</body>

</html>