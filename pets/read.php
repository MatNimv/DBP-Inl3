<?php
require_once "../functions.php";

$allUsers = loadJson("users.json");
$allPets = loadJson("../pets.json");
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
    $slicedUsers = array_slice($allUsers, 0, $limit);
    sendJson($slicedUsers);
    exit();
   
}


if (isset($_GET["id"])) {
    $ids = explode(",", $_GET["id"]);
    $petsById = [];

    foreach ($allPets as $pet) {
        if (in_array($user["id"], $ids)) {
            $petsById[] = $user;
        }
    } if(count($petsById)==0){
        $json = json_encode(["message"=>"User does not exist"]);
        sendJson($json, 400);
    }
    

    sendJson($usersById);
    exit();
}

sendJson($allUsers);
?>