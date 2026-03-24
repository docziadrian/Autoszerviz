<?php

class Foglalas {
    private $kapcsolat;
    private $tabla_nev = "foglalasok";

    public $id;
    public $ugyfel_id;
    public $szolgaltatas_id;
    public $idopont;
    public $allapot;

    public $ugyfel_nev;
    public $szolgaltatas_megnevezes;

    public function __construct($adatbazis_kapcsolat) {
        $this->kapcsolat = $adatbazis_kapcsolat;
    }

    public function letrehozas() {
        $lekerdezes = "INSERT INTO " . $this->tabla_nev . " SET ugyfel_id=:ugyfel_id, szolgaltatas_id=:szolgaltatas_id, idopont=:idopont, allapot=:allapot";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->ugyfel_id = htmlspecialchars(strip_tags($this->ugyfel_id));
        $this->szolgaltatas_id = htmlspecialchars(strip_tags($this->szolgaltatas_id));
        $this->idopont = htmlspecialchars(strip_tags($this->idopont));
        $this->allapot = htmlspecialchars(strip_tags($this->allapot));

        $utasitas->bindParam(":ugyfel_id", $this->ugyfel_id);
        $utasitas->bindParam(":szolgaltatas_id", $this->szolgaltatas_id);
        $utasitas->bindParam(":idopont", $this->idopont);
        $utasitas->bindParam(":allapot", $this->allapot);

        if($utasitas->execute()) {
            return true;
        }
        return false;
    }

    public function olvasas() {
        $lekerdezes = "
            SELECT 
                f.id, f.ugyfel_id, f.szolgaltatas_id, f.idopont, f.allapot, 
                u.nev AS ugyfel_nev, 
                sz.megnevezes AS szolgaltatas_megnevezes 
            FROM " . $this->tabla_nev . " f 
            LEFT JOIN ugyfelek u ON f.ugyfel_id = u.id 
            LEFT JOIN szolgaltatasok sz ON f.szolgaltatas_id = sz.id 
            ORDER BY f.idopont DESC";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->execute();
        return $utasitas;
    }

    public function olvasas_egy() {
        $lekerdezes = "
            SELECT 
                f.id, f.ugyfel_id, f.szolgaltatas_id, f.idopont, f.allapot, 
                u.nev AS ugyfel_nev, 
                sz.megnevezes AS szolgaltatas_megnevezes 
            FROM " . $this->tabla_nev . " f 
            LEFT JOIN ugyfelek u ON f.ugyfel_id = u.id 
            LEFT JOIN szolgaltatasok sz ON f.szolgaltatas_id = sz.id 
            WHERE f.id = ? 
            LIMIT 0,1";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->bindParam(1, $this->id);
        $utasitas->execute();
        $sor = $utasitas->fetch(PDO::FETCH_ASSOC);
        if ($sor) {
            $this->ugyfel_id = $sor['ugyfel_id'];
            $this->szolgaltatas_id = $sor['szolgaltatas_id'];
            $this->idopont = $sor['idopont'];
            $this->allapot = $sor['allapot'];
            $this->ugyfel_nev = $sor['ugyfel_nev'];
            $this->szolgaltatas_megnevezes = $sor['szolgaltatas_megnevezes'];
        }
    }

    public function frissites() {
        $lekerdezes = "UPDATE " . $this->tabla_nev . " SET ugyfel_id=:ugyfel_id, szolgaltatas_id=:szolgaltatas_id, idopont=:idopont, allapot=:allapot WHERE id=:id";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->ugyfel_id = htmlspecialchars(strip_tags($this->ugyfel_id));
        $this->szolgaltatas_id = htmlspecialchars(strip_tags($this->szolgaltatas_id));
        $this->idopont = htmlspecialchars(strip_tags($this->idopont));
        $this->allapot = htmlspecialchars(strip_tags($this->allapot));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $utasitas->bindParam(':ugyfel_id', $this->ugyfel_id);
        $utasitas->bindParam(':szolgaltatas_id', $this->szolgaltatas_id);
        $utasitas->bindParam(':idopont', $this->idopont);
        $utasitas->bindParam(':allapot', $this->allapot);
        $utasitas->bindParam(':id', $this->id);

        if($utasitas->execute()) {
            return true;
        }
        return false;
    }

    public function torles() {
        $lekerdezes = "DELETE FROM " . $this->tabla_nev . " WHERE id = ?";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $utasitas->bindParam(1, $this->id);

        if($utasitas->execute()) {
            return true;
        }
        return false;
    }
}
?>
