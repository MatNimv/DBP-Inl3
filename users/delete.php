<?php
require_once "functions.php";
//tar bort EN användare. 

//tar emot inputen som användaren har gett i Insomnia.
//vilket är endast id.
$data = file_get_contents("php://input");
//decodar innehållet till en assossiativ array.
$requestData = json_decode($data, true);
//ALLA användare från users.json
$allUsers = loadJSON("users.json");

//variabel för metoden.
$method = $_SERVER["REQUEST_METHOD"];

//ALLA användare från databasen.
$allUsers = loadJSON("users.json");

//allt utförs ENDAST om metoden är DELETE.
if ($method === "DELETE"){
    $found = false;
    $id = $requestData["id"];

    if (!isset($id)){
        sendJson([
            "message" => "ID finns inte i requesten."
        ],400);
    }

    foreach ($allUsers as $index => $user){
        if($user["id"] == $id){
            $found = true;
            array_splice($allUsers, $index, 1);
            break;
        } 
    }
    if ($found == false){
        sendJson([
            "message" => "ID:$id does not exist"
        ], 404);
    }

    saveJson("database.json", $allUsers);
    sendJson(["id" => "You removed this user ID:$id"]);
}
?>