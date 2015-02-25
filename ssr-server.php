<?php
require 'vendor/autoload.php';
require 'bootstrap.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use SSRServer\SSRServer;

$ssr = new SSRServer(new \SSRServer\Database\Neo(new Everyman\Neo4j\Client('localhost', 7474)));

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $ssr
        )
    ),
    8080
);

$server->run();