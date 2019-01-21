<?php
    $bulk = new MongoDB\Driver\BulkWrite;        
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $filter=['login'=>strval($_GET['login']),'psw'=>strval($_GET['psw'])];
    $options=["projection"=>[]];
    $query = new MongoDB\Driver\Query($filter,$options);
    
    $collection = 'ville.users';

    $result = $manager->executeQuery($collection,$query)->toArray();

    if(sizeof($result)!=0){
        $_SESSION['user']=$_GET['login'];    
        echo json_encode(array("user"=>$_GET['login']));      
    }else{
        echo json_encode(array("user"=>null));
    }
?>