<?php      
    ini_set('display_errors', 1); 
    
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    
    $bulk->update(
        array("_id"=>new MongoDB\BSON\ObjectId(strval($_GET['objId']))),
        array('$push'=>array(
            "comments"=>array(
                "_id"=>new MongoDb\BSON\ObjectId,
                "from"=>strval($_GET['user']),
                "message"=>strval($_GET['message'])
            ))
        )
    );

    $manager->executeBulkWrite('ville.nancy.'.$_GET['table'], $bulk);        
?>