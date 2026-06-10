<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
    header("Location: inlog.php");
    exit();
}

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    header("Location: admin.php?tab=producten");
    exit();
}

if (isset($_POST['add_product'])) {
    $stmt = $pdo->prepare("INSERT INTO items (naam, prijs, foto_url, rating) VALUES (:naam, :prijs, :foto_url, :rating)");
    $stmt->execute([
        ':naam' => $_POST['naam'],
        ':prijs' => $_POST['prijs'],
        ':foto' => $_POST['foto_url'],
        ':rating' => $_POST['rating']
    ]);
    header("Location: admin.php?tab=toevoegen");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'producten';
$bestellingen = ($tab === 'bestellingen') ? $pdo->query("SELECT * FROM bestellingen ORDER BY id DESC")->fetchAll() : [];
$producten_lijst = ($tab === 'producten') ? $pdo->query("SELECT * FROM items ORDER BY id DESC")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full <?= $theme === 'light' ? '' : 'dark' ?>">
<head>
    <meta charset="UTF-8">
    <title>TechShop - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="<?= $theme === 'light' ? 'bg-gray-50 text-gray-900' : 'bg-gray-900 text-gray-100' ?> p-8 transition-colors duration-200">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex justify-between items-center border-b pb-4 <?= $theme === 'light' ? 'border-gray-200' : 'border-gray-800' ?>">
            <div><h1 class="text-3xl font-extrabold">Admin Panel</h1><p class="text-sm opacity-70">Welkom terug, Admin</p></div>
            <div class="flex gap-4">
                <a href="home.php" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm">Naar Webshop</a>
            </div>
        </div>

        <div class="flex gap-4 border-b pb-2 <?= $theme === 'light' ? 'border-gray-200' : 'border-gray-800' ?>">
            <a href="admin.php?tab=producten" class="px-4 py-2 rounded-lg font-semibold text-sm <?= $tab === 'producten' ? 'bg-blue-600 text-white' : ($theme === 'light' ? 'bg-gray-200' : 'bg-gray-800') ?>">Producten Beheren</a>
            <a href="admin.php?tab=toevoegen" class="px-4 py-2 rounded-lg font-semibold text-sm <?= $tab === 'toevoegen' ? 'bg-blue-600 text-white' : ($theme === 'light' ? 'bg-gray-200' : 'bg-gray-800') ?>">Product Toevoegen</a>
            <a href="admin.php?tab=bestellingen" class="px-4 py-2 rounded-lg font-semibold text-sm <?= $tab === 'bestellingen' ? 'bg-blue-600 text-white' : ($theme === 'light' ? 'bg-gray-200' : 'bg-gray-800') ?>">Bestellingen Bekijken</a>
        </div>

        <?php if ($tab === 'producten'): ?>
            <div class="p-6 rounded-2xl border <?= $theme === 'light' ? 'bg-white border-gray-200' : 'bg-gray-950 border-gray-800' ?>">
                <table class="w-full text-left text-sm">
                    <thead><tr class="opacity-70 border-b <?= $theme === 'light' ? 'border-gray-200' : 'border-gray-800' ?>"><th class="pb-2">Foto</th><th class="pb-2">Naam</th><th class="pb-2 text-right">Prijs</th><th class="pb-2 text-center">Actie</th></tr></thead>
                    <tbody>
                        <?php foreach ($producten_lijst as $p): ?>
                            <tr class="border-b <?= $theme === 'light' ? 'border-gray-100' : 'border-gray-800/50' ?>">
                                <td class="py-2"><img src="<?= $p['foto_url'] ?>" class="h-10 w-10 object-contain rounded bg-white p-0.5"></td>
                                <td class="py-2 font-medium"><?= htmlspecialchars($p['naam']) ?></td>
                                <td class="py-2 text-right">€<?= number_format($p['prijs'], 2, ',', '.') ?></td>
                                <td class="py-2 text-center"><a href="admin.php?action=delete&id=<?= $p['id'] ?>" class="text-red-500">Verwijder</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($tab === 'toevoegen'): ?>
            <div class="p-8 rounded-2xl border <?= $theme === 'light' ? 'bg-white border-gray-200' : 'bg-gray-950 border-gray-800' ?> max-w-lg">
                <form method="POST" class="space-y-4">
                    <input type="text" name="naam" placeholder="Naam" required class="w-full p-3 rounded-xl border <?= $theme === 'light' ? 'bg-gray-50 border-gray-200' : 'bg-gray-900 border-gray-700' ?>">
                    <input type="number" step="0.01" name="prijs" placeholder="Prijs" required class="w-full p-3 rounded-xl border <?= $theme === 'light' ? 'bg-gray-50 border-gray-200' : 'bg-gray-900 border-gray-700' ?>">
                    <input type="text" name="foto_url" placeholder="Foto URL" required class="w-full p-3 rounded-xl border <?= $theme === 'light' ? 'bg-gray-50 border-gray-200' : 'bg-gray-900 border-gray-700' ?>">
                    <input type="number" min="1" max="5" name="rating" placeholder="Rating (1-5)" required class="w-full p-3 rounded-xl border <?= $theme === 'light' ? 'bg-gray-50 border-gray-200' : 'bg-gray-900 border-gray-700' ?>">
                    <button type="submit" name="add_product" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">Toevoegen</button>
                </form>
            </div>
        <?php elseif ($tab === 'bestellingen'): ?>
            <div class="space-y-6">
                <?php foreach ($bestellingen as $b): ?>
                    <div class="p-6 rounded-2xl border <?= $theme === 'light' ? 'bg-white border-gray-200' : 'bg-gray-950 border-gray-800' ?>">
                        <h2 class="text-lg font-bold text-blue-500">Bestelling #<?= $b['id'] ?></h2>
                        <p class="text-sm">Klant: <?= htmlspecialchars($b['voornaam'].' '.$b['achternaam']) ?></p>
                        <p class="text-sm">Adres: <?= htmlspecialchars($b['straatnaam'].' '.$b['huisnummer'].', '.$b['postcode'].' '.$b['woonplaats']) ?></p>
                        <p class="text-sm">Email: <?= htmlspecialchars($b['email']) ?></p>
                        <p class="text-sm">Nummer: <?= htmlspecialchars($b['telefoonnummer']) ?></p>
                        <p class="text-sm">Methode: <?= htmlspecialchars($b['betaalmethode']) ?></p>
                        <p class="text-sm font-bold mt-2">Totaal: €<?= number_format($b['totaal_bedrag'], 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>