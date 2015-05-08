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
        $page = $page === null ? $page : 1;
      /*  $from = $app->request->params('source')
        $destination= $app->request->params('destination')



        $query = null;
        $filter = array();

        if ($word) {
          $query = array(
            'query' => array(
              'match_all' => new \stdClass
            )
          );
        } else {
          $query = array(
            'query' => array(
              'match' => array(
                'content' => $word
              )
            )
          );
        }

        if ($from !== null) {
          $filter[] = array('term' => array('source_number' => $from));
        }

        if ($destination !== null) {
          $filter[] = array('term' => array('destination_number' => $from));
        }*/

        $size = 20;

        $body = null;

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
        $esParams = array(
          'index' => 'api',
          'type' => 'sms',
          'body' => json_encode($body)
        );

        $results = $inject['es']->search($esParams);
        $response = $results['hits']['hits'];


        $body = array();
        $no_pages = ceil((int)$results['hits']['total'] / $size);

        $pg = array();
        for($i = 1; $i < $no_pages + 1; $i++) {
          $pg[] = array(
            "page" => "$i",
            "url" => "http://localhost:1234/app.php/api/sms?page=$i"
          );
        }

        $sms = array();
        foreach($response as $result) {
          $tmp = $result['_source'];
          $tmp['id'] = $result['_id'];
          array_push($sms, $tmp);
        }
        $body['messages'] = $sms;
        //$body['pages'] = array(
        //  'all' => $pg,
        //  'current' => "http://localhost:1234/app.php/api/sms?page=$page"
        //);

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
