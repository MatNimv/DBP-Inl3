<?php
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
    //Kollar att alla fälten är ifyllda
    if (isset($rqstData["first_name"], $rqstData["last_name"], $rqstData["gender"], $rqstData["age"])) {
        $firstName = $rqstData["first_name"];
        $lastName = $rqstData["last_name"];
        $gender = $rqstData["gender"];
        $age = $rqstData["age"];

        $check = true;

        if (strlen($firstName) < 2 || strlen($lastName) < 2 || strlen($gender) < 1 || $age < 13) {
            $check = false;
        }

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
        } else {
            $json = json_encode(
                [
                    "message" => "Bad Request - (check the fields once again)",
                    "first_name" => "Has to be more than 2 characters",
                    "last_name" => "Has to be more than 2 characters",
                    "gender" => "Has to be more than 2 characters",
                    "age" => "You can not be younger than 13"

                ]
            );
            sendJson($json, 400);
        }
    } else {
        $json = json_encode(["message" => "Bad Request - (all the fields has to be filled)"]);
        sendJson($json, 400);
    }
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
}
