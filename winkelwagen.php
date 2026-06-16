<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: inlog.php");
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $aantal = $_POST['aantal'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $aantal;
    } else {
        $_SESSION['cart'][$product_id] = $aantal;
    }
    header("Location: home.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: winkelwagen.php");
    exit();
}

$totaal = 0;
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Winkelwagen</title>
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
                    <a href="admin.php" class="text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1.5">☰ Admin Panel</a>
                <?php endif; ?>

                <a href="home.php" class="text-sm font-medium text-gray-200 light-text-gray-700 hover:text-white transition-colors">Producten</a>
                <a href="winkelwagen.php" class="text-sm font-medium text-gray-200 light-text-gray-700 hover:text-white transition-colors flex items-center gap-1">🛒 Winkelwagen</a>

                <label for="theme-toggle-input" class="cursor-pointer text-lg p-2 rounded-xl hover:bg-gray-900/50 light-hover-bg-gray-100 transition-colors select-none">
                    <div class="scale-185">
                        ☯︎
                    </div>
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

    <main class="grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-white light-text-gray-900 mb-8">Jouw Winkelwagen</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-gray-950 light-bg-white rounded-2xl border border-gray-800 light-border-gray-200 p-10 text-center shadow-md">
                <p class="text-gray-400 light-text-gray-700 mb-4">Je winkelwagen is momenteel leeg.</p>
                <a href="home.php" class="inline-flex bg-gray-900 light-bg-gray-50 hover:bg-gray-850 text-blue-400 border border-gray-700 light-border-gray-200 px-5 py-2 rounded-xl text-sm font-medium transition-all">Shop Nu</a>
            </div>
        <?php else: ?>
            <div class="bg-gray-950 light-bg-white rounded-2xl border border-gray-800 light-border-gray-200 shadow-xl overflow-hidden transition-colors duration-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 light-bg-gray-50 border-b border-gray-800 light-border-gray-200">
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">Product</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700 text-center">Aantal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">Prijs per stuk</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700">Subtotaal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 light-text-gray-700 text-center">Actie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50 light-border-gray-200">
                        <?php foreach ($_SESSION['cart'] as $id => $aantal): ?>
                            <?php
                            $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
                            $query->execute([':id' => $id]);
                            $product = $query->fetch();

                            $subtotaal = $product['prijs'] * $aantal;
                            $totaal += $subtotaal;
                            ?>
                            <tr class="hover:bg-gray-900/20 transition-colors">
                                <td class="p-4 text-sm font-semibold text-gray-200 light-text-gray-900"><?= htmlspecialchars($product['naam']) ?></td>
                                <td class="p-4 text-sm font-mono text-gray-300 light-text-gray-900 text-center"><?= $aantal ?></td>
                                <td class="p-4 text-sm text-gray-400 light-text-gray-700">€<?= number_format($product['prijs'], 2, ',', '.') ?></td>
                                <td class="p-4 text-sm font-bold text-blue-400">€<?= number_format($subtotaal, 2, ',', '.') ?></td>
                                <td class="p-4 text-sm text-center">
                                    <a href="winkelwagen.php?action=delete&id=<?= $product['id'] ?>" class="text-red-400 hover:text-red-300 transition-colors font-medium">Verwijder</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="bg-gray-900/60 light-bg-gray-50 font-semibold border-t-2 border-gray-800 light-border-gray-200">
                            <td colspan="3" class="p-5 text-sm text-right text-gray-400 light-text-gray-700">Totaal bedrag:</td>
                            <td colspan="2" class="p-5 text-2xl font-black text-white light-text-gray-900">€<?= number_format($totaal, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-end">
                <form action="checkout.php" method="post" class="w-full sm:w-auto">
                    <button type="submit" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center tracking-wide text-base">
                        Afrekenen
                    </button>
                </form>
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