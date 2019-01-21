<?php
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $restaurants = array(
        "_id"=>new MongoDB\BSON\ObjectId,
        "name"=>"BURGER KEBAB",
        "telephone"=>"09 82 59 71 94",
        "addresse"=>"6 Avenue Foch, 54000 Nancy",
        "type"=>"fast food",
        "site"=>"https://www.omalo.fr/",
        "horaires"=>array(
            "dimanche"=>array(
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),     
            ),
            "lundi"=>array(                
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
            "mardi"=>array(                
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
            "mercredi"=>array(             
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
            "jeudi"=>array(                
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
            "vendredi"=>array(                
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
            "samedi"=>array(                
                "periode"=>array(
                    "debut"=>"10:00",
                    "fin"=>"01:00",
                ),
            ),
        ),       
        "lat"=>48.689558, 
        "lng"=>6.176020,
        "comments"=>array(
            array(
                "_id"=>new MongoDB\BSON\ObjectId,
                "from"=>"Jean",
                "message"=>"Super restaurant !",
            ),
        ),
    );

    $bulk->insert($restaurants);

    $result = $manager->executeBulkWrite('ville.nancy.restaurants', $bulk);
    if(isset($console)){
        $console.='addToConsole("Restaurants were add");';
    }
?>