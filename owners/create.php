<?php
//Kopplar functions.php
require_once "../functions.php";

//Ladda in vår JSON-data från fil
$users = loadJSON("users.json");

//Vilken HTTP-metod vi tog emot samt vilken sorts content det är
$rqstMethod = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

//Hämtar ut det som skickades till vår server
$data = file_get_contents("php://input");
$rqstData = json_decode($data, true);

//Kollar om metoden är post
if ($rqstMethod == "POST") {
    if ($contentType === "application/json") {
        //Kollar att alla fälten är ifyllda
        if (isset($rqstData["first_name"], $rqstData["last_name"], $rqstData["gender"], $rqstData["age"])) {
            $firstName = $rqstData["first_name"];
            $lastName = $rqstData["last_name"];
            $gender = $rqstData["gender"];
            $age = $rqstData["age"];

            $check = true;

            //Kollar om förnamnet innehåller minst 2 bokstäver
            if (strlen($firstName) < 2) {
                $check = false;

                $json = json_encode(
                    [
                        "first_name" => "First name has to have at least 2 characters",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Kollar om efternamnet innehåller minst 2 bokstäver
            if (strlen($lastName) < 2) {
                $check = false;

                $json = json_encode(
                    [
                        "last_name" => "Last name has to have at least 2 characters",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Kollar om kön innehåller minst 1 bokstav
            if (strlen($gender) < 1) {
                $check = false;

                $json = json_encode(
                    [
                        "gender" => "Please add at least one character to your gender",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Kollar om personen är minst 13 år eller högst lika gammal som Bilbo Baggins.
            if ($age < 13 || $age > 111) {
                $check = false;

                $json = json_encode(
                    [
                        "age" => "You can not be younger than 13 nor older than 111",
                    ]
                );
                sendJson($json, 400);
                exit();
            }
            //Om check är sant så skapar vi den nya användaren.
            if ($check) {
                //Hittar det högsta ID:et
                $highestID = theHighestId($users);
                //Skapar den nya användaren i rätt ordning
                $newUser = array(
                    "id" => $highestID,
                    "first_name" => $firstName,
                    "last_name" => $lastName,
                    "gender" => $gender,
                    "age" => $age
                );
                array_push($users, $newUser);

                saveJson("users.json", $users);
                sendJson($newUser, 201);
                exit();
            }
            //Om alla fälten inte är ifyllda skickas det ut ett felmeddelande.
        } else {
            $json = json_encode(["message" => "Bad Request - (all the fields has to be filled)"]);
            sendJson($json, 400);
            exit();
        }
        //Om formatet inte är JSON skickas det ut ett felmeddelande.
    } else {
        sendJson(["message" => "Bad Request."], 400);
    }
    //Om GET inte är POST skickas det ut ett felmeddelande. 
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
    exit();
}
