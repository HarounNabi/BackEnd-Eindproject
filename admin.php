<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
    header("Location: inlog.php");
    exit();
}

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle_theme') {
    $_SESSION['theme'] = ($_SESSION['theme'] == 'dark') ? 'light' : 'dark';
    $current_tab = isset($_GET['tab']) ? '&tab=' . $_GET['tab'] : '';
    header("Location: admin.php?1" . $current_tab);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    $stmt->execute([':id' => intval($_GET['id'])]);
    header("Location: admin.php?tab=producten");
    exit();
}

if (isset($_POST['add_product'])) {
    $stmt = $pdo->prepare("INSERT INTO items (naam, prijs, foto_url, rating) VALUES (:naam, :prijs, :foto_url, :rating)");
    $stmt->execute([
        ':naam' => $_POST['naam'],
        ':prijs' => $_POST['prijs'],
        ':foto_url' => $_POST['foto_url'],
        ':rating' => $_POST['rating']
    ]);
    header("Location: admin.php?tab=producten");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'producten';
$bestellingen = ($tab === 'bestellingen') ? $pdo->query("SELECT * FROM bestellingen ORDER BY id DESC")->fetchAll() : [];
$producten_lijst = ($tab === 'producten') ? $pdo->query("SELECT * FROM items ORDER BY id DESC")->fetchAll() : [];

$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Admin Panel</title>
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
                <a href="home.php" class="text-sm font-medium text-main<?= $suffix ?> hover:opacity-80 transition-colors">Producten</a>
                <a href="admin.php?action=toggle_theme&tab=<?= $tab ?>" class="cursor-pointer text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-185">
                    ☯︎
                </a>
                <a href="loguit.php" class="text-sm font-medium text-red-400 hover:text-red-600 transition-colors">Uitloggen</a>
            </nav>
        </div>
    </header>

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16">

        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-amber-400">Admin Management Panel</h2>
                <p class="text-sm text-muted<?= $suffix ?> mt-1">Welkom terug.</p>
            </div>
            <a href="home.php" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-5 rounded-xl shadow-md text-sm transition-all">
                Naar Webshop
            </a>
        </div>

        <div class="flex flex-wrap gap-3 border-b border<?= $suffix ?> pb-4 mb-8">
            <a href="admin.php?tab=producten" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all <?= $tab === 'producten' ? 'bg-blue-600 text-white' : 'bg-card' . $suffix . ' border border' . $suffix . ' hover' . $suffix ?>">
                🛠 Producten Beheren
            </a>
            <a href="admin.php?tab=toevoegen" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all <?= $tab === 'toevoegen' ? 'bg-blue-600 text-white' : 'bg-card' . $suffix . ' border border' . $suffix . ' hover' . $suffix ?>">
                + Product Toevoegen
            </a>
            <a href="admin.php?tab=bestellingen" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all <?= $tab === 'bestellingen' ? 'bg-blue-600 text-white' : 'bg-card' . $suffix . ' border border' . $suffix . ' hover' . $suffix ?>">
                🗎 Bestellingen Bekijken
            </a>
        </div>

        <?php if ($tab === 'producten'): ?>
            <div class="bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> shadow-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-main<?= $suffix ?> border-b border<?= $suffix ?>">
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Foto</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Productnaam</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> text-right">Verkoopprijs</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> text-center">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border<?= $suffix ?>">
                        <?php foreach ($producten_lijst as $p): ?>
                            <tr class="hover<?= $suffix ?> transition-colors">
                                <td class="p-4">
                                    <div class="h-12 w-12 rounded-lg bg-transparent flex items-center justify-center overflow-hidden">
                                        <img src="<?= htmlspecialchars($p['foto_url']) ?>" class="max-h-full max-w-full object-contain">
                                    </div>
                                </td>
                                <td class="p-4 text-sm font-bold tracking-tight"><?= htmlspecialchars($p['naam']) ?></td>
                                <td class="p-4 text-sm font-bold font-mono text-blue-500 text-right">€<?= number_format($p['prijs'], 2, ',', '.') ?></td>
                                <td class="p-4 text-sm text-center">
                                    <a href="admin.php?action=delete&id=<?= $p['id'] ?>" class="text-red-400 hover:text-red-300 font-semibold transition-colors" onclick="return confirm('Weet je zeker dat je dit item wilt verwijderen?')">Verwijder</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($tab === 'toevoegen'): ?>
            <div class="max-w-xl bg-card<?= $suffix ?> border border<?= $suffix ?> rounded-2xl p-8 shadow-xl">
                <h3 class="text-xl font-bold mb-6">Nieuw Product Aanmaken</h3>
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Productnaam</label>
                        <input type="text" name="naam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Prijs (€)</label>
                            <input type="number" step="0.01" name="prijs" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Beoordeling (1-5)</label>
                            <input type="number" min="1" max="5" step="0.1" name="rating" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Afbeelding direct link (URL)</label>
                        <input type="text" name="foto_url" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" name="add_product" class="w-full cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg transition-all tracking-wide text-sm pt-3">
                        Product Opslaan in Database
                    </button>
                </form>
            </div>

        <?php elseif ($tab === 'bestellingen'): ?>
            <?php if (empty($bestellingen)): ?>
                <div class="bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> p-10 text-center shadow-md">
                    <p class="text-muted<?= $suffix ?>">Er zijn op dit moment geen klantbestellingen aanwezig.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($bestellingen as $b): ?>
                        <details class="group bg-card<?= $suffix ?> rounded-2xl border border<?= $suffix ?> shadow-xl overflow-hidden transition-all duration-200">

                            <summary class="list-none cursor-pointer bg-main<?= $suffix ?> px-6 py-4 flex flex-col sm:flex-row justify-between gap-4 items-start sm:items-center select-none hover:opacity-90 transition-opacity">
                                <div class="flex items-center gap-4">
                                    <span class="text-amber-400 font-bold transition-transform duration-200 group-open:rotate-90">▶</span>
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Bestelling Referentie</span>
                                        <h3 class="text-lg font-black font-mono">#<?= $b['id'] ?></h3>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8 sm:text-right w-full sm:w-auto justify-between sm:justify-end">
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Klant Account</span>
                                        <p class="text-sm font-bold text-amber-400"><?= htmlspecialchars($b['gebruikersnaam']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?>">Totaal bedrag</span>
                                        <p class="text-base font-black text-blue-500 font-mono">€<?= number_format($b['totaal_bedrag'], 2, ',', '.') ?></p>
                                    </div>
                                </div>
                            </summary>

                            <div class="border-t border<?= $suffix ?>">
                                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border<?= $suffix ?>">
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted<?= $suffix ?> mb-3">Geadresseerde & Afleverlocatie</h4>
                                        <div class="text-sm space-y-1 font-medium">
                                            <p><?= htmlspecialchars($b['voornaam'] . ' ' . $b['achternaam']) ?></p>
                                            <p><?= htmlspecialchars($b['straatnaam'] . ' ' . $b['huisnummer']) ?></p>
                                            <p><?= htmlspecialchars($b['postcode'] . ' ' . $b['woonplaats']) ?></p>
                                            <p class="text-muted<?= $suffix ?> pt-2">📞 <?= htmlspecialchars($b['telefoonnummer']) ?></p>
                                            <p class="text-muted<?= $suffix ?>">✉️ <?= htmlspecialchars($b['email']) ?></p>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted<?= $suffix ?> mb-3">Transactie Details</h4>
                                        <div class="text-sm space-y-1 font-medium">
                                            <p>Methode: <span class="text-main<?= $suffix ?> font-bold"><?= htmlspecialchars($b['betaalmethode']) ?></span></p>
                                            <p class="text-sm text-muted<?= $suffix ?> pt-2">Status: <span class="text-emerald-400 font-bold">Betaald</span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 bg-main<?= $suffix ?>/30">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-muted<?= $suffix ?> mb-3">Inbegrepen Producten</h4>
                                    <div class="divide-y border<?= $suffix ?>">
                                        <?php
                                        $itemQuery = $pdo->prepare("SELECT bi.*, i.naam FROM bestelling_items bi JOIN items i ON bi.item_id = i.id WHERE bi.bestelling_id = :b_id");
                                        $itemQuery->execute([':b_id' => $b['id']]);
                                        $items = $itemQuery->fetchAll();

                                        foreach ($items as $item):
                                        ?>
                                            <div class="py-3 flex justify-between gap-4 first:pt-0 last:pb-0 text-sm">
                                                <div class="font-medium">
                                                    <span><?= htmlspecialchars($item['naam']) ?></span>
                                                    <span class="text-xs text-muted<?= $suffix ?> font-mono ml-2">x<?= $item['aantal'] ?></span>
                                                </div>
                                                <span class="font-bold text-blue-400 font-mono">€<?= number_format($item['prijs'] * $item['aantal'], 2, ',', '.') ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer class="bg-card<?= $suffix ?> border-t border<?= $suffix ?> py-6 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted<?= $suffix ?>">
            <p>&copy; <?= date("Y"); ?> TechShop - Intern Configuratiescherm.</p>
        </div>
    </footer>

</body>

</html>