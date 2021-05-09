<?php 
    $file = fopen("log.csv", "a");
    $ip = $_POST["ip"];
    $country = $_POST["country"];
    $region = $_POST["region"];
    $regionName = $_POST["regionName"];
    $city = $_POST["city"];
    $line = $ip.",".$country.",".$region.",".$regionName.",".$city."\n";
    fwrite($file, $line);

    fclose($file);
?>