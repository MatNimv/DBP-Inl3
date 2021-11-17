<?php
require_once "../functions.php";
//denna sidan tar bort EN användare. 
//Tar emot {id}, 
//skickar iväg användaren som togs bort.

//tar emot inputen som användaren har gett i Insomnia.
//vilket är endast id.
$data = file_get_contents("php://input");
//decodar innehållet till en assossiativ array.
$requestData = json_decode($data, true);
//ALLA användare från users.json
$allUsers = loadJSON("users.json");

//variabel för metoden.
$method = $_SERVER["REQUEST_METHOD"];
//variabeln för content type.
$contentType = $_SERVER["CONTENT_TYPE"];

//ALLA användare från databasen.
$allUsers = loadJSON("users.json");

//allt utförs ENDAST om metoden är DELETE.
if ($method === "DELETE"){
    if ($contentType === "application/json"){
        $found = false;
        $id = $requestData["id"];

        $foundUser = null;

        if (!isset($id)){
            sendJson([
                "message" => "ID finns inte i requesten."
            ],400);
        }

        foreach ($allUsers as $index => $user){
            if($user["id"] == $id){
                $found = true;
                array_splice($allUsers, $index, 1);
                $foundUser = $user;
                break;
            } 
        }
        if ($found == false){
            sendJson([
                "message" => "ID:$id does not exist"
            ], 404);
        }

        saveJson("users.json", $allUsers);
        sendJson([
            "message" => "You removed this user ID:$id",
            "user" => $user]);
        } else {
            sendJson([
                "message" => "Content type must me JSON."
            ], 400);
        }
    }
?>