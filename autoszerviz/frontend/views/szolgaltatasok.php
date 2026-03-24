<?php
$hiba = "";
$siker = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mentes_gomb'])) {
    if ($muvelet == 'hozzaadas') {
        $adat = array(
            'megnevezes' => $_POST['megnevezes'],
            'ar' => $_POST['ar'],
            'idotartam_perc' => $_POST['idotartam_perc']
        );
        $valasz = api_kuld($alap_url . 'szolgaltatasok', 'POST', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'letrehozva') !== false) {
            header("Location: index.php?oldal=szolgaltatasok&uzenet=siker_hozzaadas");
            exit;
        } else {
            $hiba = "Nem sikerült a szolgáltatás hozzáadása.";
        }
    } elseif ($muvelet == 'szerkesztes' && $id) {
        $adat = array(
            'id' => $id,
            'megnevezes' => $_POST['megnevezes'],
            'ar' => $_POST['ar'],
            'idotartam_perc' => $_POST['idotartam_perc']
        );
        $valasz = api_kuld($alap_url . 'szolgaltatasok', 'PUT', $adat);
        if (isset($valasz['uzenet']) && strpos($valasz['uzenet'], 'frissitve') !== false) {
            header("Location: index.php?oldal=szolgaltatasok&uzenet=siker_szerkesztes");
            exit;
        } else {
            $hiba = "Nem sikerült a szolgáltatás módosítása.";
        }
    }
}

if ($muvelet == 'torles' && $id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['megerosites'])) {
        $adat = array('id' => $id);
        $valasz = api_kuld($alap_url . 'szolgaltatasok', 'DELETE', $adat);
        header("Location: index.php?oldal=szolgaltatasok&uzenet=siker_torles");
        exit;
    }
}

if (isset($_GET['uzenet'])) {
    if ($_GET['uzenet'] == 'siker_hozzaadas') $siker = "Szolgáltatás sikeresen hozzáadva!";
    if ($_GET['uzenet'] == 'siker_szerkesztes') $siker = "Szolgáltatás sikeresen módosítva!";
    if ($_GET['uzenet'] == 'siker_torles') $siker = "Szolgáltatás sikeresen törölve!";
}
?>

<h2>Szolgáltatások kezelése</h2>

<?php if ($hiba): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($hiba) ?></div>
<?php endif; ?>
<?php if ($siker): ?>
    <div class="alert alert-success"><?= htmlspecialchars($siker) ?></div>
<?php endif; ?>

<?php if ($muvelet == 'lista'): ?>
    <?php $szolgaltatasok = api_kuld($alap_url . 'szolgaltatasok', 'GET'); ?>
    <a href="index.php?oldal=szolgaltatasok&muvelet=hozzaadas" class="btn btn-primary mb-3">Új szolgáltatás hozzáadása</a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Megnevezés</th>
                <th>Ár (Ft)</th>
                <th>Időtartam (perc)</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($szolgaltatasok) && is_array($szolgaltatasok)): ?>
                <?php foreach ($szolgaltatasok as $sz): ?>
                    <tr>
                        <td><?= htmlspecialchars($sz['id']) ?></td>
                        <td><?= htmlspecialchars($sz['megnevezes']) ?></td>
                        <td><?= number_format($sz['ar'], 0, ',', ' ') ?> Ft</td>
                        <td><?= htmlspecialchars($sz['idotartam_perc']) ?> perc</td>
                        <td>
                            <a href="index.php?oldal=szolgaltatasok&muvelet=szerkesztes&id=<?= $sz['id'] ?>" class="btn btn-sm btn-warning">Szerkesztés</a>
                            <a href="index.php?oldal=szolgaltatasok&muvelet=torles&id=<?= $sz['id'] ?>" class="btn btn-sm btn-danger">Törlés</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nincs megjeleníthető szolgáltatás.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($muvelet == 'hozzaadas' || $muvelet == 'szerkesztes'): ?>
    <?php
    $aktualis_megnevezes = "";
    $aktualis_ar = "";
    $aktualis_idotartam = "";
    
    if ($muvelet == 'szerkesztes' && $id) {
        $szolgaltatas_adat = api_kuld($alap_url . 'szolgaltatasok&id=' . $id, 'GET');
        if (isset($szolgaltatas_adat['megnevezes'])) {
            $aktualis_megnevezes = $szolgaltatas_adat['megnevezes'];
            $aktualis_ar = $szolgaltatas_adat['ar'];
            $aktualis_idotartam = $szolgaltatas_adat['idotartam_perc'];
        }
    }
    ?>
    <div class="card w-50">
        <div class="card-body">
            <h4 class="card-title"><?= $muvelet == 'hozzaadas' ? 'Új szolgáltatás' : 'Szolgáltatás szerkesztése' ?></h4>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Megnevezés</label>
                    <input type="text" name="megnevezes" class="form-control" value="<?= htmlspecialchars($aktualis_megnevezes) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ár (Ft)</label>
                    <input type="number" name="ar" class="form-control" value="<?= htmlspecialchars($aktualis_ar) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Időtartam (perc)</label>
                    <input type="number" name="idotartam_perc" class="form-control" value="<?= htmlspecialchars($aktualis_idotartam) ?>" required>
                </div>
                <button type="submit" name="mentes_gomb" class="btn btn-success">Mentés</button>
                <a href="index.php?oldal=szolgaltatasok" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>

<?php elseif ($muvelet == 'torles' && $id): ?>
    <div class="card w-50 border-danger">
        <div class="card-body text-center">
            <h4 class="text-danger">Biztosan törölni szeretnéd a szolgáltatást?</h4>
            <p>Ez a művelet nem vonható vissza!</p>
            <form method="post" action="">
                <button type="submit" name="megerosites" class="btn btn-danger">Igen, törlés</button>
                <a href="index.php?oldal=szolgaltatasok" class="btn btn-secondary">Mégse</a>
            </form>
        </div>
    </div>
<?php endif; ?>
