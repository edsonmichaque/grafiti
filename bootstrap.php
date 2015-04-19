<?php
  require 'vendor/autoload.php';
  require 'lib/export.php';

  $appParser = new AppConfigParser();

  $appConfig = $appParser->options();

  $esConfig = new ESConfigParser($appConfig['db']['config']);

  $slimParams = array();
  $slimParams['mode'] = $appConfig['mode'];
  $slimParams['debug'] = $appConfig['debug'];
  $slimParams['log.enabled'] = $appConfig['logs']['enabled'];
  $slimParams['http.version'] = $appConfig['http']['version'];

  $esParams = array('hosts' => array($esConfig->url()));

  $app = new \Slim\Slim($slimParams);
  $es = new \Elasticsearch\Client($esParams);
  $fetcher = new \JsonSchema\Uri\UriRetriever;
  $validator = new \JsonSchema\Validator;

  $inject = array(
    'es'        => $es,
    'fetcher'   => $fetcher,
    'validator' => $validator
  );

?>
