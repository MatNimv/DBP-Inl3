<?php

//denna funktion ska skicka ut en JSON till användaren.
//får det som ett meddelande i en asso array, och även
//en http-kod som visar om det gått rätt eller fel.
function sendJson($data, $statusCode = 200)
{
    //innehållet som skickas är i JSON
    header("Content-Type: application/json");

    //statuskoden som kommer in via parametern
    http_response_code($statusCode);

    //ett meddelande till användaren
    $json = json_encode($data);

    echo $json;
    exit();
}

//läsa in JSON-fil, konvertera till asso array
function loadJSON($fileName)
{
    if (file_exists($fileName) == true) {
        //om filen finns (är true) - då
        //gör JSONdatan till en asso array
        $data = file_get_contents("$fileName");
        $dataContent = json_decode($data, true);
        return $dataContent;
    } else {
        return false;
    }
}

//spara en asso array som i en fil, 
//$fileName är filen vi vill lägga in det nya i.
//$data är objektet som sparas in i objektet.
function saveJson($fileName, $data)
{
    //sätter in i $fileName: datan i fint format
    if ( //om file_put_contents blir false skickas false med.
        file_put_contents(
            $fileName,
            json_encode($data, JSON_PRETTY_PRINT)
        ) == false
    ) {
        return false;
    } else {
        return true;
    }
}

//Hittar det högsts id:et
function theHighestId($array)
{
    $userID = 0;
    foreach ($array as $obj) {
        if ($obj["id"] > $userID) {
            $userID = $obj["id"];
        }
    }
    $userID = $userID + 1;
    return $userID;
}

//en funktion som "skriver ut" värdet. Funkar bara i webbläsaren.
function inspect($variable)
{
    echo '<pre>';
    echo var_dump($variable);
    echo '</pre>';
}
