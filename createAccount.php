<?php
    ini_set('display_errors', 1);     
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    /*Get everything from table*/
    $filter=["login"=>strval($_GET['login'])];
    $options=["projection"=>[]];
    $query = new MongoDB\Driver\Query($filter,$options);

    $result = $manager->executeQuery('ville.users', $query)->toArray();
        
    if(sizeof($result)==0){
        $users = array(
            '_id'=>new MongoDB\BSON\ObjectId,
            'login'=>strval($_GET['login']),
            'psw'=>strval($_GET['psw']),
        );
    
        $bulk = new MongoDB\Driver\BulkWrite;
        
        $bulk->insert($users);
    
        $result = $manager->executeBulkWrite('ville.users', $bulk);
        echo json_encode(array('user'=>strval($_GET['login'])));
    }else{
        echo json_encode(array('user'=>null));
    }
?>