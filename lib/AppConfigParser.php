<?php
  require 'include.php';

  use Symfony\Component\Yaml\Yaml;

  class AppconfigParser {
    private $opt = null;

    public function __construct() {
      $this->opt = Yaml::parse(__DIR__ . '/../config/app.yml');
    }

    public function options() {
      return $this->opt;
    }
  }

?>
