<?php

namespace Amp\Mysql\Test;

const DB_HOST = '127.0.0.1:6033';
const DB_USER = 'root';
const DB_PASS = 'root';

initialize(new \mysqli(DB_HOST, DB_USER, DB_PASS));
