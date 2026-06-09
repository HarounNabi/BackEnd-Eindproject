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
<html lang="nl" class="h-full bg-gray-900 text-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Afrekenen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="flex flex-col min-h-screen justify-center items-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="w-full max-w-xl space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-white tracking-wider">TECH<span class="text-blue-500">SHOP</span></h1>
        </div>

        <?php if ($error != ''): ?>
            <div class="p-4 rounded-xl bg-red-950/50 border border-red-800 text-red-400 text-sm text-center"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
            <div class="p-4 rounded-xl bg-green-950/50 border border-green-800 text-green-400 text-sm text-center">
                <?= $success ?><br>
                <a href="home.php" class="text-white underline font-bold block mt-2">Terug naar de winkel</a>
            </div>
        <?php else: ?>

            <div class="bg-gray-950 p-8 rounded-2xl border border-gray-800 shadow-2xl">
                <h2 class="text-2xl font-bold mb-6 text-center text-white">Gegevens Invullen</h2>

                <form action="checkout.php" method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Voornaam:</label>
                            <input type="text" name="voornaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Achternaam:</label>
                            <input type="text" name="achternaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Telefoonnummer:</label>
                            <input type="text" name="nummer" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">E-mailadres:</label>
                            <input type="email" name="email" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-400 mb-1">Straatnaam:</label>
                            <input type="text" name="straatnaam" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Huisnummer:</label>
                            <input type="text" name="huisnummer" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Postcode:</label>
                            <input type="text" name="postcode" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Woonplaats:</label>
                            <input type="text" name="woonplaats" required class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Betaalmethode:</label>
                        <select name="betaalmethode" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white">
                            <option value="iDEAL">iDEAL | Wero</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Creditcard">Creditcard</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-800 flex justify-between items-center">
                        <p class="text-xl font-bold text-white">Totaal: €<?= number_format($totaal, 2, ',', '.') ?></p>
                        <button type="submit" name="plaats_bestelling" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition-all cursor-pointer">
                            Bestelling plaatsen
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>