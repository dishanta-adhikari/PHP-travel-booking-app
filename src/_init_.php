<?php
require_once __DIR__ . '/Config/Session.php';        // start the session

require_once __DIR__ . '/../vendor/autoload.php';    // composer autoload

require_once __DIR__ . '/Config/env.php';            // load .env

require_once __DIR__ . '/Config/constants.php';      // defined constants - APP_URL,AUTH_URL

require_once __DIR__ . '/Config/Database.php';       // returns db $conn

http_response_code(404);
http_response_code(500);
