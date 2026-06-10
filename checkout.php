<?php
session_start();
include_once 'db/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: inlog.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: home.php");
    exit();
}

$error = '';
$success = '';

$totaal = 0;
foreach ($_SESSION['cart'] as $id => $aantal) {
    $query = $pdo->prepare("SELECT prijs FROM items WHERE id = :id");
    $query->execute([':id' => $id]);
    $product = $query->fetch();
    if ($product) {
        $totaal += $product['prijs'] * $aantal;
    }
}

if (isset($_POST['plaats_bestelling'])) {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $nummer = $_POST['nummer'];
    $email = $_POST['email'];
    $straatnaam = $_POST['straatnaam'];
    $huisnummer = $_POST['huisnummer'];
    $postcode = $_POST['postcode'];
    $woonplaats = $_POST['woonplaats'];
    $betaalmethode = $_POST['betaalmethode'];

    if ($voornaam != '' && $achternaam != '' && $nummer != '' && $email != '' && $straatnaam != '' && $huisnummer != '' && $postcode != '' && $woonplaats != '') {

        $insert = $pdo->prepare("INSERT INTO bestellingen (gebruikersnaam, voornaam, achternaam, telefoonnummer, email, straatnaam, huisnummer, postcode, woonplaats, betaalmethode, totaal_bedrag) VALUES (:gebruikersnaam, :voornaam, :achternaam, :nummer, :email, :straatnaam, :huisnummer, :postcode, :woonplaats, :betaalmethode, :totaal)");

        $insert->execute([
            ':gebruikersnaam' => $_SESSION['user'],
            ':voornaam' => $voornaam,
            ':achternaam' => $achternaam,
            ':nummer' => $nummer,
            ':email' => $email,
            ':straatnaam' => $straatnaam,
            ':huisnummer' => $huisnummer,
            ':postcode' => $postcode,
            ':woonplaats' => $woonplaats,
            ':betaalmethode' => $betaalmethode,
            ':totaal' => $totaal
        ]);

        $bestelling_id = $pdo->lastInsertId();

        foreach ($_SESSION['cart'] as $id => $aantal) {
            $productQuery = $pdo->prepare("SELECT prijs FROM items WHERE id = :id");
            $productQuery->execute([':id' => $id]);
            $product = $productQuery->fetch();

            if ($product) {
                $insertItem = $pdo->prepare("INSERT INTO bestelling_items (bestelling_id, item_id, aantal, prijs) VALUES (:bestelling_id, :item_id, :aantal, :prijs)");
                $insertItem->execute([
                    ':bestelling_id' => $bestelling_id,
                    ':item_id' => $id,
                    ':aantal' => $aantal,
                    ':prijs' => $product['prijs']
                ]);
            }
        }

        unset($_SESSION['cart']);
        $success = "Je bestelling is succesvol geplaatst!";
    } else {
        $error = "Vul alle velden in.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Afrekenen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/css">
        :has(#theme-toggle-input:checked) .light-bg-white { background-color: #ffffff; }
        :has(#theme-toggle-input:checked) .light-bg-gray-50 { background-color: #f9fafb; }
        :has(#theme-toggle-input:checked) .light-text-gray-900 { color: #111827; }
        :has(#theme-toggle-input:checked) .light-text-gray-700 { color: #374151; }
        :has(#theme-toggle-input:checked) .light-border-gray-200 { border-color: #e5e7eb; }
        :has(#theme-toggle-input:checked) .light-text-black { color: #000000; }
        :has(#theme-toggle-input:checked) .light-hover-text-black:hover { color: #000000; }
        :has(#theme-toggle-input:checked) .light-hover-bg-gray-100:hover { background-color: #f3f4f6; }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 light-bg-gray-50 light-text-gray-900 transition-colors duration-200 min-h-screen">

    <input type="checkbox" id="theme-toggle-input" class="hidden">

    <header class="bg-gray-950 light-bg-white border-b border-gray-800 light-border-gray-200 fixed top-0 left-0 w-full z-50 transition-colors duration-200">
        <div class="w-full px-8 h-20 flex items-center justify-between">
            <a href="home.php" class="flex items-center shrink-0">
                <img src="logo.png" alt="TechShop Logo" class="h-14 w-auto object-contain">
            </a>
            <nav class="flex items-center gap-6">
                <a href="home.php" class="text-sm font-medium text-gray-200 light-text-gray-700 hover:text-white light-hover-text-black transition-colors">Producten</a>
                <label for="theme-toggle-input" class="cursor-pointer text-lg p-2 rounded-xl hover:bg-gray-900/50 light-hover-bg-gray-100 transition-colors select-none">🌓</label>
            </nav>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 pt-28 pb-12">
        <h1 class="text-3xl font-extrabold text-white light-text-gray-900 mb-8">Afrekenen</h1>

        <?php if ($error != ''): ?>
            <div class="p-4 mb-6 rounded-xl bg-red-950/30 border border-red-800 text-red-400 text-sm"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
            <div class="bg-gray-950 light-bg-white p-8 rounded-2xl border border-gray-800 light-border-gray-200 text-center">
                <h2 class="text-xl font-bold mb-2">Bedankt voor je bestelling!</h2>
                <p class="text-gray-400 light-text-gray-700 mb-6"><?= $success ?></p>
                <a href="home.php" class="bg-blue-600 text-white font-medium py-2 px-6 rounded-xl hover:bg-blue-500 transition-colors">Terug naar de winkel</a>
            </div>
        <?php else: ?>

            <form action="checkout.php" method="POST" class="bg-gray-950 light-bg-white p-6 md:p-8 rounded-2xl border border-gray-800 light-border-gray-200 shadow-xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Voornaam</label>
                        <input type="text" name="voornaam" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Achternaam</label>
                        <input type="text" name="achternaam" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Telefoonnummer</label>
                        <input type="text" name="nummer" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">E-mailadres</label>
                        <input type="email" name="email" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="col-span-3">
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Straatnaam</label>
                        <input type="text" name="straatnaam" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Nr.</label>
                        <input type="text" name="huisnummer" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Postcode</label>
                        <input type="text" name="postcode" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Woonplaats</label>
                        <input type="text" name="woonplaats" required class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-medium text-gray-400 light-text-gray-700 mb-1">Betaalmethode</label>
                    <select name="betaalmethode" class="w-full bg-gray-900 light-bg-gray-50 border border-gray-700 light-border-gray-200 rounded-xl p-3 text-white light-text-gray-900 outline-none focus:border-blue-500 transition-colors">
                        <option value="iDEAL">iDEAL | Wero</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Creditcard">Creditcard</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-800 light-border-gray-200">
                    <p class="text-xl font-bold text-white light-text-gray-900">Totaal: €<?= number_format($totaal, 2, ',', '.') ?></p>
                    <button type="submit" name="plaats_bestelling" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2.5 px-6 rounded-xl transition-all">
                        Bestellen
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </main>

</body>
</html>