<?php
require_once "../functions.php";

$allUsers = loadJson("../users.json");
$allPets = loadJson("pets.json");
?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod === "GET") {
    //first_name
    if (isset($_GET["name"])) {
        foreach ($allPets as $pet => $pet) {
            if ($pet["name"] == $_GET["name"]) {
                sendJson($allPets[$pet]);
                exit();
            
            }
        }
    }
}
if (isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    $slicedUsers = array_slice($allPets, 0, $limit);
    sendJson($slicedUsers);
    exit();
   
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $petsById = [];

    foreach ($allPets as $pet) {
        if ($pet["id"] == $id) {
            $petsById[] = $pet;
        }
    } if(count($petsById)==0){
        $json = json_encode(["message"=>"User does not exist"]);
        sendJson($json, 400);
    }
    

    sendJson($petsById);
    exit();
}

sendJson($allPets);
?>