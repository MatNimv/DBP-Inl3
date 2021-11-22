<?php
require_once "../functions.php";
//Hämtar alla ägare från arrayn.
$allOwners = loadJson("owners.json");

?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
//Kontrollerare om metoden är GET.
if ($requestMethod === "GET") {
    //Hitta owner basserat på first_name
    if (isset($_GET["first_name"])) {
        foreach ($allOwners as $owner => $owner) {
            if ($owner["first_name"] == $_GET["first_name"]) {
                sendJson($allOwners[$owner]);
                exit();
            }
        }
    }
    //Om GET innehåller "ids" skapar den en ny array
    if (isset($_GET["ids"])) {
        $ids = explode(",", $_GET["ids"]);
        $ownersById = [];

        foreach ($allOwners as $owner) {
            if (in_array($owner["id"], $ids)) {
                $ownersById[] = $owner;
            }
        }
    }

    //Hitta owners baserat på id
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $ownersById = [];

        //Skapa tom array för id owners
        foreach ($allOwners as $owner) {
            if ($owner["id"] == $id) {
                $ownersById[] = $owner;
            }
        }
        //Om inte id finns, skicka felmeddelande
        if (count($ownersById) == 0) {
            $json = json_encode(["message" => "Owner does not exist"]);
            sendJson($json, 400);
            exit();
        }
        sendJson($ownersById);
        exit();
    }

    //om det finns AGE i URL.
    if (isset($_GET["age"])) {
        $age = $_GET["age"];
        $ageArray = [];

        foreach ($allOwners as $owner) {
            if ($owner["age"] == $age) {
                array_push($ageArray, $owner);
            }
            $allOwners = $ageArray;
        } //om det inte finns användare från det specifika landet.
        if (count($ageArray) == 0) {
            sendJson(["message" => "No owner at this age."]);
        }
    }
    if (isset($_GET["limit"])) { //skickar ut antal som finns i limit
        $limit = $_GET["limit"];
        $slicedOwners = array_slice($allOwners, 0, $limit);
        sendJson($slicedOwners);
    } else {
        sendJson($allOwners);
    }
} else { //Skickar ut ett felmeddalnde om metoden inte är json.
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
    exit();
}
?>