<?php
require_once "../functions.php";
//denna sidan tar bort ETT djur. 
//Input: {id}, 
//Output: id samt hela djuret.
//   {
//       id: 
//       name:
//       species:
//       origin:
//       owner:
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
$allPets = loadJSON("pets.json");

//allt utförs ENDAST om metoden är DELETE.
if ($method === "DELETE"){
    //utförs endast om contentype är json
    if ($contentType === "application/json"){
        $found = false;
        $id = $requestData["id"];

        $foundPet = null;

        if (!isset($id)){ //om id inte ens finns i URl.
            sendJson(["message" => "Bad Request. ID must be included."],400);
        }

        foreach ($allPets as $index => $pet){
            if($pet["id"] == $id){
                $found = true;
                array_splice($allPets, $index, 1);
                $foundPet = $pet;
                break;
            } 
        }
        if ($found == false){ //om id inte matchar med den requestade.
            sendJson(["message" => "ID:$id was not found."], 404);
        }

        //sparar den "uppdaterade" users.json filen.
        saveJson("pets.json", $allPets);
        //meddelar vilket id samt hela användaren som togs bort
        sendJson([
            "message" => "You removed this user ID:$id",
            "user" => $pet]);
        } else { //om contenttype inte är application/json.
            sendJson(["message" => "Bad Request."], 400);
        }
    } else { //alla andra metoder än DELETE.
        sendJson(["message" => "Method is not allowed."], 405);
    }
?>