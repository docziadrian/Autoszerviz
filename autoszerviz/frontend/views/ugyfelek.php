<?php
$hiba = "";
$siker = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mentes_gomb'])) {
    if ($muvelet == 'hozzaadas') {
        $adat = array(
            'nev' => $_POST['nev'],
            'telefon' => $_POST['telefon'],
            'email' => $_POST['email']
        );
        $valasz = api_kuld($alap_url . 'ugyfelek', 'POST', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'sikeresen') !== false) {
            header("Location: index.php?oldal=ugyfelek&uzenet=siker_hozzaadas");
            exit;
        } else {
            $hiba = "Nem sikerült az ügyfél hozzáadása.";
        }
    } elseif ($muvelet == 'szerkesztes' && $id) {
        $adat = array(
            'id' => $id,
            'nev' => $_POST['nev'],
            'telefon' => $_POST['telefon'],
            'email' => $_POST['email']
        );
        $valasz = api_kuld($alap_url . 'ugyfelek', 'PUT', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'sikeresen') !== false) {
            header("Location: index.php?oldal=ugyfelek&uzenet=siker_szerkesztes");
            exit;
        } else {
            $hiba = "Nem sikerült az ügyfél módosítása.";
        }
    }
}

if ($muvelet == 'torles' && $id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['megerosites'])) {
        $adat = array('id' => $id);
        $valasz = api_kuld($alap_url . 'ugyfelek', 'DELETE', $adat);
        header("Location: index.php?oldal=ugyfelek&uzenet=siker_torles");
        exit;
    }
}

if (isset($_GET['uzenet'])) {
    if ($_GET['uzenet'] == 'siker_hozzaadas') $siker = "Ügyfél sikeresen hozzáadva!";
    if ($_GET['uzenet'] == 'siker_szerkesztes') $siker = "Ügyfél sikeresen módosítva!";
    if ($_GET['uzenet'] == 'siker_torles') $siker = "Ügyfél sikeresen törölve!";
}
?>

<h2>Ügyfelek kezelése</h2>

<?php if ($hiba): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($hiba) ?></div>
<?php endif; ?>
<?php if ($siker): ?>
    <div class="alert alert-success"><?= htmlspecialchars($siker) ?></div>
<?php endif; ?>

<?php if ($muvelet == 'lista'): ?>
    <?php $ugyfelek = api_kuld($alap_url . 'ugyfelek', 'GET'); ?>
    <a href="index.php?oldal=ugyfelek&muvelet=hozzaadas" class="btn btn-primary mb-3">Új ügyfél hozzáadása</a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ugyfelek) && is_array($ugyfelek)): ?>
                <?php foreach ($ugyfelek as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td><?= htmlspecialchars($u['nev']) ?></td>
                        <td><?= htmlspecialchars($u['telefon']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <a href="index.php?oldal=ugyfelek&muvelet=szerkesztes&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Szerkesztés</a>
                            <a href="index.php?oldal=ugyfelek&muvelet=torles&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger">Törlés</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nincs megjeleníthető ügyfél.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($muvelet == 'hozzaadas' || $muvelet == 'szerkesztes'): ?>
    <?php
    $aktualis_nev = "";
    $aktualis_telefon = "";
    $aktualis_email = "";
    
    if ($muvelet == 'szerkesztes' && $id) {
        $ugyfel_adat = api_kuld($alap_url . 'ugyfelek&id=' . $id, 'GET');
        if (isset($ugyfel_adat['nev'])) {
            $aktualis_nev = $ugyfel_adat['nev'];
            $aktualis_telefon = $ugyfel_adat['telefon'];
            $aktualis_email = $ugyfel_adat['email'];
        }
    }
    ?>
    <div class="card w-50">
        <div class="card-body">
            <h4 class="card-title"><?= $muvelet == 'hozzaadas' ? 'Új ügyfél' : 'Ügyfél szerkesztése' ?></h4>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Név</label>
                    <input type="text" name="nev" class="form-control" value="<?= htmlspecialchars($aktualis_nev) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefonszám</label>
                    <input type="text" name="telefon" class="form-control" value="<?= htmlspecialchars($aktualis_telefon) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email cím</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($aktualis_email) ?>" required>
                </div>
                <button type="submit" name="mentes_gomb" class="btn btn-success">Mentés</button>
                <a href="index.php?oldal=ugyfelek" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>

<?php elseif ($muvelet == 'torles' && $id): ?>
    <div class="card w-50 border-danger">
        <div class="card-body text-center">
            <h4 class="text-danger">Biztosan törölni szeretnéd az ügyfelet?</h4>
            <p>Ez a művelet nem vonható vissza!</p>
            <form method="post" action="">
                <button type="submit" name="megerosites" class="btn btn-danger">Igen, törlés</button>
                <a href="index.php?oldal=ugyfelek" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>
<?php endif; ?>
