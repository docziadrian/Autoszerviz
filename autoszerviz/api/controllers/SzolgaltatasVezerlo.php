<?php

class SzolgaltatasVezerlo {
    private $szolgaltatas;

    public function __construct($adatbazis) {
        $this->szolgaltatas = new Szolgaltatas($adatbazis);
    }

    public function feldolgoz($keres_mod) {
        $azonosito = isset($_GET['id']) ? $_GET['id'] : null;

        switch($keres_mod) {
            case 'GET':
                if($azonosito) {
                    $this->szolgaltatas->id = $azonosito;
                    $this->szolgaltatas->olvasas_egy();
                    if($this->szolgaltatas->megnevezes != null) {
                        $szolgaltatas_tomb = array(
                            "id" =>  $this->szolgaltatas->id,
                            "megnevezes" => $this->szolgaltatas->megnevezes,
                            "ar" => $this->szolgaltatas->ar,
                            "idotartam_perc" => $this->szolgaltatas->idotartam_perc
                        );
                        http_response_code(200);
                        echo json_encode($szolgaltatas_tomb);
                    } else {
                        http_response_code(404);
                        echo json_encode(array("uzenet" => "Hiba: A szolgaltatas nem talalhato."));
                    }
                } else {
                    $eredmeny = $this->szolgaltatas->olvasas();
                    $szam = $eredmeny->rowCount();
                    if($szam > 0) {
                        $szolgaltatasok_tomb = array();
                        while ($sor = $eredmeny->fetch(PDO::FETCH_ASSOC)) {
                            extract($sor);
                            $szolgaltatas_elem = array(
                                "id" => $id,
                                "megnevezes" => $megnevezes,
                                "ar" => $ar,
                                "idotartam_perc" => $idotartam_perc
                            );
                            array_push($szolgaltatasok_tomb, $szolgaltatas_elem);
                        }
                        http_response_code(200);
                        echo json_encode($szolgaltatasok_tomb);
                    } else {
                        http_response_code(200);
                        echo json_encode(array());
                    }
                }
                break;
            case 'POST':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->megnevezes) && !empty($adatok->ar) && !empty($adatok->idotartam_perc)) {
                    $this->szolgaltatas->megnevezes = $adatok->megnevezes;
                    $this->szolgaltatas->ar = $adatok->ar;
                    $this->szolgaltatas->idotartam_perc = $adatok->idotartam_perc;
                    
                    if($this->szolgaltatas->letrehozas()) {
                        http_response_code(201);
                        echo json_encode(array("uzenet" => "Szolgaltatas letrehozva."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni it."));
                    }
                } else {
                    if (isset($_POST['megnevezes']) && isset($_POST['ar']) && isset($_POST['idotartam_perc'])) {
                        $this->szolgaltatas->megnevezes = $_POST['megnevezes'];
                        $this->szolgaltatas->ar = $_POST['ar'];
                        $this->szolgaltatas->idotartam_perc = $_POST['idotartam_perc'];
                        
                        if($this->szolgaltatas->letrehozas()) {
                            http_response_code(201);
                            echo json_encode(array("uzenet" => "Szolgaltatas letrehozva."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Hiba: Nem sikerult letrehozni."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok."));
                    }
                }
                break;
            case 'PUT':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id) && !empty($adatok->megnevezes) && !empty($adatok->ar) && !empty($adatok->idotartam_perc)) {
                    $this->szolgaltatas->id = $adatok->id;
                    $this->szolgaltatas->megnevezes = $adatok->megnevezes;
                    $this->szolgaltatas->ar = $adatok->ar;
                    $this->szolgaltatas->idotartam_perc = $adatok->idotartam_perc;
                    
                    if($this->szolgaltatas->frissites()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Szolgaltatas frissitve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Hiba: Nem sikerult frissiteni."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("uzenet" => "Hianyzo adatok."));
                }
                break;
            case 'DELETE':
                $adatok = json_decode(file_get_contents("php://input"));
                if(!empty($adatok->id)) {
                    $this->szolgaltatas->id = $adatok->id;
                    if($this->szolgaltatas->torles()) {
                        http_response_code(200);
                        echo json_encode(array("uzenet" => "Szolgaltatas torolve."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("uzenet" => "Nem sikerult torolni."));
                    }
                } else {
                    if($azonosito) {
                        $this->szolgaltatas->id = $azonosito;
                        if($this->szolgaltatas->torles()) {
                            http_response_code(200);
                            echo json_encode(array("uzenet" => "Szolgaltatas torolve."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("uzenet" => "Nem sikerult torolni."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("uzenet" => "Hianyzo adatok."));
                    }
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array("uzenet" => "Nem engedelyezett metodus."));
                break;
        }
    }
}
?>
