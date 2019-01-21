<?php
session_start();
ini_set('display_errors', 1);    

$console = "";
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

/*Get everything from table*/
$filter=[];
$options=["projection"=>[]];
$query = new MongoDB\Driver\Query($filter,$options);

$resultUsers = $manager->executeQuery('ville.users',$query)->toArray();
$resultParkingVelo = $manager->executeQuery('ville.nancy.parking_velo',$query)->toArray();
$resultParking = $manager->executeQuery('ville.nancy.parking',$query)->toArray();
$resultRestaurant = $manager->executeQuery('ville.nancy.restaurants',$query)->toArray();


//Foreach table
//If do not exist add table
if(sizeof($resultUsers)==0){
    include('./AddUsersToDB.php');    
}else{
    unset($resultUsers);
}

if(sizeof($resultParkingVelo)==0){
    include('./AddParkingVeloToDB.php');
    $resultParkingVelo = $manager->executeQuery('ville.nancy.parking_velo',$query)->toArray();
}
if(sizeof($resultParking)==0){
    include('./AddParkingToDB.php');
    $resultParking = $manager->executeQuery('ville.nancy.parking',$query)->toArray();
}
if(sizeof($resultRestaurant)==0){
    include('./AddRestaurantsToDB.php');
    $resultRestaurant = $manager->executeQuery('ville.nancy.restaurants',$query)->toArray();
}

$add_marker=null;

//Create markers
foreach($resultParkingVelo as $row){
    $add_marker.='addMarkerParkingVelo("'.$row->name.'",'.$row->lat.','.$row->lng.','.$row->available.','.$row->free.',"'.$row->_id.'");';
}
foreach($resultParking as $row){    
    if($row->places!=null){
        $places = $row->places;
    }else{
        $places = 0;
    }
    $add_marker.='addMarkerParking("'.$row->name.'",'.$row->lat.','.$row->lng.','.$row->capacity.','.$places.',true,"'.$row->_id.'");';
}
foreach($resultRestaurant as $row){    
    $add_marker.='addMarkerRestaurants("'.$row->name.'",'.$row->lat.','.$row->lng.',"'.$row->telephone.'","'.$row->addresse.'",true,"'.$row->_id.'");';
}

echo '<HTML>
                <head>                
                    <link rel="stylesheet" href="markers.css"/>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
                    integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
                    crossorigin=""/>                     
                    <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
                    integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
                    crossorigin=""></script> 
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">                                        
                </head>
                <body>
                    <div class="flex-col align-center" id="connexion">';
                        
                    if(!isset($_SESSION['user'])){
                        echo'<h2>Connectez vous ou créez un compte pour pouvoir poster des commentaires</h2>
                        <input id="edtlogin"/>
                        <input type="password" id="edtpsw"/>
                        <div class="flex-row">
                            <button onclick="tryToConnect(\'connexion\')">Connexion</button>   
                            <button onclick="tryToConnect(\'createAccount\')">Créer mon compte</button>
                        </div>';
                    }else{
                        echo '<h1>Bienvenu'.strval($_SESSION['user']).'</h1><button onclick="disconnection()">Déconnexion</button>';
                    }

                    echo '</div>
                    <div class="map-container">
                        <div class="left-banner">
                            <div><label>Parkings vélo</label><input id="park_velo" type="checkbox" checked/></div>
                            <div><label>Parkings</label><input id="parking" type="checkbox"/></div> 
                            <div><label>Restaurants</label><input id="restaurant" type="checkbox"/></div>  
                        </div>
                        <div style="height:500px;width:500px;" id="mapid"></div>
                        <div class="right-banner">
                            <div id="detail_info"></div>
                        </div>                                                
                    </div>
                    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
                    <script src="map.js"></script>
                    <script>
                        initmap();
                        '.$console.$add_marker.'
                    </script>                        
                </body>
            </HTML>'; 
?>