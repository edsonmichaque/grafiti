<?php
  require __DIR__ . '/../bootstrap.php';

  $schemas = array(
    'sms'     => __DIR__ . '/schemas/sms',
    'contact' => __DIR__ . '/schemas/contact'
  );

  $inject[] = $schemas;

  $app->group('/api', function() use ($app, $inject) {
    $app->group('/sms', function() use ($app, $inject) {

      // POST /api/sms
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

      // GET /api/sms
      $app->get('/', function() use ($app, $inject) {
        $word = $app->request->params('q');
        $page = $app->request->params('page');
        $size = 100;

        // SMS dates
        $from_date = $app->request->params('from_date');
        $to_date = $app->request->params('to_date');

        // SMS numbers
        $from_number = $app->request->params('from_number');
        $to_number = $app->request->params('to_number');

        $query = array();

        // query sms
        if ($word !== null) {
          $body = array(
            'from' => $page,
            'size' => $size,
            'query' => array(
              'match' => array(
                'content' => $word
              )
            )
          );
        } else {
          $body = array(
            'from' => $page,
            'size' => $size,
            'query' => array(
              'match_all' => new \stdClass
            )
          );
        }



        // query numbers
        if ($from_number !== null && $from_number !== null) {

        } else if ($from_number !== null) {

        } else {

        }

        // query dates
        if ($from_number !== null && $from_number !== null) {

        } else if ($from_number !== null) {

        } else {

        }


        $es = array(
          'index' => 'api',
          'type' => 'sms',
          'body' => json_encode($body)
        );

        $results = $inject['es']->search($es);
        $response = $results['hits']['hits'];

        $sms = array();

        foreach($response as $result) {
          $tmp = $result['_source'];
          $tmp['id'] = $result['_id'];
          array_push($sms, $tmp);
        }

        $body = array();
        $body['messages'] = $sms;

        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody(json_encode($body));
      });

      $app->get('/:id', function($id) use ($app, $inject) {
        $esParam = array(
          'index' => 'api',
          'type' => 'sms',
          'id' => $id
        );



        $r = $inject['es']->get($esParam);
        $body = $r['_source'];
        $body['id'] = $r['_id'];

        $app->response->setBody(json_encode($body));
      });

      $app->delete('/:id', function($id) use ($app, $inject) {
        $esParam = array(
          'index' => 'api',
          'type' => 'sms',
          'id' => $id
        );



        $r = $inject['es']->delete($esParam);
        $body = $r['_source'];
        $body['id'] = $r['_id'];

        $app->response->setBody(json_encode($body));
      });

      $app->get('/:id', function($id) use ($app, $inject) {
        $esParam = array(
          'index' => 'api',
          'type' => 'sms',
          'id' => $id
        );



        $r = $inject['es']->update($esParam);
        $body = $r['_source'];
        $body['id'] = $r['_id'];

        $app->response->setBody(json_encode($body));
      });


    });

  });

  $app->run();
?>
