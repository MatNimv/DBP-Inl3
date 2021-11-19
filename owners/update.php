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
//    OCH om det är en nyckel som inte skickas med
//    "nyckel"
//    "nyckel" osv
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
            $foundOwner = null;
            //ALLA användare från users.json
            $allOwners = loadJSON("owners.json");

            foreach($allOwners as $index => $owner){
                if ($owner["id"] == $id){
                    $found = true;
                    $keyNotSetArr = [];

                    //kontrollerar alla nycklar och om de är tomma. 
                    //Om den har skickats med ändras valuen.
                    if (isset($requestData["first_name"])){
                        $firstName = $requestData["first_name"];

                        if (strlen($firstName) == 0){
                            //om firstname 0 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a first name."
                                ]
                            ],400);
                        }
                    $owner["first_name"] = $firstName;
                    } else {
                        array_push($keyNotSetArr, "first_name");
                    }

                    if (isset($requestData["last_name"])){
                        $lastName = $requestData["last_name"];

                        if (strlen($lastName) == 0){
                            //om lastname har 0 bokstäver
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write a last name."
                                ]
                            ],400);
                        }
                    $owner["last_name"] = $lastName;
                    } else {
                        array_push($keyNotSetArr, "last_name");
                    }

                    if (isset($requestData["gender"])){
                        $gender = $requestData["gender"];

                        if (strlen($gender) <= 1){
                            //om gender inte har mer än 1 tecken.
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "Please write more than 1 letter for your gender."
                                ]
                            ],400);
                        }
                    $owner["gender"] = $gender;
                    } else {
                        array_push($keyNotSetArr, "gender");
                    }

                    if (isset($requestData["age"])){
                        $age = $requestData["age"];

                        if ($age <= 12){
                            //om åldern inte är mer än 12
                            sendJson([
                                "message" => "Bad Request, invalid format",
                                "errors" => [
                                    "message" => "A number higher than 12 for age is required."
                                ]
                            ],400);
                        }
                    $owner["age"] = $age;
                    } else {
                        array_push($keyNotSetArr, "age");
                    }

                    //uppdaterar användaren.
                    $allOwners[$index] = $owner;
                    $foundOwner = $owner;
                    break;
                }
            }
            //uppdaterar databasen.
            saveJson("users.json", $allOwners);

            //om det är nycklar som inte skickats in, skrivs de ut med användaren.
            if (count($keyNotSetArr) >= 1){
                $messageArr = [];
                foreach($keyNotSetArr as $oneKey){
                    array_push($messageArr, $oneKey);
                }
                sendJson([
                    "User" => $foundOwner,
                    "Keys not changed. If this seems wrong, please check your spelling." => $messageArr
                ]);
            } else { //annars skickas bara hela användaren.
                sendJson([
                    "User" => $foundOwner]);
            }

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
