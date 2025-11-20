<?php
// Laad alle helpers en zorg dat alleen admins hier kunnen komen
require_once __DIR__ . '/../includes/init.php';

require_admin();

$errors = [];
// Open een herbruikbare databaseverbinding voor alle queries in dit script
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valideer de invoer van het formulier
    $errors = validate_user($_POST);

    // Check of gebruikersnaam al bestaat
    if (empty($errors['username'])) {
        $checkUsername = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $checkUsername->execute(['username' => $_POST['username']]);
        if ($checkUsername->fetchColumn() > 0) {
            $errors['username'] = 'Deze gebruikersnaam bestaat al.';
        }
    }

    // Check op bestaand emailadres
    if (empty($errors['email'])) {
        $checkEmail = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $checkEmail->execute(['email' => $_POST['email']]);
        if ($checkEmail->fetchColumn() > 0) {
            $errors['email'] = 'Deze e-mail bestaat al.';
        }
    }

    if (empty($errors)) {
        // Voeg de nieuwe gebruiker toe met een gehasht wachtwoord
        $statement = $pdo->prepare('INSERT INTO users (first_name, insertion, last_name, birth_date, email, username, password_hash, role, is_active)
            VALUES (:first_name, :insertion, :last_name, :birth_date, :email, :username, :password_hash, :role, :is_active)');

        $statement->execute([
            'first_name' => trim($_POST['first_name']),
            'insertion' => trim($_POST['insertion'] ?? ''),
            'last_name' => trim($_POST['last_name']),
            'birth_date' => $_POST['birth_date'],
            'email' => trim($_POST['email']),
            'username' => trim($_POST['username']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        clear_old_input();
        set_flash('Gebruiker succesvol toegevoegd.');
        header('Location: dashboard.php');
        exit;
    }

    // Bewaar de ingevulde waarden behalve het wachtwoord
    $formData = $_POST;
    unset($formData['password']);
    save_old_input($formData);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruiker toevoegen - <?php echo esc(APP_NAME); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="top-bar">
    <h1>Nieuwe gebruiker</h1>
    <nav>
        <a class="btn btn-secondary" href="dashboard.php">Terug naar overzicht</a>
    </nav>
</header>

<main class="container">
    <!-- Formulier om een volledig nieuw account aan te maken -->
    <form method="post" class="card">
        <div class="form-grid">
            <!-- Kolom 1: persoonlijke gegevens -->
            <div class="form-group">
                <label for="first_name">Voornaam</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo old('first_name'); ?>" required>
                <?php if (!empty($errors['first_name'])): ?><small class="error"><?php echo esc($errors['first_name']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="insertion">Tussenvoegsel</label>
                <input type="text" id="insertion" name="insertion" value="<?php echo old('insertion'); ?>" placeholder="bijv. van">
            </div>
            <div class="form-group">
                <label for="last_name">Achternaam</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo old('last_name'); ?>" required>
                <?php if (!empty($errors['last_name'])): ?><small class="error"><?php echo esc($errors['last_name']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="birth_date">Geboortedatum</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo old('birth_date'); ?>" required>
                <?php if (!empty($errors['birth_date'])): ?><small class="error"><?php echo esc($errors['birth_date']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" value="<?php echo old('email'); ?>" required>
                <?php if (!empty($errors['email'])): ?><small class="error"><?php echo esc($errors['email']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="role">Accountrol</label>
                <select id="role" name="role" required>
                    <option value="">-- Kies rol --</option>
                    <option value="user" <?php echo old('role') === 'user' ? 'selected' : ''; ?>>Gebruiker</option>
                    <option value="admin" <?php echo old('role') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <?php if (!empty($errors['role'])): ?><small class="error"><?php echo esc($errors['role']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" value="<?php echo old('username'); ?>" required>
                <?php if (!empty($errors['username'])): ?><small class="error"><?php echo esc($errors['username']); ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($errors['password'])): ?><small class="error"><?php echo esc($errors['password']); ?></small><?php endif; ?>
            </div>
        </div>

        <!-- Checkbox om het account direct actief te maken -->
        <label class="checkbox">
            <input type="checkbox" name="is_active" value="1" <?php echo old('is_active', '1') === '1' ? 'checked' : ''; ?>>
            Account is actief
        </label>

        <!-- Call-to-action knoppen voor opslaan of annuleren -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a class="btn btn-secondary" href="dashboard.php">Annuleren</a>
        </div>
    </form>
</main>
</body>
</html>
<?php clear_old_input(); ?>
