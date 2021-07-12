<?php

require_once './../vendor/autoload.php';

$file = './../test/proto/controllers/home.proto';
$file = realpath($file);

$content = file_get_contents($file);

$parserToArr = new \ProtoParser\ProtoToArray($content);
$parser      = new \ProtoParser\ProtoParser();

$data = $parser->parser($parserToArr);
print_r($data);