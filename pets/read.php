<?php
require_once "../functions.php";

$allOwners = loadJson("../owners/owners.json");
$allPets = loadJson("pets.json");

?>
<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "GET") {

    if (isset($_GET["include"])){
        $includedArr = [];
        $isIncluded = $_GET["include"];

        //om include är true
        if ($isIncluded == "yes"){
            foreach($allPets as $index =>  $pet){
                //tar fram förnamnet av ägaren till Pet:et
                $ownersID = array_column($allOwners, "id");
                $indexOfOwner = array_search($pet["owner"], $ownersID);
                $ownerOfPet = $allOwners[$indexOfOwner];
                $ownerOfPet = $ownerOfPet["first_name"];

                //variabler till nycklara
                $id = $pet["id"];
                $name = $pet["name"];
                $species = $pet["species"];
                $origin = $pet["origin"];

                //förnyar "djuret" med ytterligare en extranyckel..
                $updatedDog = [
                    "id" =>   $id,
                    "name" => $name,
                    "species" => $species,
                    "origin" => $origin,
                    "owner" => [
                        "first_name" => $ownerOfPet
                        ]
                    ];
                    //byter ut den gamla mot den nya Petarrayen, för tillfället
                    array_push($includedArr, $updatedDog);
                    $allPets = $includedArr;
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

        if(isset($_GET["ids"])){//flera ids än 1.
            $ids = explode(",", $_GET["ids"]);
            $petsById = [];
        
            foreach($allPets as $pet){
                if (in_array($pet["id"], $ids)){
                    $petsById[] = $pet;
                }
            }
            sendJson($petsById);
        }
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $petsById = [];

            foreach ($allPets as $pet) {
                if ($pet["id"] == $id) {
                    $petsById[] = $pet;
                }
            } if(count($petsById)==0){
                $json = json_encode(["message"=>"Pet does not exist"]);
                sendJson($json, 404);
                exit();
            }
            sendJson($petsById);
        }
            if (isset($_GET["origin"])) {
            $origin = $_GET["origin"];
            $originArray = [];
    
            foreach ($allPets as $pet) {
                if ($pet["origin"] == $origin) {
                    array_push($originArray, $pet);
                }
                $allPets = $originArray;
            } //om det inte finns användare från det specifika landet.
            if (count($originArray) == 0) {
                sendJson(["message" => "No pet from this origin."]);
            }
        }
        if (isset($_GET["limit"])){//skickar ut antal som finns i limit
            $limit = $_GET["limit"];
            $slicedPets = array_slice($allPets, 0, $limit);
            sendJson($slicedPets);
        } else {sendJson($allPets);}
    } else {
        $json = json_encode(["message" => "Method is not allowed!"]);
        sendJson($data, 405);
        exit();
    }
?>