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
    header("Location: home.php");
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
    header("Location: home.php");
    exit();
}

$query = $pdo->query("SELECT * FROM items");
$products = $query->fetchAll();

$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Hardware & Gaming Gear</title>
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

                <a href="home.php?action=toggle_theme" class="cursor-pointer text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-185">
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

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16 px">

        <div class="mb-12 text-center sm:text-left">
            <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl bg-linear-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Tech & goods
            </h2>
            <p class="mt-3 text-base text-muted<?= $suffix ?> max-w-md">
                Upgrade je setup met de nieuwste items
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($products as $product): ?>
                <div class="bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> overflow-hidden shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all flex flex-col h-full group">

                    <div class="relative w-full pt-[75%] bg-transparent overflow-hidden p-6 flex items-center justify-center shrink-0">
                        <img src="<?= htmlspecialchars($product['foto_url']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>" class="absolute max-w-[85%] max-h-[85%] object-contain group-hover:scale-105 transition-transform duration-300">
                    </div>

                    <div class="p-6 flex flex-col grow">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <h3 class="text-base font-bold tracking-tight line-clamp-2 min-h-12 grow">
                                <?= htmlspecialchars($product['naam']) ?>
                            </h3>
                            <div class="flex items-center gap-1 bg-main<?= $suffix ?> border border<?= $suffix ?> px-2 py-1 rounded-lg shrink-0">
                                <span class="text-amber-400 text-xs font-bold">★</span>
                                <span class="text-xs font-semibold font-mono text-main<?= $suffix ?>"><?= number_format($product['rating'], 1) ?></span>
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-t border<?= $suffix ?> flex items-center justify-between gap-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-muted<?= $suffix ?> font-medium uppercase tracking-wider">Prijs</span>
                                <span class="text-xl font-black text-blue-500 font-mono">€<?= number_format($product['prijs'], 2, ',', '.') ?></span>
                            </div>

                            <form action="home.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="add_to_cart" class="cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-blue-600/10 transition-all text-sm flex items-center gap-1.5">
                                    <span>In Wagen</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

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