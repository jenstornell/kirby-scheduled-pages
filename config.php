<?php
Kirby::plugin('jenstornell/schedule', [
  'options' => [
    'active' => true
  ],
  'hooks' => [
      'route:after' => function ($route, $path, $method, $result) {
          if(!isset($result) || !property_exists($result, 'content')) return;
          if(!option('jenstornell.schedule.active')) return;

          $timestamp = strtotime($result->content()->schedule_until());

          if($timestamp && $timestamp > time()) {
              header('HTTP/1.0 404 Not Found');
              echo page((string)site()->errorPage())->render();
              die;
          }
      }
  ],
  'pageMethods' => [
      'isScheduled' => function() {
          $timestamp = strtotime($this->content()->schedule_until());
          if($timestamp && $timestamp > time())
              return true;
          return false;
      }
  ],
  'pagesMethods' => [
      'scheduled' => function($match = true) {
          if($match)
              return $this->filterBy('scheduled', true);
          return $this->filterBy('scheduled', '!=', true);
      }
  ],
  'collections' => [
      'scheduled' => function($site) {
          return $site->index()->scheduled();
      }
  ],
  'fields' => [
      'schedule' => include kirby()->root('kirby') . '/config/fields/date.php'
  ]
]);