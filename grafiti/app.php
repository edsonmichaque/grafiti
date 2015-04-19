<?php
  require __DIR__ . '/../bootstrap.php';

  $schemas = array(
    'sms'     => __DIR__ . '/schemas/sms',
    'contact' => __DIR__ . '/schemas/contact'
  );

  $inject[] = $schemas;

  $app->group('/api', function() use ($app, $inject) {
    $app->group('/sms', function() use ($app, $inject) {

      $app->post('/', function() use ($app, $inject) {
        $schema = $inject['fetcher']->retrieve('file:///' . realpath('./schemas/sms/create.json'));
        $sms = $app->request->getBody();

        $inject['validator']->check(json_decode($sms), $schema);

        if ($inject['validator']->isValid()) {
          $esParams = array(
            'index' => 'api',
            'type'  => 'sms',
            'body'  => $sms
          );

          $ret = $inject['es']->index($esParams);
          print_r($ret);
        } else {

          foreach ($inject['validator']->getErrors() as $error) {
            echo sprintf("[%s] %s\n", $error['property'], $error['message']);
          }
          echo 'not valid';
        }

      });

    });

  });

  $app->run();
?>
