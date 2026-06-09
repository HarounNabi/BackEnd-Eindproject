<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
    header("Location: inlog.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'producten';

$bestellingen = [];
$producten_lijst = [];

if ($tab === 'bestellingen') {
    $query = $pdo->query("SELECT * FROM bestellingen ORDER BY id DESC");
    $bestellingen = $query->fetchAll();
} else {
    $query = $pdo->query("SELECT * FROM items ORDER BY id DESC");
    $producten_lijst = $query->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-900 text-gray-100 p-8">

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex justify-between items-center border-b border-gray-800 pb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Admin Panel</h1>
                <p class="text-sm text-gray-400">Welkom terug, Admin</p>
            </div>
            <a href="home.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-sm transition-colors">
                Naar Webshop
            </a>
        </div>

        <div class="flex gap-4 border-b border-gray-800 pb-2">
            <a href="admin.php?tab=producten" class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors <?= $tab === 'producten' ? 'bg-blue-600 text-white' : 'bg-gray-950 text-gray-400 hover:text-white' ?>">
                Producten Beheren
            </a>
            <a href="admin.php?tab=bestellingen" class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors <?= $tab === 'bestellingen' ? 'bg-blue-600 text-white' : 'bg-gray-950 text-gray-400 hover:text-white' ?>">
                Bestellingen Bekijken
            </a>
        </div>

        <?php if ($tab === 'producten'): ?>
            <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800 space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Je Producten</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-800">
                                <th class="pb-2">Foto</th>
                                <th class="pb-2">Naam</th>
                                <th class="pb-2 text-right">Prijs</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-300 divide-y divide-gray-900">
                            <?php foreach ($producten_lijst as $p): ?>
                                <tr>
                                    <td class="py-2"><img src="<?= $p['foto_url'] ?>" class="h-10 w-10 object-contain rounded bg-white p-0.5"></td>
                                    <td class="py-2 font-medium text-white"><?= htmlspecialchars($p['naam']) ?></td>
                                    <td class="py-2 text-right font-mono">€<?= number_format($p['prijs'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'bestellingen'): ?>
            <?php if (empty($bestellingen)): ?>
                <p class="text-gray-400">Er zijn nog geen bestellingen geplaatst.</p>
            <?php else: ?>

                <div class="space-y-6">
                    <?php foreach ($bestellingen as $bestelling): ?>
                        <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800 space-y-4">

                            <div class="flex flex-col md:flex-row justify-between border-b border-gray-800 pb-4 gap-2">
                                <div>
                                    <h2 class="text-xl font-bold text-blue-400">Bestelling #<?= $bestelling['id'] ?></h2>
                                    <p class="text-sm text-gray-400">Klant: <?= htmlspecialchars($bestelling['voornaam']) . " " . htmlspecialchars($bestelling['achternaam']) ?> (Account: <?= htmlspecialchars($bestelling['gebruikersnaam']) ?>)</p>
                                </div>
                                <div class="text-left md:text-right text-sm text-gray-400">
                                    <p>Adres: <?= htmlspecialchars($bestelling['straatnaam']) . " " . htmlspecialchars($bestelling['huisnummer']) ?>, <?= htmlspecialchars($bestelling['postcode']) ?> <?= htmlspecialchars($bestelling['woonplaats']) ?></p>
                                    <p>Tel: <?= htmlspecialchars($bestelling['telefoonnummer']) ?> | E-mail: <?= htmlspecialchars($bestelling['email']) ?></p>
                                </div>
                            </div>

                            <?php
                            $itemsQuery = $pdo->prepare("
                                SELECT bestelling_items.*, items.naam 
                                FROM bestelling_items 
                                JOIN items ON bestelling_items.item_id = items.id 
                                WHERE bestelling_items.bestelling_id = :bestelling_id
                            ");
                            $itemsQuery->execute([':bestelling_id' => $bestelling['id']]);
                            $producten_in_bestelling = $itemsQuery->fetchAll();
                            ?>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="text-gray-500 border-b border-gray-800">
                                            <th class="pb-2">Product</th>
                                            <th class="pb-2 text-center">Aantal</th>
                                            <th class="pb-2 text-right">Prijs per stuk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-300 divide-y divide-gray-900">
                                        <?php foreach ($producten_in_bestelling as $p): ?>
                                            <tr>
                                                <td class="py-2"><?= htmlspecialchars($p['naam']) ?></td>
                                                <td class="py-2 text-center font-mono"><?= $p['aantal'] ?>x</td>
                                                <td class="py-2 text-right font-mono">€<?= number_format($p['prijs'], 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t border-gray-800 text-sm">
                                <p class="text-gray-400">Betaalmethode: <span class="text-gray-200 font-semibold"><?= htmlspecialchars($bestelling['betaalmethode']) ?></span></p>
                                <p class="text-lg font-bold text-white">Totaal betaald: €<?= number_format($bestelling['totaal_bedrag'], 2, ',', '.') ?></p>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        <?php endif; ?>

    </div>

</body>

</html>