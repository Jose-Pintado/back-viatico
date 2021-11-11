<?php
use Psr\Container\ContainerInterface;

$container -> set('bd' , function (ContainerInterface $c){
 
   try
   {   
        $dbHost = $_ENV['DB_HOST'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASSWORD'];
        $dbName = $_ENV['DB_DB'];
        $dbtype = $_ENV['DB_TYPE'];
        $dbport = $_ENV['DB_PORT'];
        $dbcollation=$_ENV['DB_COLLATION'];
        $con = $dbtype . ":host=" . $dbHost . ";dbname=" . $dbName . ";port=" . $dbport;
        $con = new PDO($con, $dbUser, $dbPass);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
   }catch (PDOException $e){
      print "Error!!!!".$e->getMessage()."<br>";
      die();
   }
   return $con ;
});

