<?php

  require 'include.php';

  use Symfony\Component\Yaml\Yaml;

  class ESConfigParser {
    private $opt = null;

    public function __construct($db) {
      $this->opt = Yaml::parse(__DIR__ . '/../config/' . $db);
    }

    public function options() {
      return $this->opt;
    }

    public function url() {
      $opt = $this->opt;
      $url = $opt['protocol'] . '://' . $opt['user'];
      $url .= ':' . $opt['password'];
      $url .= '@' . $opt['host'] . ':443';

      return $url;
    }
  }

?>
