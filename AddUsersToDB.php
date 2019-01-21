<?php
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $users = array(
        '_id'=>new MongoDB\BSON\ObjectId,
        'login'=>'User',
        'psw'=>'test',
    );

    $bulk->insert($users);

    $result = $manager->executeBulkWrite('ville.users', $bulk);
    if(isset($console)){
        $console.='addToConsole("Users were add");';
    }
?>