<?php
require_once "../functions.php";

$allOwners = loadJson("../owners/owners.json");
$allPets = loadJson("pets.json");

?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "GET") {
    //hitta djur basserat på "name"
    if (isset($_GET["name"])) {
        foreach ($allPets as $pet => $pet) {
            if ($pet["name"] == $_GET["name"]) {
                sendJson($allPets[$pet]);
                exit();
            }
        }
    }

    if (isset($_GET["include"])){
        $isIncluded = $_GET["include"];
    
        //om include är true
        $ownerOfPet = null;
        if ($isIncluded == "yes"){
    
            foreach($allPets as $index =>  $pet){
                //tar fram förnamnet av ägaren till Pet:et
                $ownersID = array_column($allOwners, "id");
                $indexOfOwner = array_search($pet["owner"], $ownersID);
                $ownerOfPet = $allOwners[$indexOfOwner];
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
                $updatedPet = [
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
                    array_push($allPets, $updatedPet);
                    saveJson("pets.json", $allPets);
                }
    
            } else { //om det INTE är true i include
                foreach($allPets as $index => $pet){
    
                    $id = $pet["id"];
                    $name = $pet["name"];
                    $species = $pet["species"];
                    $origin = $pet["origin"];
    
                    //får fram ID av ägaren till djuret.
                    $ownersID = array_column($allOwners, "id");
                    $indexOfOwner = array_search($pet["owner"], $ownersID);
                    $ownerOfPet = $allOwners[$indexOfOwner];
                    $ownerOfPetID = $ownerOfPet["id"];
    
                    $updatedPet = [
                        "id" =>   $id,
                        "name" => $name,
                        "species" => $species,
                        "origin" => $origin,
                        "owner" => $ownerOfPetID
                        ];
                    //byter ut den gamla mot den nya Petarrayen.
                    array_splice($allPets, $index, 100);
                    array_push($allPets, $updatedPet);
                    saveJson("pets.json", $allPets);
                }
            }
        }
    //Maximalt antal djur
    if (isset($_GET["limit"])) {
        $limit = $_GET["limit"];
        $slicedOwners = array_slice($allPets, 0, $limit);
        sendJson($slicedOwners);
        exit();
    }

    //hitta djur bassserat på id
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $petsById = [];

        foreach ($allPets as $pet) {
            if ($pet["id"] == $id) {
                $petsById[] = $pet;
            }
            //ifall id inte finns, skicka felmeddelande
        }
        if (count($petsById) == 0) {
            $json = json_encode(["message" => "User does not exist"]);
            sendJson($json, 400);
        }


        sendJson($petsById);
        exit();
    }
    if (isset($_GET["origin"])) {
        $origin = $_GET["origin"];
        $originArray = [];

        foreach ($allPets as $pet) {
            if ($pet["origin"] == $origin) {
                array_push($originArray, $pet);
            }
        } //om det inte finns användare i den specifika åldern.
        if (count($originArray) == 0) {
            sendJson(["message" => "No pet from this origin"]);
        } else { //annars skickar den ut alla med den specifika åldern.
            sendJson($originArray);
        }
    } else { //om inte det finns AGE, skickas alla användare ut ändå.
        sendJson($allPets);
    }
    //Om GET inte är GET skickas det ut ett felmeddelande. 
} else {
    $json = json_encode(["message" => "Method is not allowed!"]);
    sendJson($data, 405);
    exit();
}
?>