<?php
require_once "../functions.php";
//denna sidan tar bort EN användare. 
//Input: {id}, 
//Output: id samt hela användaren.
//   {
//       id: 
//       first_name:
//       last_name:
//       gender:
//       pet:
//   }

//tar emot inputen som användaren har gett i Insomnia.
//vilket är endast id.
$data = file_get_contents("php://input");
//decodar innehållet till en assossiativ array.
$requestData = json_decode($data, true);

//variabel för metoden.
$method = $_SERVER["REQUEST_METHOD"];
//variabeln för content type.
$contentType = $_SERVER["CONTENT_TYPE"];

//får ut ALLA användare från databasen.
$allOwners = loadJSON("owners.json");

//allt utförs ENDAST om metoden är DELETE.
if ($method === "DELETE"){
    //utförs endast om contentype är json
    if ($contentType === "application/json"){
        $found = false;
        $id = $requestData["id"];

        $foundOwner = null;

        if (!isset($id)){ //om id inte ens finns i URl.
            sendJson(["message" => "Bad Request. ID must be included."],400);
        }

        foreach ($allOwners as $index => $owner){
            if($owner["id"] == $id){
                $found = true;
                array_splice($allOwners, $index, 1);
                $foundOwner = $owner;
                break;
            } 
        }
        if ($found == false){ //om id inte matchar med den requestade.
            sendJson(["message" => "ID:$id was not found."], 404);
        }

        //sparar den "uppdaterade" users.json filen.
        saveJson("owners.json", $allOwners);
        //meddelar vilket id samt hela användaren som togs bort
        sendJson([
            "message" => "You removed this owner ID:$id",
            "owner" => $owner]);
        } else { //om contenttype inte är application/json.
            sendJson(["message" => "Bad Request."], 400);
        }
    } else { //alla andra metoder än DELETE.
        sendJson(["message" => "Method is not allowed."], 405);
    }
