<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: inlog.php");
    exit();
}

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle_theme') {
    $_SESSION['theme'] = ($_SESSION['theme'] == 'dark') ? 'light' : 'dark';
    header("Location: winkelwagen.php");
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    header("Location: winkelwagen.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    unset($_SESSION['cart'][$id]);
    header("Location: winkelwagen.php");
    exit();
}

$totaal = 0;
$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Winkelwagen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col min-h-screen bg-main<?= $suffix ?> text-main<?= $suffix ?> transition-colors duration-200">

    <header class="bg-card<?= $suffix ?> border-b border<?= $suffix ?> fixed top-0 left-0 w-full z-50 transition-colors duration-200">
        <div class="w-full px-8 h-20 flex items-center justify-between">

            <a href="home.php" class="flex items-center shrink-0">
                <img src="logo.png" alt="TechShop Logo" class="h-14 w-auto object-contain">
            </a>

            <nav class="flex items-center gap-6">
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] == 'Admin'): ?>
                    <a href="admin.php" class="text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1.5"> Admin Panel</a>
                <?php endif; ?>

                <a href="home.php" class="text-sm font-medium text-main<?= $suffix ?> hover:opacity-80 transition-colors">Producten</a>
                <a href="winkelwagen.php" class="text-sm font-medium text-main<?= $suffix ?> hover:opacity-80 transition-colors flex items-center gap-1">
                    🛒 Winkelwagen
                    <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                        <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                    </span>
                </a>

                <a href="winkelwagen.php?action=toggle_theme" class="cursor-pointer text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-185">
                    ☯︎
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <span class="text-xs bg-main<?= $suffix ?> border border<?= $suffix ?> px-3 py-1.5 rounded-full text-muted<?= $suffix ?>">
                        Gebruiker: <b class="text-main<?= $suffix ?>"><?= htmlspecialchars($_SESSION['user']) ?></b>
                    </span>
                    <a href="loguit.php" class="text-sm font-medium text-red-400 hover:text-red-600 transition-colors">Uitloggen</a>
                <?php endif; ?>
            </nav>

        </div>
    </header>

    <main class="grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-10">
        <h2 class="text-3xl font-extrabold tracking-tight mb-8">Jouw Winkelwagen</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> p-10 text-center shadow-md">
                <p class="text-muted<?= $suffix ?> mb-4">Je winkelwagen is momenteel leeg.</p>
                <a href="home.php" class="inline-flex bg-main<?= $suffix ?> text-blue-400 border border<?= $suffix ?> px-5 py-2 rounded-xl text-sm font-medium transition-all">Shop Nu</a>
            </div>
        <?php else: ?>
            <div class="bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> shadow-xl overflow-hidden transition-colors duration-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-main<?= $suffix ?> border-b border<?= $suffix ?>">
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Product</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> text-center">Aantal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Prijs per stuk</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Subtotaal</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> text-center">Actie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border<?= $suffix ?>">
                        <?php foreach ($_SESSION['cart'] as $id => $aantal): ?>
                            <?php
                            $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
                            $query->execute([':id' => $id]);
                            $product = $query->fetch();

                            if (!$product) continue;

                            $subtotaal = $product['prijs'] * $aantal;
                            $totaal += $subtotaal;
                            ?>
                            <tr class="hover<?= $suffix ?> transition-colors">
                                <td class="p-4 text-sm font-semibold"><?= htmlspecialchars($product['naam']) ?></td>
                                <td class="p-4 text-sm font-mono text-center"><?= $aantal ?></td>
                                <td class="p-4 text-sm text-muted<?= $suffix ?>">€<?= number_format($product['prijs'], 2, ',', '.') ?></td>
                                <td class="p-4 text-sm font-bold text-blue-400">€<?= number_format($subtotaal, 2, ',', '.') ?></td>
                                <td class="p-4 text-sm text-center">
                                    <a href="winkelwagen.php?action=delete&id=<?= $product['id'] ?>" class="text-red-400 hover:text-red-300 transition-colors font-medium">Verwijder</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="bg-main<?= $suffix ?> font-semibold border-t-2 border<?= $suffix ?>">
                            <td colspan="3" class="p-5 text-sm text-right text-muted<?= $suffix ?>">Totaal bedrag:</td>
                            <td colspan="2" class="p-5 text-2xl font-black text-blue-500">€<?= number_format($totaal, 2, ',', '.') ?></td>
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
    <footer class="bg-card<?= $suffix ?> border-t border<?= $suffix ?> py-6 mt-auto transition-colors duration-200">
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