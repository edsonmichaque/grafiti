<?php

  require 'include.php';

  class Tag {

    public function __construct() {

    }

    public function tag($char, $str) {
      preg_match_all("/(^|[^a-z0-9])$char([a-z0-9_])+/i", $str, $matches);

      $hashes = array();

      if (!empty($matches[0])) {
        foreach($matches as $match) {
          array_push($hashes, preg_replace('/[^a-z0-9_]/i', "", $match));
        }
      }

      return $hashes[0];
    }
  }

?>
