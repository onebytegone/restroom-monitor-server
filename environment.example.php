<?php

/*
 * Report all PHP errors
 */
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

/**
 * Set timezone
 */
date_default_timezone_set("America/New_York");

/**
 * Save root directory
 */
define('ROOT_DIR', dirname(__FILE__));

/**
 * This is the key to share between the server and the status updater
 */
define('PRIVATE_SHARED_JWT_KEY', 'correcthorsebatterystaple');
