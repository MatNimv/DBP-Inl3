<?php
require_once "../functions.php";
//denna filen ändrar valuet av nyckeln, beroende
//på vilka man skickar in.
//input {
//    id 
//    name && ||
//    species  && ||
//    origin && ||
//    owner 
//}
// output {
//    id 
//    name
//    species
//    origin 
//    owner 
//    OCH om det är en nyckel som inte skickas med
//    "nyckel"
//    "nyckel" osv
//}

//variabel för metoden.
$method = $_SERVER["REQUEST_METHOD"];
//variabeln för content type.
$contentType = $_SERVER["CONTENT_TYPE"];
//all data som användaren skickar in
$data = file_get_contents("php://input");
//asso array av datan
$requestData = json_decode($data, true);

//utförs om metoden ENDAST är patch.
if ($method === "PATCH") {
    //utförs endast om det är json.
    if ($contentType === "application/json") {
        //kollar om ägaren har skickat med id.
        if (isset($requestData["id"])) {

            $id = $requestData["id"];
            $found = false;
            $foundPet = null;
            //ALLA användare från users.json
            $allPets = loadJSON("pets.json");
            //Letar upp djurets id och redigerar innehållet.
            foreach ($allPets as $index => $pet) {
                if ($pet["id"] == $id) {
                    $found = true;
                    $keyNotSetArr = [];

                    //kontrollerar alla nycklar och om de är tomma. 
                    //Om den har skickats med ändras valuen.
                    if (isset($requestData["name"])) {
                        $name = $requestData["name"];

                        if (strlen($name) == 0) {
                            //om name 0 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a name."
                                ]
                            ], 400);
                            exit();
                        }
                        $pet["name"] = $name;
                    } else {
                        array_push($keyNotSetArr, "name");
                    }

                    if (isset($requestData["species"])) {
                        $species = $requestData["species"];

                        if (strlen($species) <= 2) {
                            //om species har färre än 3 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a species."
                                ]
                            ], 400);
                            exit();
                        }
                        $pet["species"] = $species;
                    } else {
                        array_push($keyNotSetArr, "species");
                    }

                    if (isset($requestData["origin"])) {
                        $origin = $requestData["origin"];

                        if (strlen($origin) <= 2) {
                            //om origin inte har mer än 2 tecken.
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write more than 2 letters for your country."
                                ]
                            ], 400);
                            exit();
                        }
                        $pet["origin"] = $origin;
                    } else {
                        array_push($keyNotSetArr, "origin");
                    }

                    if (isset($requestData["owner"])) {
                        $owner = $requestData["owner"];

                        if ($owner == 0) {
                            //om ägarens ID är 0
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please provide a valid ID."
                                ]
                            ], 400);
                            exit();
                        }
                        $pet["owner"] = $owner;
                    } else {
                        array_push($keyNotSetArr, "owner");
                    }

                    //uppdaterar användaren.
                    $allPets[$index] = $pet;
                    $foundPet = $pet;
                    break;
                }
            }
            //uppdaterar databasen.
            saveJson("pets.json", $allPets);

            //om det är nycklar som inte skickats in, skrivs de ut med användaren.
            if (count($keyNotSetArr) >= 1) {
                $messageArr = [];
                foreach ($keyNotSetArr as $oneKey) {
                    array_push($messageArr, $oneKey);
                }
                sendJson([
                    "Pet" => $foundPet,
                    "Keys not changed. If this seems wrong, please check your spelling." => $messageArr
                ]);
                exit();
            } else { //annars skickas bara hela ägaren.
                sendJson([$foundPet]);
                exit();
            }

            //om id inte finns i databasen.
            if ($found == false) {
                sendJson(["message" => "ID was not found."], 404);
                exit();
            }
        } else { //om id inte är med i requesten.
            sendJson(["message" => "Bad Request. ID must be included"], 400);
            exit();
        }
    } else { //om contenttype inte är json
        sendJson(["message" => "Bad Request."], 400);
        exit();
    }
} else { //om metoden inte är PATCH
    sendJson(["message" => "Method is not allowed."], 405);
    exit();
}
