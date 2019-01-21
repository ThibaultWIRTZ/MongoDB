<?php
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $json = file_get_contents('https://geoservices.grand-nancy.org/arcgis/rest/services/public/VOIRIE_Parking/MapServer/0/query?where=1%3D1&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=nom%2Cadresse%2Cplaces%2Ccapacite&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=4326&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentsOnly=false&datumTransformation=&parameterValues=&rangeValues=&f=pjson');
    $result = json_decode($json);
    
    $features = $result->features;

    foreach ($features as $parking) {
        $attributes = $parking->attributes;
        $geometry = $parking->geometry;

        $parking_info=array(
            "_id"=>new MongoDB\BSON\ObjectId,
            "name"=>$attributes->NOM,
            "lat"=>$geometry->y,
            "lng"=>$geometry->x,
            "capacity"=>$attributes->CAPACITE,
            "places"=>$attributes->PLACES,
            "comments"=>array(),
        );
        $bulk->insert($parking_info);
    }

    $result = $manager->executeBulkWrite('ville.nancy.parking', $bulk);
    if(isset($console)){
        $console.='addToConsole("Parkings were add");';    
    }
?>