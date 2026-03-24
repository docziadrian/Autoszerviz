<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autószerviz Időpontfoglaló</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?oldal=foglalasok">Autószerviz</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigacio" aria-controls="navigacio" aria-expanded="false" aria-label="Navigáció">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navigacio">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link <?= $oldal == 'foglalasok' ? 'active' : '' ?>" href="index.php?oldal=foglalasok">Foglalások</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $oldal == 'ugyfelek' ? 'active' : '' ?>" href="index.php?oldal=ugyfelek">Ügyfelek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $oldal == 'szolgaltatasok' ? 'active' : '' ?>" href="index.php?oldal=szolgaltatasok">Szolgáltatások</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <?php require_once $tartalom_fajl; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
