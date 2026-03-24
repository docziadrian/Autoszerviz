<?php
$hiba = "";
$siker = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mentes_gomb'])) {
    if ($muvelet == 'hozzaadas') {
        $adat = array(
            'ugyfel_id' => $_POST['ugyfel_id'],
            'szolgaltatas_id' => $_POST['szolgaltatas_id'],
            'idopont' => $_POST['idopont'],
            'allapot' => $_POST['allapot']
        );
        $valasz = api_kuld($alap_url . 'foglalasok', 'POST', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'letrehozva') !== false) {
            header("Location: index.php?oldal=foglalasok&uzenet=siker_hozzaadas");
            exit;
        } else {
            $hiba = "Nem sikerült a foglalás hozzáadása.";
        }
    } elseif ($muvelet == 'szerkesztes' && $id) {
        $adat = array(
            'id' => $id,
            'ugyfel_id' => $_POST['ugyfel_id'],
            'szolgaltatas_id' => $_POST['szolgaltatas_id'],
            'idopont' => $_POST['idopont'],
            'allapot' => $_POST['allapot']
        );
        $valasz = api_kuld($alap_url . 'foglalasok', 'PUT', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'frissitve') !== false) {
            header("Location: index.php?oldal=foglalasok&uzenet=siker_szerkesztes");
            exit;
        } else {
            $hiba = "Nem sikerült a foglalás módosítása.";
        }
    }
}

if ($muvelet == 'torles' && $id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['megerosites'])) {
        $adat = array('id' => $id);
        $valasz = api_kuld($alap_url . 'foglalasok', 'DELETE', $adat);
        header("Location: index.php?oldal=foglalasok&uzenet=siker_torles");
        exit;
    }
}

if (isset($_GET['uzenet'])) {
    if ($_GET['uzenet'] == 'siker_hozzaadas') $siker = "Foglalás sikeresen hozzáadva!";
    if ($_GET['uzenet'] == 'siker_szerkesztes') $siker = "Foglalás sikeresen módosítva!";
    if ($_GET['uzenet'] == 'siker_torles') $siker = "Foglalás sikeresen törölve!";
}
?>

<h2>Foglalások kezelése</h2>

<?php if ($hiba): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($hiba) ?></div>
<?php endif; ?>
<?php if ($siker): ?>
    <div class="alert alert-success"><?= htmlspecialchars($siker) ?></div>
<?php endif; ?>

<?php if ($muvelet == 'lista'): ?>
    <?php $foglalasok = api_kuld($alap_url . 'foglalasok', 'GET'); ?>
    <a href="index.php?oldal=foglalasok&muvelet=hozzaadas" class="btn btn-primary mb-3">Új foglalás hozzáadása</a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Ügyfél neve</th>
                <th>Szolgáltatás neve</th>
                <th>Időpont</th>
                <th>Állapot</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($foglalasok) && is_array($foglalasok)): ?>
                <?php foreach ($foglalasok as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['id']) ?></td>
                        <td><?= htmlspecialchars($f['ugyfel_nev'] ?? 'Ismeretlen') ?></td>
                        <td><?= htmlspecialchars($f['szolgaltatas_megnevezes'] ?? 'Ismeretlen') ?></td>
                        <td><?= htmlspecialchars($f['idopont']) ?></td>
                        <td>
                            <?php if ($f['allapot'] == 'foglalt'): ?>
                                <span class="badge bg-warning text-dark">Foglalt</span>
                            <?php elseif ($f['allapot'] == 'teljesitve'): ?>
                                <span class="badge bg-success">Teljesítve</span>
                            <?php elseif ($f['allapot'] == 'lemondva'): ?>
                                <span class="badge bg-danger">Lemondva</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($f['allapot']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?oldal=foglalasok&muvelet=szerkesztes&id=<?= $f['id'] ?>" class="btn btn-sm btn-warning">Szerkesztés</a>
                            <a href="index.php?oldal=foglalasok&muvelet=torles&id=<?= $f['id'] ?>" class="btn btn-sm btn-danger">Törlés</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Nincs megjeleníthető foglalás.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($muvelet == 'hozzaadas' || $muvelet == 'szerkesztes'): ?>
    <?php
    $aktualis_ugyfel = "";
    $aktualis_szolgaltatas = "";
    $aktualis_idopont = "";
    $aktualis_allapot = "foglalt";

    if ($muvelet == 'szerkesztes' && $id) {
        $foglalas_adat = api_kuld($alap_url . 'foglalasok&id=' . $id, 'GET');
        if (isset($foglalas_adat['ugyfel_id'])) {
            $aktualis_ugyfel = $foglalas_adat['ugyfel_id'];
            $aktualis_szolgaltatas = $foglalas_adat['szolgaltatas_id'];

            // IDO: YYYY-MM-DDThh:mm
            if (!empty($foglalas_adat['idopont'])) {
                $idopont_obj = new DateTime($foglalas_adat['idopont']);
                $aktualis_idopont = $idopont_obj->format('Y-m-d\TH:i');
            }

            $aktualis_allapot = $foglalas_adat['allapot'];
        }
    }

    $ugyfelek = api_kuld($alap_url . 'ugyfelek', 'GET');
    $szolgaltatasok = api_kuld($alap_url . 'szolgaltatasok', 'GET');
    ?>
    <div class="card w-50">
        <div class="card-body">
            <h4 class="card-title"><?= $muvelet == 'hozzaadas' ? 'Új foglalás' : 'Foglalás szerkesztése' ?></h4>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Ügyfél</label>
                    <select name="ugyfel_id" class="form-select" required>
                        <option value="">Válassz ügyfelet...</option>
                        <?php if (!empty($ugyfelek) && is_array($ugyfelek)) foreach ($ugyfelek as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $aktualis_ugyfel == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nev']) ?> (<?= htmlspecialchars($u['telefon']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Szolgáltatás</label>
                    <select name="szolgaltatas_id" class="form-select" required>
                        <option value="">Válassz szolgáltatást...</option>
                        <?php if (!empty($szolgaltatasok) && is_array($szolgaltatasok)) foreach ($szolgaltatasok as $sz): ?>
                            <option value="<?= $sz['id'] ?>" <?= $aktualis_szolgaltatas == $sz['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sz['megnevezes']) ?> - <?= number_format($sz['ar'], 0, ',', ' ') ?> Ft
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Időpont</label>
                    <input type="datetime-local" name="idopont" class="form-control" value="<?= htmlspecialchars($aktualis_idopont) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Állapot</label>
                    <select name="allapot" class="form-select" required>
                        <option value="foglalt" <?= $aktualis_allapot == 'foglalt' ? 'selected' : '' ?>>Foglalt</option>
                        <option value="teljesitve" <?= $aktualis_allapot == 'teljesitve' ? 'selected' : '' ?>>Teljesítve</option>
                        <option value="lemondva" <?= $aktualis_allapot == 'lemondva' ? 'selected' : '' ?>>Lemondva</option>
                    </select>
                </div>
                <button type="submit" name="mentes_gomb" class="btn btn-success">Mentés</button>
                <a href="index.php?oldal=foglalasok" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>

<?php elseif ($muvelet == 'torles' && $id): ?>
    <div class="card w-50 border-danger">
        <div class="card-body text-center">
            <h4 class="text-danger">Biztosan törölni szeretnéd a foglalást?</h4>
            <p>Ez a művelet nem vonható vissza!</p>
            <form method="post" action="">
                <button type="submit" name="megerosites" class="btn btn-danger">Igen, törlés</button>
                <a href="index.php?oldal=foglalasok" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>
<?php endif; ?>