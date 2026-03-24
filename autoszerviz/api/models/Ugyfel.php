<?php

class Ugyfel {
    private $kapcsolat;
    private $tabla_nev = "ugyfelek";

    public $id;
    public $nev;
    public $telefon;
    public $email;

    public function __construct($adatbazis_kapcsolat) {
        $this->kapcsolat = $adatbazis_kapcsolat;
    }

    public function letrehozas() {
        $lekerdezes = "INSERT INTO " . $this->tabla_nev . " SET nev=:nev, telefon=:telefon, email=:email";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->nev = htmlspecialchars(strip_tags($this->nev));
        $this->telefon = htmlspecialchars(strip_tags($this->telefon));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $utasitas->bindParam(":nev", $this->nev);
        $utasitas->bindParam(":telefon", $this->telefon);
        $utasitas->bindParam(":email", $this->email);

        if($utasitas->execute()) {
            return true;
        }
        return false;
    }

    public function olvasas() {
        $lekerdezes = "SELECT id, nev, telefon, email FROM " . $this->tabla_nev . " ORDER BY nev ASC";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->execute();
        return $utasitas;
    }

    public function olvasas_egy() {
        $lekerdezes = "SELECT id, nev, telefon, email FROM " . $this->tabla_nev . " WHERE id = ? LIMIT 0,1";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->bindParam(1, $this->id);
        $utasitas->execute();
        $sor = $utasitas->fetch(PDO::FETCH_ASSOC);
        if ($sor) {
            $this->nev = $sor['nev'];
            $this->telefon = $sor['telefon'];
            $this->email = $sor['email'];
        }
    }

    public function frissites() {
        $lekerdezes = "UPDATE " . $this->tabla_nev . " SET nev=:nev, telefon=:telefon, email=:email WHERE id=:id";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->nev = htmlspecialchars(strip_tags($this->nev));
        $this->telefon = htmlspecialchars(strip_tags($this->telefon));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $utasitas->bindParam(':nev', $this->nev);
        $utasitas->bindParam(':telefon', $this->telefon);
        $utasitas->bindParam(':email', $this->email);
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
