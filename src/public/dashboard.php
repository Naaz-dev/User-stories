<?php
// Start alle basisfunctionaliteit en controleer of er een sessie actief is
require_once __DIR__ . '/../includes/init.php';

// Alleen ingelogde gebruikers mogen het dashboard zien
require_login();

// Ophalen van flashmeldingen en de huidige gebruiker om in de UI te tonen
$flash = get_flash();
$user = current_user();
$isAdmin = is_admin();

// Haal alle gebruikers op voor het overzicht, gesorteerd op voornaam
$pdo = get_pdo();
$statement = $pdo->query('SELECT id, first_name, insertion, last_name, birth_date, email, username, role, is_active FROM users ORDER BY first_name');
$users = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruikers - <?php echo esc(APP_NAME); ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/script.js"></script>
</head>
<body>
<header class="top-bar">
    <div>
        <h1>Personen</h1>
        <p>Welkom, <?php echo esc($user['name']); ?> (<?php echo esc($user['role']); ?>)</p>
    </div>
    <nav>
        <!-- Acties voor beheerders -->
        <?php if ($isAdmin): ?>
            <a class="btn" href="add_user.php">Voeg persoon toe</a>
        <?php endif; ?>
        <a class="btn btn-secondary" href="logout.php">Uitloggen</a>
    </nav>
</header>

<main class="container">
    <!-- Toont een eenmalig bericht, bijvoorbeeld na het aanmaken van een gebruiker -->
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo esc($flash['type']); ?>"><?php echo esc($flash['message']); ?></div>
    <?php endif; ?>

    <!-- Informeer reguliere gebruikers dat zij geen beheerrechten hebben -->
    <?php if (!$isAdmin): ?>
        <div class="alert alert-info">Je bent ingelogd als gewone gebruiker. Je kunt de gegevens bekijken, maar alleen beheerders mogen accounts wijzigen.</div>
    <?php endif; ?>

    <div class="table-wrapper">
        <!-- Overzichtstabel met alle accounts in het systeem -->
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Voornaam</th>
                <th>Tussenvoegsel</th>
                <th>Achternaam</th>
                <th>Geboortedatum</th>
                <th>Email</th>
                <th>Gebruikersnaam</th>
                <th>Rol</th>
                <th>Status</th>
                <?php if ($isAdmin): ?>
                    <th>Acties</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $person): ?>
                <tr>
                    <td><?php echo esc($person['id']); ?></td>
                    <td><?php echo esc($person['first_name']); ?></td>
                    <td><?php echo esc($person['insertion']); ?></td>
                    <td><?php echo esc($person['last_name']); ?></td>
                    <td><?php echo esc($person['birth_date']); ?></td>
                    <td><?php echo esc($person['email']); ?></td>
                    <td><?php echo esc($person['username']); ?></td>
                    <td><?php echo esc($person['role']); ?></td>
                    <td><?php echo (int)$person['is_active'] === 1 ? 'Actief' : 'Geblokkeerd'; ?></td>
                    <?php if ($isAdmin): ?>
                        <td class="actions">
                            <!-- Admins kunnen accounts aanpassen of verwijderen -->
                            <a class="btn btn-small" href="edit_user.php?id=<?php echo esc($person['id']); ?>">Wijzig</a>
                            <form method="post" action="delete_user.php" class="inline-form" data-confirm="Weet je zeker dat je deze gebruiker wilt verwijderen?">
                                <input type="hidden" name="id" value="<?php echo esc($person['id']); ?>">
                                <button type="submit" class="btn btn-danger btn-small">Verwijder</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
