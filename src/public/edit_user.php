<?php
// Laad alle helpers en controleer admin-rechten
require_once __DIR__ . '/../includes/init.php';

// Alleen beheerders mogen gebruikers aanpassen
require_admin();

// Zoek de gebruiker op basis van het aangeleverde ID (GET bij de eerste load, POST bij het submitten)
$pdo = get_pdo();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Zonder geldig ID sturen we terug naar het overzicht
if (!$id) {
    set_flash('Ongeldig gebruikers-ID.', 'error');
    header('Location: dashboard.php');
    exit;
}

// Haal de bestaande gegevens van deze gebruiker op om het formulier vooraf in te vullen
$statement = $pdo->prepare('SELECT id, first_name, insertion, last_name, birth_date, email, username, role, is_active FROM users WHERE id = :id');
$statement->execute(['id' => $id]);
$user = $statement->fetch();

// Bestaat de gebruiker niet meer? Laat een melding zien en ga terug
if (!$user) {
    set_flash('Gebruiker niet gevonden.', 'error');
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;
    $formData['id'] = $id; // nodig zodat validator weet dat het om een update gaat
    $errors = validate_user($formData, true);

    // Controleer of de nieuwe gebruikersnaam nog uniek is (behalve voor deze gebruiker zelf)
    if (empty($errors['username'])) {
        $checkUsername = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND id <> :id');
        $checkUsername->execute([
            'username' => $_POST['username'],
            'id' => $id,
        ]);
        if ($checkUsername->fetchColumn() > 0) {
            $errors['username'] = 'Deze gebruikersnaam bestaat al.';
        }
    }

    // Controleer hetzelfde voor het e-mailadres
    if (empty($errors['email'])) {
        $checkEmail = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id');
        $checkEmail->execute([
            'email' => $_POST['email'],
            'id' => $id,
        ]);
        if ($checkEmail->fetchColumn() > 0) {
            $errors['email'] = 'Deze e-mail bestaat al.';
        }
    }

    if (empty($errors)) {
        // Voer alle updates uit binnen een transactie zodat we kunnen rollbacken bij fouten
        $pdo->beginTransaction();

        try {
            // Update de basisgegevens van de gebruiker in één query
            $update = $pdo->prepare('UPDATE users SET first_name = :first_name, insertion = :insertion, last_name = :last_name, birth_date = :birth_date, email = :email, username = :username, role = :role, is_active = :is_active WHERE id = :id');
            $update->execute([
                'first_name' => trim($_POST['first_name']),
                'insertion' => trim($_POST['insertion'] ?? ''),
                'last_name' => trim($_POST['last_name']),
                'birth_date' => $_POST['birth_date'],
                'email' => trim($_POST['email']),
                'username' => trim($_POST['username']),
                'role' => $_POST['role'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'id' => $id,
            ]);

            if (!empty($_POST['password'])) {
                // Alleen als er een nieuw wachtwoord is ingevuld, vervang de hash
                $updatePassword = $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
                $updatePassword->execute([
                    'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'id' => $id,
                ]);
            }

            $pdo->commit();
        } catch (Throwable $throwable) {
            $pdo->rollBack();
            if (APP_DEBUG) {
                throw $throwable;
            }
            $errors['general'] = 'Bijwerken is mislukt, probeer het opnieuw.';
        }

        if (empty($errors)) {
            // Alles gelukt: toon een melding en ga terug naar het overzicht
            set_flash('Gebruiker bijgewerkt.');
            header('Location: dashboard.php');
            exit;
        }
    }

    // Bewaar de ingevulde waarden behalve het wachtwoord zodat het formulier opnieuw gevuld is
    $formData = $_POST;
    unset($formData['password']);
    save_old_input($formData);
} else {
    // Eerste bezoek: vul het formulier vooraf in met de bestaande waarden
    save_old_input($user);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruiker bewerken - <?php echo esc(APP_NAME); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="top-bar">
    <h1>Gebruiker aanpassen</h1>
    <nav>
        <a class="btn btn-secondary" href="dashboard.php">Terug naar overzicht</a>
    </nav>
</header>

<main class="container">
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?php echo esc($errors['general']); ?></div>
    <?php endif; ?>
    <!-- Formulier om bestaande accountgegevens te actualiseren -->
    <form method="post" class="card">
        <input type="hidden" name="id" value="<?php echo esc($id); ?>">
        <div class="form-grid">
            <!-- Persoonlijke gegevens -->
            <div class="form-group">
                <label for="first_name">Voornaam</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo old('first_name', $user['first_name']); ?>" required>
                <?php if (!empty($errors['first_name'])): ?><small class="error"><?php echo esc($errors['first_name']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="insertion">Tussenvoegsel</label>
                <input type="text" id="insertion" name="insertion" value="<?php echo old('insertion', $user['insertion']); ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Achternaam</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo old('last_name', $user['last_name']); ?>" required>
                <?php if (!empty($errors['last_name'])): ?><small class="error"><?php echo esc($errors['last_name']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="birth_date">Geboortedatum</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo old('birth_date', $user['birth_date']); ?>" required>
                <?php if (!empty($errors['birth_date'])): ?><small class="error"><?php echo esc($errors['birth_date']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" value="<?php echo old('email', $user['email']); ?>" required>
                <?php if (!empty($errors['email'])): ?><small class="error"><?php echo esc($errors['email']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="role">Accountrol</label>
                <select id="role" name="role" required>
                    <option value="user" <?php echo old('role', $user['role']) === 'user' ? 'selected' : ''; ?>>Gebruiker</option>
                    <option value="admin" <?php echo old('role', $user['role']) === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <?php if (!empty($errors['role'])): ?><small class="error"><?php echo esc($errors['role']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" value="<?php echo old('username', $user['username']); ?>" required>
                <?php if (!empty($errors['username'])): ?><small class="error"><?php echo esc($errors['username']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Nieuw wachtwoord</label>
                <input type="password" id="password" name="password" placeholder="Laat leeg om te behouden">
                <?php if (!empty($errors['password'])): ?><small class="error"><?php echo esc($errors['password']); ?></small><?php endif; ?>
            </div>
        </div>

        <!-- Keuze voor actief of geblokkeerd account -->
        <label class="checkbox">
            <input type="checkbox" name="is_active" value="1" <?php echo old('is_active', (string)$user['is_active']) === '1' ? 'checked' : ''; ?>>
            Account is actief
        </label>

        <!-- Opslaan of terug naar het overzicht -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a class="btn btn-secondary" href="dashboard.php">Annuleren</a>
        </div>
    </form>
</main>
</body>
</html>
<?php clear_old_input(); ?>
