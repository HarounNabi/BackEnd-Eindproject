<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'Admin') {
    header("Location: inlog.php");
    exit();
}

$message = '';

if (isset($_POST['submit'])) {
    $naam = $_POST['naam'];
    $prijs = $_POST['prijs'];
    $rating = $_POST['rating'];
    $foto_url = $_POST['foto_url'];

    if ($naam != '' && $prijs != '' && $foto_url != '') {
        $query = $pdo->prepare("INSERT INTO items (naam, prijs, rating, foto_url) VALUES (:naam, :prijs, :rating, :foto_url)");
        $query->execute([
            ':naam' => $naam,
            ':prijs' => $prijs,
            ':rating' => $rating,
            ':foto_url' => $foto_url
        ]);
        $message = "Product succesvol toegevoegd!";
    } else {
        $message = "Vul alle verplichte velden in.";
    }
}

$items_query = $pdo->query("SELECT * FROM items");
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/css">
        /* Custom scrollbar behouden voor het assortiment */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }

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

        :has(#theme-toggle-input:checked) .light-border-gray-200 {
            border-color: #e5e7eb;
        }

        :has(#theme-toggle-input:checked) .light-hover-bg-gray-100:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-900 text-gray-100 light-bg-gray-50 light-text-gray-900 transition-colors duration-200">

    <input type="checkbox" id="theme-toggle-input" class="hidden">

    <header class="bg-gray-950 light-bg-white border-b border-gray-800 light-border-gray-200 fixed top-0 left-0 w-full z-50 transition-colors duration-200">
        <div class="w-full px-8 h-20 flex items-center justify-between">

            <a href="home.php" class="flex items-center shrink-0">
                <img src="logo.png" alt="TechShop Logo" class="h-14 w-auto object-contain">
            </a>

            <nav class="flex items-center gap-6">
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] == 'Admin'): ?>
                    <a href="admin.php" class="text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1.5">⚙ Admin Panel</a>
                <?php endif; ?>

                <a href="home.php" class="text-sm font-medium text-gray-200 light-text-gray-700 hover:text-white transition-colors">Producten</a>
                <a href="winkelwagen.php" class="text-sm font-medium text-gray-200 light-text-gray-700 hover:text-white transition-colors flex items-center gap-1">🛒 Winkelwagen</a>

                <label for="theme-toggle-input" class="cursor-pointer text-lg p-2 rounded-xl hover:bg-gray-900/50 light-hover-bg-gray-100 transition-colors select-none">
                    🌓
                </label>

                <?php if (isset($_SESSION['user'])): ?>
                    <span class="text-xs bg-gray-900 light-bg-gray-50 border border-gray-800 light-border-gray-200 px-3 py-1.5 rounded-full text-gray-400 light-text-gray-700">
                        Gebruiker: <b class="text-gray-200 light-text-gray-900"><?= htmlspecialchars($_SESSION['user']) ?></b>
                    </span>
                    <a href="loguit.php" class="text-sm font-medium text-red-400 hover:text-red-600 transition-colors">Uitloggen</a>
                <?php endif; ?>
            </nav>

        </div>
    </header>

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <div class="bg-gray-950 light-bg-white p-6 rounded-2xl border border-gray-800 light-border-gray-200 shadow-xl h-fit">
                <h2 class="text-2xl font-bold mb-6 text-white light-text-gray-900 tracking-wide">Nieuw Product Toevoegen</h2>

                <?php if ($message != ''): ?>
                    <div class="mb-6 p-4 rounded-xl bg-blue-950/50 border border-blue-800 text-blue-300 text-sm font-medium">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="admin.php" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 light-text-gray-700 mb-2">Productnaam:</label>
                        <input type="text" name="naam" required class="w-full bg-gray-900 light-bg-white border border-gray-700 light-border-gray-200 rounded-xl px-4 py-3 text-white light-text-gray-900 focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 light-text-gray-700 mb-2">Prijs (€):</label>
                            <input type="number" name="prijs" step="0.01" required class="w-full bg-gray-900 light-bg-white border border-gray-700 light-border-gray-200 rounded-xl px-4 py-3 text-white light-text-gray-900 focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 light-text-gray-700 mb-2">Rating (1-5):</label>
                            <input type="number" name="rating" step="0.1" min="1" max="5" class="w-full bg-gray-900 light-bg-white border border-gray-700 light-border-gray-200 rounded-xl px-4 py-3 text-white light-text-gray-900 focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 light-text-gray-700 mb-2">Afbeelding URL:</label>
                        <input type="url" name="foto_url" required class="w-full bg-gray-900 light-bg-white border border-gray-700 light-border-gray-200 rounded-xl px-4 py-3 text-white light-text-gray-900 focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <button type="submit" name="submit" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center">
                        Product Opslaan
                    </button>
                </form>
            </div>

            <div class="bg-gray-950 light-bg-white p-6 rounded-2xl border border-gray-800 light-border-gray-200 shadow-xl flex flex-col h-[600px]">
                <h2 class="text-2xl font-bold mb-6 text-white light-text-gray-900 tracking-wide">Huidig Assortiment</h2>
                <div class="overflow-y-auto grow rounded-xl border border-gray-800 light-border-gray-200 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-900 light-bg-gray-50 sticky top-0 border-b border-gray-800 light-border-gray-200">
                            <tr>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">ID</th>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">Naam</th>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">Prijs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50 light-border-gray-200">
                            <?php while ($item = $items_query->fetch()): ?>
                                <tr class="hover:bg-gray-900/40 transition-colors">
                                    <td class="p-4 text-sm font-mono text-gray-500">#<?= $item['id'] ?></td>
                                    <td class="p-4 text-sm font-medium text-gray-200 light-text-gray-900"><?= htmlspecialchars($item['naam']) ?></td>
                                    <td class="p-4 text-sm font-semibold text-blue-400">€<?= number_format($item['prijs'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
    <footer class="bg-gray-950 light-bg-white border-t border-gray-800 light-border-gray-200 py-6 mt-auto transition-colors duration-200">
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