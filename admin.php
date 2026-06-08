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
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="flex flex-col min-h-screen">

    <header class="bg-gray-950 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="home.php" class="flex items-center">
                <img src="logo.png" alt="TechShop Logo" class="h-12 w-auto object-contain">
            </a>
            <nav class="flex items-center gap-6">
                <a href="home.php" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">Naar de Webshop</a>
            </nav>
        </div>
    </header>

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800 shadow-xl h-fit">
                <h2 class="text-2xl font-bold mb-6 text-white tracking-wide">Nieuw Product Toevoegen</h2>

                <?php if ($message != ''): ?>
                    <div class="mb-6 p-4 rounded-xl bg-blue-950/50 border border-blue-800 text-blue-300 text-sm font-medium">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="admin.php" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Productnaam:</label>
                        <input type="text" name="naam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Prijs (€):</label>
                            <input type="number" name="prijs" step="0.01" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Rating (1-5):</label>
                            <input type="number" name="rating" step="0.1" min="1" max="5" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Afbeelding URL:</label>
                        <input type="url" name="foto_url" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <button type="submit" name="submit" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-600/10 transition-all text-center">
                        Product Opslaan
                    </button>
                </form>
            </div>

            <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800 shadow-xl flex flex-col h-[600px]">
                <h2 class="text-2xl font-bold mb-6 text-white tracking-wide">Huidig Assortiment</h2>
                <div class="overflow-y-auto grow rounded-xl border border-gray-800 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-900 sticky top-0 border-b border-gray-800">
                            <tr>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">ID</th>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Naam</th>
                                <th class="p-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Prijs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            <?php while ($item = $items_query->fetch()): ?>
                                <tr class="hover:bg-gray-900/40 transition-colors">
                                    <td class="p-4 text-sm font-mono text-gray-500">#<?= $item['id'] ?></td>
                                    <td class="p-4 text-sm font-medium text-gray-200"><?= htmlspecialchars($item['naam']) ?></td>
                                    <td class="p-4 text-sm font-semibold text-blue-400">€<?= number_format($item['prijs'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>

</html>