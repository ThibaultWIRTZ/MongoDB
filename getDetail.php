<?php
    ini_set('display_errors', 1);    
    
    $bulk = new MongoDB\Driver\BulkWrite;        
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $filter=["_id"=>new MongoDB\BSON\ObjectId(strval($_GET['objId']))];
    $options=["projection"=>[]];
    $query = new MongoDB\Driver\Query($filter,$options);

    
    $collection = 'ville.nancy.'.$_GET['table'];

    $result = $manager->executeQuery($collection,$query)->toArray();

    echo json_encode($result[0]);
?>