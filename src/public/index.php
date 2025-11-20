<?php
// Laad alle benodigde hulpfuncties en start de sessie
require_once __DIR__ . '/../includes/init.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
// Haal eventueel eerder ingestelde flashmeldingen op voor feedback op het loginformulier
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valideer het loginformulier
    $errors = validate_login($_POST);

    if (empty($errors)) {
        // Probeer in te loggen met de ingevulde waarden
        $loginError = attempt_login($_POST['username'], $_POST['password']);

        if ($loginError === null) {
            clear_old_input();
            header('Location: dashboard.php');
            exit;
        }

        $errors['general'] = $loginError;
    }

    // Bewaar ingevulde data zodat het formulier opnieuw gevuld kan worden
    save_old_input([
        'username' => $_POST['username'] ?? '',
    ]);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen - <?php echo esc(APP_NAME); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container narrow">
    <h1 class="text-center">Login</h1>

    <!-- Algemene foutmelding tonen wanneer gebruikersnaam/wachtwoord niet matchen -->
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?php echo esc($errors['general']); ?></div>
    <?php endif; ?>

    <!-- Toont bijvoorbeeld de melding dat je bent uitgelogd -->
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo esc($flash['type']); ?>"><?php echo esc($flash['message']); ?></div>
    <?php endif; ?>

    <form method="post" class="card">
        <div class="form-group">
            <label for="username">Gebruikersnaam</label>
            <input type="text" name="username" id="username" value="<?php echo old('username'); ?>" required>
            <?php if (!empty($errors['username'])): ?>
                <small class="error"><?php echo esc($errors['username']); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Wachtwoord</label>
            <input type="password" name="password" id="password" required>
            <?php if (!empty($errors['password'])): ?>
                <small class="error"><?php echo esc($errors['password']); ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
<?php clear_old_input(); ?>
