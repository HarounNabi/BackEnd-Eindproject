<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: inlog.php");
    exit();
}

$all_items = $pdo->query("SELECT * FROM items");
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Home</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/css">
        /* We gebruiken pure CSS om de lichte modus classes te activeren als de checkbox aan staat */
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

                <a href="home.php" class="text-sm font-medium text-gray-200 light:text-gray-700 light-text-gray-700 hover:text-white light-hover-text-black transition-colors">Producten</a>
                <a href="winkelwagen.php" class="text-sm font-medium text-gray-200 light:text-gray-700 light-text-gray-700 hover:text-white light-hover-text-black transition-colors flex items-center gap-1">🛒 Winkelwagen</a>

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
        <?php if (isset($_GET['id'])): ?>
            <?php
            $id = $_GET['id'];
            $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
            $query->execute([':id' => $id]);
            $item = $query->fetch();
            ?>
            <div class="bg-gray-950 light-bg-white rounded-2xl border border-gray-800 light-border-gray-200 p-6 md:p-10 shadow-xl max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 transition-colors duration-200">
                <div class="bg-gray-900 light-bg-gray-50 rounded-xl p-6 flex items-center justify-center border border-gray-850 light-border-gray-200 aspect-square">
                    <img src="<?= $item['foto_url'] ?>" class="max-h-full max-w-full object-contain rounded-lg">
                </div>
                <div class="flex flex-col justify-between py-2">
                    <div>
                        <h2 class="text-3xl font-extrabold text-white light-text-gray-900 tracking-tight mb-4"><?= htmlspecialchars($item['naam']) ?></h2>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-amber-400 text-lg">★</span>
                            <span class="text-sm font-semibold text-gray-300 light-text-gray-700"><?= $item['rating'] ?? 'N/A' ?> / 5.0</span>
                        </div>
                        <p class="text-4xl font-black text-blue-400 mb-8">€<?= number_format($item['prijs'], 2, ',', '.') ?></p>
                    </div>

                    <form action="winkelwagen.php" method="POST" class="space-y-4">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-400 light-text-gray-700">Aantal:</label>
                            <input type="number" name="aantal" value="1" min="1" class="w-20 bg-gray-900 light-bg-white border border-gray-700 light-border-gray-200 rounded-xl px-3 py-2 text-white light-text-gray-900 font-semibold text-center focus:outline-none focus:border-blue-500">
                        </div>
                        <button type="submit" name="add_to_cart" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center">
                            In Winkelwagen
                        </button>
                    </form>

                    <a href="home.php" class="mt-6 text-sm text-gray-500 hover:text-gray-400 transition-colors block text-center md:text-left">← Terug naar het overzicht</a>
                </div>
            </div>
        <?php else: ?>
            <h2 class="text-3xl font-extrabold tracking-tight text-white light-text-gray-900 mb-8 transition-colors duration-200">
                Onze Producten
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($item = $all_items->fetch()): ?>
                    <div class="bg-gray-950 light-bg-white rounded-2xl border border-gray-800 light-border-gray-200 p-5 flex flex-col justify-between shadow-md hover:shadow-xl transition-all group duration-200">
                        <div>
                            <div class="bg-gray-900 light-bg-gray-50 rounded-xl p-4 flex items-center justify-center border border-gray-850 light-border-gray-200 h-48 mb-4 overflow-hidden">
                                <img src="<?= $item['foto_url'] ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <h3 class="text-lg font-bold text-gray-200 light-text-gray-900 line-clamp-2 mb-2 group-hover:text-white light-group-hover:text-black"><?= htmlspecialchars($item['naam']) ?></h3>
                        </div>
                        <div class="mt-4">
                            <p class="text-xl font-black text-blue-400 mb-4">€<?= number_format($item['prijs'], 2, ',', '.') ?></p>
                            <a href="home.php?id=<?= $item['id'] ?>" class="w-full block bg-gray-900 light-bg-gray-50 hover:bg-gray-850 border border-gray-700 light-border-gray-200 text-gray-200 light-text-gray-700 hover:text-white font-medium py-2.5 px-4 rounded-xl text-center text-sm transition-all">
                                Bekijk Product
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
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