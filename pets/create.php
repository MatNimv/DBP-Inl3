<?php
//Kopplar functions.php
require_once "../functions.php";

//Ladda in vår JSON-data från fil
$pets = loadJSON("pets.json");
$owners = loadJSON("../users/users.json");

//Hittar ägarnas ID
$ownersID = array_column($owners, "id");

//Vilken HTTP-metod vi tog emot samt vilken sorts content det är
$rqstMethod = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

//Hämtar ut det som skickades till vår server
$data = file_get_contents("php://input");
$rqstData = json_decode($data, true);


//Kollar om metoden är post
if ($rqstMethod == "POST") {
    if ($contentType === "application/json") {
        //Kollar att alla fälten är ifyllda
        if (isset($rqstData["name"], $rqstData["species"], $rqstData["origin"], $rqstData["owner"])) {
            $name = $rqstData["name"];
            $species = $rqstData["species"];
            $origin = $rqstData["origin"];
            $owner = $rqstData["owner"];

            $check = true;

            //Kollar om namnet innehåller minst 2 bokstäver
            if (strlen($name) < 2) {
                $check = false;

                $json = json_encode(
                    [
                        "name" => "Name has to include at least 2 characters",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Kollar om rasen innehåller minst 2 bokstäver
            if (strlen($species) < 2) {
                $check = false;

                $json = json_encode(
                    [
                        "species" => "Species has to include at least 2 characters",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Kollar om härkomsten innehåller minst 2 bokstäver
            if (strlen($origin) < 2) {
                $check = false;

                $json = json_encode(
                    [
                        "origin" => "Name of origin has to include at least 2 characters",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Skickar ut ett felmeddelande om det inne finns en ägare med detta id
            if (!in_array($owner, $ownersID)) {
                $check = false;

                $json = json_encode(
                    [
                        "owner" => "This owner does not exist",
                    ]
                );
                sendJson($json, 400);
                exit();
            }

            //Om check är sant så skapar vi det nya djuret
            if ($check) {
                //Hittar det högsta ID:et
                $highestID = theHighestId($pets);
                //Skapar det nya djuret i rätt ordning
                $newPet = array(
                    "id" => $highestID,
                    "name" => $name,
                    "species" => $species,
                    "origin" => $origin,
                    "owner" => $owner,
                );
                //Pushar in det nya djuret i arrayen. 
                array_push($pets, $newPet);

                saveJson("pets.json", $pets);
                sendJson($newPet, 201);
                exit();
            }
            //Om alla fälten inte är ifyllda skickas det ut ett felmeddelande.
        } else {
            $json = json_encode(["message" => "Bad Request - (all the fields has to be filled)"]);
            sendJson($json, 400);
            exit();
        }
        //Om content inte är JSON skickas det ut ett felmeddelande.
    } else {
        sendJson(["message" => "Bad Request."], 400);
    }
    //Om GET inte är POST skickas det ut ett felmeddelande. 
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
    exit();
}
