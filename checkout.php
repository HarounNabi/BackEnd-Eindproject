<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header("Location: home.php");
    exit();
}

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle_theme') {
    $_SESSION['theme'] = ($_SESSION['theme'] == 'dark') ? 'light' : 'dark';
    header("Location: checkout.php");
    exit();
}

$totaal = 0;
$items_om_te_tonen = [];

foreach ($_SESSION['cart'] as $id => $aantal) {
    $query = $pdo->prepare("SELECT * FROM items WHERE id = :id");
    $query->execute([':id' => $id]);
    $product = $query->fetch();
    if ($product) {
        $subtotaal = $product['prijs'] * $aantal;
        $totaal += $subtotaal;
        $items_om_te_tonen[] = [
            'product' => $product,
            'aantal' => $aantal,
            'subtotaal' => $subtotaal
        ];
    }
}

if (isset($_POST['plaats_bestelling'])) {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $straatnaam = $_POST['straatnaam'];
    $huisnummer = $_POST['huisnummer'];
    $postcode = $_POST['postcode'];
    $woonplaats = $_POST['woonplaats'];
    $betaalmethode = $_POST['betaalmethode'];

    $insertBestelling = $pdo->prepare("INSERT INTO bestellingen (gebruikersnaam, voornaam, achternaam, telefoonnummer, email, straatnaam, huisnummer, postcode, woonplaats, betaalmethode, totaal_bedrag) VALUES (:gebruikersnaam, :voornaam, :achternaam, :telefoonnummer, :email, :straatnaam, :huisnummer, :postcode, :woonplaats, :betaalmethode, :totaal_bedrag)");

    $insertBestelling->execute([
        ':gebruikersnaam' => $_SESSION['user'],
        ':voornaam' => $voornaam,
        ':achternaam' => $achternaam,
        ':telefoonnummer' => $telefoonnummer,
        ':email' => $email,
        ':straatnaam' => $straatnaam,
        ':huisnummer' => $huisnummer,
        ':postcode' => $postcode,
        ':woonplaats' => $woonplaats,
        ':betaalmethode' => $betaalmethode,
        ':totaal_bedrag' => $totaal
    ]);

    $bestelling_id = $pdo->lastInsertId();

    foreach ($items_om_te_tonen as $item) {
        $insertItem = $pdo->prepare("INSERT INTO bestelling_items (bestelling_id, item_id, aantal, prijs) VALUES (:bestelling_id, :item_id, :aantal, :prijs)");
        $insertItem->execute([
            ':bestelling_id' => $bestelling_id,
            ':item_id' => $item['product']['id'],
            ':aantal' => $item['aantal'],
            ':prijs' => $item['product']['prijs']
        ]);
    }

    unset($_SESSION['cart']);
    $_SESSION['bestelling_afgerond'] = true;
    header("Location: checkout.php?status=success");
    exit();
}

$suffix = '_' . $_SESSION['theme'];
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Afrekenen</title>
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
                <a href="checkout.php?action=toggle_theme" class="cursor-pointer text-lg p-2 rounded-xl hover<?= $suffix ?> transition-colors select-none scale-185">
                    ☯︎
                </a>
            </nav>
        </div>
    </header>

    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="max-w-2xl mx-auto text-center bg-card<?= $suffix ?> border border<?= $suffix ?> rounded-2xl p-12 shadow-xl">
                <div class="w-16 h-16 bg-emerald-600 text-white rounded-full flex items-center justify-center text-3xl mx-auto mb-6 font-bold">✓</div>
                <h2 class="text-3xl font-black mb-4">Bedankt voor je bestelling!</h2>
                <p class="text-muted<?= $suffix ?> mb-8">We gaan direct aan de slag met het inpakken van je hardware gear.</p>
                <a href="home.php" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-600/20">Terug naar winkel</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7 bg-card<?= $suffix ?> border border<?= $suffix ?> rounded-2xl p-8 shadow-xl">
                    <h2 class="text-2xl font-bold mb-6">Verzendgegevens</h2>
                    <form action="checkout.php" method="POST" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Voornaam</label>
                                <input type="text" name="voornaam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Achternaam</label>
                                <input type="text" name="achternaam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Telefoonnummer</label>
                                <input type="tel" name="telefoonnummer" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">E-mailadres</label>
                                <input type="email" name="email" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Straatnaam</label>
                                <input type="text" name="straatnaam" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Huisnummer</label>
                                <input type="text" name="huisnummer" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Postcode</label>
                                <input type="text" name="postcode" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-2">Woonplaats</label>
                                <input type="text" name="woonplaats" required class="w-full bg-main<?= $suffix ?> border border<?= $suffix ?> rounded-xl px-4 py-3 text-main<?= $suffix ?> focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted<?= $suffix ?> mb-3">Betaalmethode</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center gap-3 bg-main<?= $suffix ?> border border<?= $suffix ?> p-4 rounded-xl cursor-pointer select-none">
                                    <input type="radio" name="betaalmethode" value="iDEAL" checked class="w-4 h-4 text-blue-600 focus:ring-0">
                                    <span class="text-sm font-medium">iDEAL</span>
                                </label>
                                <label class="flex items-center gap-3 bg-main<?= $suffix ?> border border<?= $suffix ?> p-4 rounded-xl cursor-pointer select-none">
                                    <input type="radio" name="betaalmethode" value="PayPal" class="w-4 h-4 text-blue-600 focus:ring-0">
                                    <span class="text-sm font-medium">PayPal</span>
                                </label>
                                <label class="flex items-center gap-3 bg-main<?= $suffix ?> border border<?= $suffix ?> p-4 rounded-xl cursor-pointer">
                                    <input type="radio" name="betaalmethode" value="CreditCard" class="w-4 h-4 text-blue-600 focus:ring-0">
                                    <span class="text-sm font-medium">CreditCard</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" name="plaats_bestelling" class="w-full cursor-pointer bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-blue-600/10 transition-all tracking-wide text-base mt-4">
                            Bestelling Bestätigen (€<?= number_format($totaal, 2, ',', '.') ?>)
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-5 bg-card<?= $suffix ?> border border<?= $suffix ?> rounded-2xl p-6 shadow-xl space-y-6">
                    <h2 class="text-xl font-bold border-b border<?= $suffix ?> pb-4">Overzicht</h2>
                    <div class="divide-y border<?= $suffix ?> overflow-hidden">
                        <?php foreach ($items_om_te_tonen as $item): ?>
                            <div class="py-4 flex justify-between gap-4 first:pt-0 last:pb-0">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold tracking-tight"><?= htmlspecialchars($item['product']['naam']) ?></span>
                                    <span class="text-xs text-muted<?= $suffix ?>">Aantal: <?= $item['aantal'] ?></span>
                                </div>
                                <span class="text-sm font-bold text-blue-400 shrink-0">€<?= number_format($item['subtotaal'], 2, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="bg-main<?= $suffix ?> p-4 rounded-xl border border<?= $suffix ?> flex justify-between items-center">
                        <span class="text-sm font-medium text-muted<?= $suffix ?>">Totaal te betalen:</span>
                        <span class="text-2xl font-black text-blue-500 font-mono">€<?= number_format($totaal, 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-card<?= $suffix ?> border-t border<?= $suffix ?> py-6 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted<?= $suffix ?>">
            <p>&copy; <?= date("Y"); ?> TechShop. Alle rechten voorbehouden.</p>
        </div>
    </footer>

</body>

</html>