<?php

class Szolgaltatas {
    private $kapcsolat;
    private $tabla_nev = "szolgaltatasok";

    public $id;
    public $megnevezes;
    public $ar;
    public $idotartam_perc;

    public function __construct($adatbazis_kapcsolat) {
        $this->kapcsolat = $adatbazis_kapcsolat;
    }

    public function letrehozas() {
        $lekerdezes = "INSERT INTO " . $this->tabla_nev . " SET megnevezes=:megnevezes, ar=:ar, idotartam_perc=:idotartam_perc";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->megnevezes = htmlspecialchars(strip_tags($this->megnevezes));
        $this->ar = htmlspecialchars(strip_tags($this->ar));
        $this->idotartam_perc = htmlspecialchars(strip_tags($this->idotartam_perc));

        $utasitas->bindParam(":megnevezes", $this->megnevezes);
        $utasitas->bindParam(":ar", $this->ar);
        $utasitas->bindParam(":idotartam_perc", $this->idotartam_perc);

        if($utasitas->execute()) {
            return true;
        }
        return false;
    }

    public function olvasas() {
        $lekerdezes = "SELECT id, megnevezes, ar, idotartam_perc FROM " . $this->tabla_nev . " ORDER BY megnevezes ASC";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->execute();
        return $utasitas;
    }

    public function olvasas_egy() {
        $lekerdezes = "SELECT id, megnevezes, ar, idotartam_perc FROM " . $this->tabla_nev . " WHERE id = ? LIMIT 0,1";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);
        $utasitas->bindParam(1, $this->id);
        $utasitas->execute();
        $sor = $utasitas->fetch(PDO::FETCH_ASSOC);
        if ($sor) {
            $this->megnevezes = $sor['megnevezes'];
            $this->ar = $sor['ar'];
            $this->idotartam_perc = $sor['idotartam_perc'];
        }
    }

    public function frissites() {
        $lekerdezes = "UPDATE " . $this->tabla_nev . " SET megnevezes=:megnevezes, ar=:ar, idotartam_perc=:idotartam_perc WHERE id=:id";
        $utasitas = $this->kapcsolat->prepare($lekerdezes);

        $this->megnevezes = htmlspecialchars(strip_tags($this->megnevezes));
        $this->ar = htmlspecialchars(strip_tags($this->ar));
        $this->idotartam_perc = htmlspecialchars(strip_tags($this->idotartam_perc));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $utasitas->bindParam(':megnevezes', $this->megnevezes);
        $utasitas->bindParam(':ar', $this->ar);
        $utasitas->bindParam(':idotartam_perc', $this->idotartam_perc);
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
