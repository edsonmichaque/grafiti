<?php
  require 'export.php';

  $app = new AppconfigParser;
  $appConfig = $app->options();

  $db = new ESConfigParser($appConfig['db']['config']);


  $dbConfig = $db->options();

  echo $appConfig['name'] . "\n";
  echo $appConfig['http']['version'] . "\n";
  echo $appConfig['mode'] . "\n";
  echo $appConfig['db']['config'] . "\n";

  echo $dbConfig['user'] . "\n";
  echo $dbConfig['host'] . "\n";
  echo $dbConfig['port'] . "\n";
  echo $dbConfig['protocol'] . "\n";
  echo $dbConfig['password'] . "\n";
  echo $db->url();
?>
