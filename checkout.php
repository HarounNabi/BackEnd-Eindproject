<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: inlog.php");
    exit();
}

if (empty($_SESSION['cart']) && !isset($_POST['place_order'])) {
    header("Location: home.php");
    exit();
}

$order_success = false;
$totaal = 0;
$items_to_display = [];

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $aantal) {
        $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
        $query->execute([':id' => $id]);
        $product = $query->fetch();

        if ($product) {
            $subtotaal = $product['prijs'] * $aantal;
            $totaal += $subtotaal;
            $items_to_display[] = [
                'naam' => $product['naam'],
                'aantal' => $aantal,
                'subtotaal' => $subtotaal
            ];
        }
    }
}

if (isset($_POST['place_order'])) {
    unset($_SESSION['cart']);
    $order_success = true;
}
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Afrekenen</title>
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

        :has(#theme-toggle-input:checked) .light-text-gray-600 {
            color: #4b5563;
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

    <main class="grow max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16 flex flex-col justify-center">

        <?php if ($order_success): ?>
            <div class="text-center space-y-6 py-12">
                <div class="text-7xl">🎉</div>
                <h1 class="text-5xl font-black tracking-tight text-white light-text-gray-900">Het is je gelukt!</h1>
                <p class="text-gray-400 light-text-gray-700 text-lg max-w-md mx-auto">Je bestelling is succesvol verwerkt en de winkelwagen is geleegd. Bedankt voor je aankoop!</p>

                <div class="pt-4">
                    <a href="home.php" class="inline-flex bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center">
                        Wil je verder shoppen?
                    </a>
                </div>
            </div>

        <?php else: ?>
            <h2 class="text-3xl font-extrabold tracking-tight text-white light-text-gray-900 mb-8">Bestelling Controleren</h2>

            <div class="bg-gray-950 light-bg-white rounded-2xl border border-gray-800 light-border-gray-200 shadow-xl overflow-hidden p-6 md:p-8 space-y-6 transition-colors duration-200">

                <div class="border-b border-gray-800 light-border-gray-200 pb-4">
                    <h3 class="text-lg font-bold text-gray-200 light-text-gray-900 mb-1">Overzicht van je producten</h3>
                    <p class="text-xs text-gray-500 light-text-gray-700">Controleer je winkelmand voordat je de aankoop definitief maakt.</p>
                </div>

                <ul class="divide-y divide-gray-800/40 light-border-gray-200">
                    <?php foreach ($items_to_display as $item): ?>
                        <li class="py-4 flex justify-between items-center text-sm">
                            <div>
                                <span class="font-semibold text-gray-200 light-text-gray-900"><?= htmlspecialchars($item['naam']) ?></span>
                                <span class="text-xs text-gray-500 light-text-gray-700 ml-2">x<?= $item['aantal'] ?></span>
                            </div>
                            <span class="font-medium text-gray-300 light-text-gray-900">€<?= number_format($item['subtotaal'], 2, ',', '.') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="bg-gray-900 light-bg-gray-50 rounded-xl p-4 flex justify-between items-center border border-gray-850 light-border-gray-200">
                    <span class="text-sm font-semibold text-gray-400 light-text-gray-700">Totaal te betalen bedrag:</span>
                    <span class="text-2xl font-black text-blue-400">€<?= number_format($totaal, 2, ',', '.') ?></span>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="winkelwagen.php" class="text-sm text-gray-500 hover:text-gray-400 light:hover:text-gray-900 transition-colors">
                        ← Terug naar winkelwagen
                    </a>

                    <form action="checkout.php" method="POST" class="w-full sm:w-auto">
                        <button type="submit" name="place_order" class="w-full sm:w-auto cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-emerald-600/10 transition-all text-center tracking-wide">
                            Bestelling Plaatsen
                        </button>
                    </form>
                </div>

            </div>
        <?php endif; ?>
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