<?php
ini_set('session.gc_maxlifetime', 10 * 365 * 24 * 60 * 60); // 10 years
ini_set('session.cookie_lifetime', 10 * 365 * 24 * 60 * 60); // 10 years 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
