<?php
require_once "../functions.php";

$allOwners = loadJson("owners.json");
$allPets = loadJson("../pets.json");
?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
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
}
//skapa ett maximalt antal owners i arrayn
if (isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    $slicedOwners = array_slice($allOwners, 0, $limit);
    sendJson($slicedOwners);
    exit();
}

//Hitta owners baserat på id
if (isset($_GET["id"])) {
    $ids = explode(",", $_GET["id"]);
    $ownersById = [];
    
    //Skapa tom array för id owners
    foreach ($allOwners as $owner) {
        if (in_array($owner["id"], $ids)) {
            $ownersById[] = $owner;
        }
    }
    //Om inte id finns, skicka felmeddelande
    if (count($ownersById) == 0) {
        $json = json_encode(["message" => "Owner does not exist"]);
        sendJson($json, 400);
    }
}

    //om det finns AGE i URL.
    if(isset($_GET["age"])){
        $age = $_GET["age"];
        $ageArray = [];

        foreach($allOwners as $owner){
            if ($owner["age"] == $age){
                array_push($ageArray, $owner);
            }
        } //om det inte finns användare i den specifika åldern.
        if (count($ageArray)== 0){
            sendJson(["message" => "No owner at this age."]);
        } else { //annars skickar den ut alla med den specifika åldern.
            sendJson($ageArray);
        }
    } else { //om inte det finns AGE, skickas alla användare ut ändå.
        sendJson($allOwners);
    }
?>