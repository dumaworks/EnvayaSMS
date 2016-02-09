<?php

ini_set('display_errors','0');

/*
 * Read password from json file `secret.json' in current directory
 * This password must match the password in the EnvayaSMS app settings,
 * otherwise example/www/gateway.php will return an "Invalid request signature" error.
 */

$secret = json_decode(file_get_contents(dirname(__FILE__) . '/secret.json'), true);
$PASSWORD = $secret['PASSWORD'];

/*
 * example/send_sms.php uses the local file system to queue outgoing messages
 * in this directory.
 */

$OUTGOING_DIR_NAME = __DIR__."/outgoing_sms";

/*
 * AMQP allows you to send outgoing messages in real-time (i.e. 'push' instead of polling).
 * In order to use AMQP, you would need to install an AMQP server such as RabbitMQ, and 
 * also enter the AMQP connection settings in the app. (The settings in the EnvayaSMS app
 * should use the same vhost and queue name, but may use a different host/port/user/password.)
 */

$AMQP_SETTINGS = array(
    'host' => 'localhost',
    'port' => 5672,
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => '/',
    'queue_name' => "envayasms"
);
