<?php
/**
 * Plugin Name: Test Fatal Error Plugin
 * Description: Plugin test để simulate fatal error - CHỈ DÙNG CHO TEST
 * Version: 1.0.0
 * Author: Anmi Test
 */

// WARNING: Plugin này sẽ gây fatal error khi activate
// Dùng để test watchdog recovery

// Uncomment dòng dưới để trigger fatal error
// This will cause a fatal error
trigger_error('Simulated Fatal Error for Testing Watchdog', E_USER_ERROR);

// Alternative: syntax error
// eval('this is not valid PHP code');

// Alternative: call undefined function
// undefined_function_that_does_not_exist();
