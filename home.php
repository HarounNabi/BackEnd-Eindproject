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
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Home</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="flex flex-col min-h-screen">

    <header class="bg-gray-950 border-b border-gray-800 sticky top-0 z-50">
        <div class="mx-auto mt-1 px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="home.php" class="flex items-center">
                <img src="logo.png" alt="TechShop Logo" class="h-12 w-auto object-contain">
            </a>

            <nav class="flex items-center gap-6">
                <?php if ($_SESSION['user'] == 'Admin'): ?>
                    <a href="admin.php" class="text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1.5">⚙ Admin Panel</a>
                <?php endif; ?>
                <a href="home.php" class="text-sm font-medium text-gray-200 hover:text-white transition-colors">Producten</a>
                <a href="winkelwagen.php" class="text-sm font-medium text-gray-200 hover:text-white transition-colors flex items-center gap-1">🛒 Winkelwagen</a>
                <span class="text-xs bg-gray-900 border border-gray-800 px-3 py-1.5 rounded-full text-gray-400">
                    Gebruiker: <b class="text-gray-200"><?= htmlspecialchars($_SESSION['user']) ?></b>
                </span>
                <a href="loguit.php" class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors">Uitloggen</a>
            </nav>
        </div>
    </header>

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <?php if (isset($_GET['id'])): ?>
            <?php
            $id = $_GET['id'];
            $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
            $query->execute([':id' => $id]);
            $item = $query->fetch();
            ?>
            <div class="bg-gray-950 rounded-2xl border border-gray-800 p-6 md:p-10 shadow-xl max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="bg-gray-900 rounded-xl p-6 flex items-center justify-center border border-gray-850 aspect-square">
                    <img src="<?= $item['foto_url'] ?>" class="max-h-full max-w-full object-contain rounded-lg">
                </div>
                <div class="flex flex-col justify-between py-2">
                    <div>
                        <h2 class="text-3xl font-extrabold text-white tracking-tight mb-4"><?= htmlspecialchars($item['naam']) ?></h2>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-amber-400 text-lg">★</span>
                            <span class="text-sm font-semibold text-gray-300"><?= $item['rating'] ?? 'N/A' ?> / 5.0</span>
                        </div>
                        <p class="text-4xl font-black text-blue-400 mb-8">€<?= number_format($item['prijs'], 2, ',', '.') ?></p>
                    </div>

                    <form action="winkelwagen.php" method="POST" class="space-y-4">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-400">Aantal:</label>
                            <input type="number" name="aantal" value="1" min="1" class="w-20 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-white font-semibold text-center focus:outline-none focus:border-blue-500">
                        </div>
                        <button type="submit" name="add_to_cart" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center">
                            In Winkelwagen
                        </button>
                    </form>

                    <a href="home.php" class="mt-6 text-sm text-gray-500 hover:text-gray-400 transition-colors block text-center md:text-left">← Terug naar het overzicht</a>
                </div>
            </div>
        <?php else: ?>
            <h2 class="text-3xl font-extrabold tracking-tight text-white mb-8">Onze Producten</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($item = $all_items->fetch()): ?>
                    <div class="bg-gray-950 rounded-2xl border border-gray-800 p-5 flex flex-col justify-between shadow-md hover:shadow-xl hover:border-gray-700 transition-all group">
                        <div>
                            <div class="bg-gray-900 rounded-xl p-4 flex items-center justify-center border border-gray-850 h-48 mb-4 overflow-hidden">
                                <img src="<?= $item['foto_url'] ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <h3 class="text-lg font-bold text-gray-200 line-clamp-2 mb-2 group-hover:text-white"><?= htmlspecialchars($item['naam']) ?></h3>
                        </div>
                        <div class="mt-4">
                            <p class="text-xl font-black text-blue-400 mb-4">€<?= number_format($item['prijs'], 2, ',', '.') ?></p>
                            <a href="home.php?id=<?= $item['id'] ?>" class="w-full block bg-gray-900 hover:bg-gray-850 border border-gray-700 text-gray-200 hover:text-white font-medium py-2.5 px-4 rounded-xl text-center text-sm transition-all">
                                Bekijk Product
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>

</body>

</html>