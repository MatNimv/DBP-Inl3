<?php
require_once "../functions.php";

$allUsers = loadJson("../users.json");
$allPets = loadJson("pets.json");
?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "GET") {
    if ($contentType === "application/json") {
        //hitta djur basserat på "name"
        if (isset($_GET["name"])) {
            foreach ($allPets as $pet => $pet) {
                if ($pet["name"] == $_GET["name"]) {
                    sendJson($allPets[$pet]);
                    exit();
                }
            }
        }

        //Maximalt antal djur
        if (isset($_GET["limit"])) {
            $limit = $_GET["limit"];
            $slicedUsers = array_slice($allPets, 0, $limit);
            sendJson($slicedUsers);
            exit();
        }

        //hitta djur bassserat på id
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $petsById = [];

            foreach ($allPets as $pet) {
                if ($pet["id"] == $id) {
                    $petsById[] = $pet;
                }
                //ifall id inte finns, skicka felmeddelande
            }
            if (count($petsById) == 0) {
                $json = json_encode(["message" => "User does not exist"]);
                sendJson($json, 400);
            }


            sendJson($petsById);
            exit();
        }
        //Om formatet inte är JSON skickas det ut ett felmeddelande.
    } else {
        sendJson(["message" => "Bad Request."], 400);
    }
    //Om GET inte är GET skickas det ut ett felmeddelande. 
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
    exit();
}

//Skicka alla djur
sendJson($allPets);
?>