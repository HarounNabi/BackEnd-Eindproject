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
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Winkelwagen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="flex flex-col min-h-screen">

    <header class="bg-gray-950 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="home.php" class="flex items-center">
                <img src="logo.png" alt="TechShop Logo" class="h-12 w-auto object-contain">
            </a>
            <nav class="flex items-center gap-6">
                <a href="home.php" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">Terug naar Producten</a>
            </nav>
        </div>
    </header>

    <main class="grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-white mb-8">Jouw Winkelwagen</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-gray-950 rounded-2xl border border-gray-800 p-10 text-center shadow-md">
                <p class="text-gray-400 mb-4">Je winkelwagen is momenteel leeg.</p>
                <a href="home.php" class="inline-flex bg-gray-900 hover:bg-gray-850 text-blue-400 border border-gray-700 px-5 py-2 rounded-xl text-sm font-medium transition-all">Shop Nu</a>
            </div>
        <?php else: ?>
            <div class="bg-gray-950 rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 border-b border-gray-800">
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Product</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 text-center">Aantal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Prijs per stuk</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Subtotaal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400 text-center">Actie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        <?php foreach ($_SESSION['cart'] as $id => $aantal): ?>
                            <?php
                            $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
                            $query->execute([':id' => $id]);
                            $product = $query->fetch();

                            $subtotaal = $product['prijs'] * $aantal;
                            $totaal += $subtotaal;
                            ?>
                            <tr class="hover:bg-gray-900/20 transition-colors">
                                <td class="p-4 text-sm font-semibold text-gray-200"><?= htmlspecialchars($product['naam']) ?></td>
                                <td class="p-4 text-sm font-mono text-gray-300 text-center"><?= $aantal ?></td>
                                <td class="p-4 text-sm text-gray-400">€<?= number_format($product['prijs'], 2, ',', '.') ?></td>
                                <td class="p-4 text-sm font-bold text-blue-400">€<?= number_format($subtotaal, 2, ',', '.') ?></td>
                                <td class="p-4 text-sm text-center">
                                    <a href="winkelwagen.php?action=delete&id=<?= $product['id'] ?>" class="text-red-400 hover:text-red-300 transition-colors font-medium">Verwijder</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="bg-gray-900/60 font-semibold border-t-2 border-gray-800">
                            <td colspan="3" class="p-5 text-sm text-right text-gray-400">Totaal bedrag:</td>
                            <td colspan="2" class="p-5 text-2xl font-black text-white">€<?= number_format($totaal, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-950 border-t border-gray-800 flex justify-end">
                <form action="gelukt.php" method="post" class="cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center tracking-wide text-l">
                    <button type="submit">Submit</button>
                </form>
            </div>
        <?php endif; ?>
    </main>

</body>

</html>