<?php
    ini_set('display_errors', 1);    
    
    /*Add parking vélo*/
    $bulk = new MongoDB\Driver\BulkWrite;        
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');    
        
    $velo_url="http://www.velostanlib.fr/service/carto";    
    $continu = true;    
    
    while($continu==true){
        //Import pour les informations des vélos en XML
        $content_velo = getXML($velo_url);
        $xml_velo = simplexml_load_string($content_velo);                   

        $add_marker=null;

        //Pour chacun ou prend des informations détaillées
        for($i=0;$i<sizeof($xml_velo->markers->marker);$i++){
            $content_marker = getXML("http://www.velostanlib.fr/service/stationdetails/nancy/".$xml_velo->markers->marker[$i]['number']);
            $xml_marker = simplexml_load_string($content_marker);       
                        
            //Ajout dans la base
            $parking_info=array(
                "_id"=>new MongoDB\BSON\ObjectId,
                "name"=>strval($xml_velo->markers->marker[$i]['name']),
                "lat"=>strval($xml_velo->markers->marker[$i]['lat']),
                "lng"=>strval($xml_velo->markers->marker[$i]['lng']),
                "available"=>strval($xml_marker->available),
                "free"=>strval($xml_marker->free),
                "comments"=>array()
            );
            $bulk->insert($parking_info);
        }
        $result = $manager->executeBulkWrite('ville.nancy.parking_velo', $bulk);        
        
        $console.='addToConsole("Parking bike were add");';                                     
        $continu = false;
    }

    function getXML($url){
        $content = file_get_contents($url);

        // OK
        if(checkCode(200,$http_response_header)){
            return $content;
        }

        //ERROR
        else{            
            if(checkCode(500,$http_response_header)){
                echo '<h1>Impossible d\'accéder au serveur</h1>';                
            }
            else if(checkCode(404,$http_response_header)){
                echo '<h1>Page inaccessible</h1>';
            }
            else if(checkCode(403,$http_response_header)){
                echo '<h1>Acces interdit</h1>';
            }
            $continu = false;
            return null;
        }
    }
    
    function checkCode($code,$header_response){     
        if(explode(" ",$header_response[0])[1]==$code){         
            return true;
        }
        return false;
    }
?>