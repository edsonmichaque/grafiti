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
        $smsJson = $app->request->getBody();
        $smsArray = json_decode($smsJson, true);

        $hashtags = $inject['tag']->tag('#', $smsArray['content']);
        $locations = $inject['tag']->tag('@', $smsArray['content']);

        $sms = json_decode($smsJson, true);

        if (!empty($hashtags)) {
          $sms['tags'] = $hashtags;
        }

        if (!empty($locations)) {
          $sms['locations'] = $locations;
        }

        echo 'x';
        $inject['validator']->check(json_decode($smsJson), $schema);

        if ($inject['validator']->isValid()) {
          $esParams = array(
            'index' => 'api',
            'type'  => 'sms',
            'body'  => json_encode($sms)
          );

          $ret = $inject['es']->index($esParams);
          $app->response->setStatus(201);
        } else {
          $badRequest = array('code' => 400, 'description' => 'Bad request');
          $app->response->headers->set('Content-Type', 'application/json');
          $app->response->setBody(json_encode($badRequest));
        }

      });

      // GET /api/sms
      $app->get('/', function() use ($app, $inject) {
        $word = $app->request->params('q');
        $page = $app->request->params('page');
        $size = 1000;

        // SMS dates
        // $from_date = $app->request->params('from_date');
        // $to_date = $app->request->params('to_date');

        // SMS numbers
        // $from_number = $app->request->params('from_number');
        // $to_number = $app->request->params('to_number');

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
            ),
            'sort' => array('date_received' => array('order' => 'desc'))
          );
        } else {
          $body = array(
            'from' => $page,
            'size' => $size,
            'query' => array(
              'match_all' => new \stdClass
            ),
            'sort' => array('date_received' => array('order' => 'desc'))
          );
        }
/*
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

*/
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

      $app->get('/hashtag/:tag', function($tag) use ($app, $inject) {
        $body = array('query' => array('match' => array('tags' => $tag)));

        $es = array(
          'index' => 'api',
          'type' => 'sms',
          'body' => $body
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
        $body = array();

        if (!empty($sms)) {
          $body['messages'] = $sms;
          $app->response->headers->set('Content-Type', 'application/json');
          $app->response->setBody(json_encode($body));
        } else {
          $app->response->setStatus(404);
        }
      });

      $app->get('/location/:tag', function($tag) use ($app, $inject) {
        $body = array('query' => array('match' => array('locations' => $tag)));

        $es = array(
          'index' => 'api',
          'type' => 'sms',
          'body' => $body
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

        if (!empty($sms)) {
          $body['messages'] = $sms;
          $app->response->headers->set('Content-Type', 'application/json');
          $app->response->setBody(json_encode($body));
        } else {
          $app->response->setStatus(404);
        }
      });

    });

  });

  $app->run();
?>
