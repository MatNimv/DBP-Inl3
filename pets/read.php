<?php
require_once "../functions.php";

$allUsers = loadJson("../users/users.json");
$allPets = loadJson("pets.json");
?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod === "GET") {

    
//Kunna inkludera relaterade entiteter med parametern include=1, t.ex. om jag haft en hund i 
//form av { name: "Arya", owner: 1 } (där 1 är ett ID) - med denna parametern skulle vi då 
//inkludera relationen så här { name: "Arya", owner: { name: "Sebbe" }}. Denna 
//parameter ska kunna kombineras med andra parametrar. Det är ok om detta bara fungerar för er 
//ena entitet

//om GET skickar med include=true ska alla inkluderade relationer skickas med.
//databasen redigeras, iomed att den ska skickas med oavsett vilka andra parametrar
//som nämns.
if (isset($_GET["include"])){
    $includeIsTrue = [];
    $includeIsNotTrue = [];
    $isIncluded = $_GET["include"];

    //om include är true
    $ownerOfPet = null;
    if ($isIncluded == "yes"){

        foreach($allPets as $index =>  $pet){
            //tar fram förnamnet av ägaren till Pet:et
            $ownersID = array_column($allUsers, "id");
            $indexOfOwner = array_search($pet["owner"], $ownersID);
            $ownerOfPet = $allUsers[$indexOfOwner];
            $ownerOfPet = $ownerOfPet["first_name"];

            //kollar om djuret har en ägare
            if (strlen($ownerOfPet) <= 0 ){
                $ownerOfPet = "No owner";
            }

            //variabler till nycklara
            $id = $pet["id"];
            $name = $pet["name"];
            $species = $pet["species"];
            $origin = $pet["origin"];

            //gör ett nytt "djur" med ytterligare en extranyckel..
            $updatedDog = [
                "id" =>   $id,
                "name" => $name,
                "species" => $species,
                "origin" => $origin,
                "owner" => [
                    "name" => $ownerOfPet
                    ]
                ];
                //byter ut den gamla mot den nya Petarrayen.
                array_splice($allPets, $index, 100);
                array_push($allPets, $updatedDog);
                saveJson("pets.json", $allPets);
            }

        } else { //om det INTE är true i include
            foreach($allPets as $index => $pet){

                $id = $pet["id"];
                $name = $pet["name"];
                $species = $pet["species"];
                $origin = $pet["origin"];

                //får fram ID av ägaren till djuret.
                $ownersID = array_column($allUsers, "id");
                $indexOfOwner = array_search($pet["owner"], $ownersID);
                $ownerOfPet = $allUsers[$indexOfOwner];
                $ownerOfPetID = $ownerOfPet["id"];

                $updatedDog = [
                    "id" =>   $id,
                    "name" => $name,
                    "species" => $species,
                    "origin" => $origin,
                    "owner" => $ownerOfPetID
                    ];
                //byter ut den gamla mot den nya Petarrayen.
                array_splice($allPets, $index, 100);
                array_push($allPets, $updatedDog);
                saveJson("pets.json", $allPets);
            }
        }
    }
}

    //first_name
    if (isset($_GET["name"])) {
        foreach ($allPets as $pet => $pet) {
            if ($pet["name"] == $_GET["name"]) {
                sendJson($allPets[$pet]);
                exit();
            
            }
        }
    }
if (isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    $slicedUsers = array_slice($allPets, 0, $limit);
    sendJson($slicedUsers);
    exit();
   
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $petsById = [];

    foreach ($allPets as $pet) {
        if ($pet["id"] == $id) {
            $petsById[] = $pet;
        }
    } if(count($petsById)==0){
        $json = json_encode(["message"=>"User does not exist"]);
        sendJson($json, 400);
    }
    

    sendJson($petsById);
    exit();
}

sendJson($allPets);
?>