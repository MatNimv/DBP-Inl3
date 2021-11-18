<?php
require_once "../functions.php";
//denna filen ändrar valuet av nyckeln, beroende
//på vilka man skickar in.
//input {
//    id 
//    first_name && ||
//    last_name  && ||
//    gender && ||
//    pet 
//}
// output {
//    id 
//    first_name
//    last_name
//    gender 
//    pet 
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
if ($method === "PATCH"){
    //utförs endast om det är json.
    if ($contentType === "application/json"){
        //kollar om användaren har skickat med id.
        if (isset($requestData["id"])){

            $id = $requestData["id"];
            $found = false;
            $foundUser = null;
            //ALLA användare från users.json
            $allUsers = loadJSON("users.json");

            foreach($allUsers as $index => $user){
                if ($user["id"] == $id){
                    $found = true;

                    //kontrollerar alla nycklar och om de är tomma. 
                    //Om den har skickats med ändras valuen.
                    if (isset($requestData["first_name"])){
                        $firstName = $requestData["first_name"];

                        if (strlen($firstName) == 0){
                            //om firstname har inte har mer än 0 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a first name."
                                ]
                            ],400);
                        }
                    $user["first_name"] = $firstName;
                    }

                    if (isset($requestData["last_name"])){
                        $lastName = $requestData["last_name"];

                        if (strlen($lastName) == 0){
                            //om lastname har inte har mer än 0 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a last name."
                                ]
                            ],400);
                        }
                    $user["last_name"] = $lastName;
                    }

                    if (isset($requestData["gender"])){
                        $gender = $requestData["gender"];

                        if (strlen($gender) == 0){
                            //om gender inte har mer än 0 tecken.
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a gender."
                                ]
                            ],400);
                        }
                    $user["gender"] = $gender;
                    }

                    if (isset($requestData["pet"])){
                        $pet = $requestData["pet"];

                        if (strlen($pet) == 0){
                            //om pet inte har mer än 0 bostäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a pet."
                                ]
                            ],400);
                        }
                    $user["pet"] = $pet;
                    }

                    sendJson([$requestData]);
                    //uppdaterar användaren.
                    $allUsers[$index] = $user;
                    $foundUser = $user;
                    break;
                }
            }
            //uppdaterar databasen.
            saveJson("users.json", $allUsers);
            sendJson($foundUser);

            //om id inte finns i databasen.
            if ($found == false){
                sendJson(["message" => "ID was not found."], 404);
            }
        } else { //om id inte är med i requesten.
            sendJson(["message" => "Bad Request. ID must be included"], 400);
        }
    } else {//om contenttype inte är json
        sendJson(["message" => "Bad Request."], 400);
    }
} else { //om metoden inte är PATCH
    sendJson(["message" => "Method is not allowed."], 405);
}
?>