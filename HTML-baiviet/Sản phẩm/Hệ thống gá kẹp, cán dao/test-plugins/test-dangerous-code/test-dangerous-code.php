<?php
/**
 * Plugin Name: Test Dangerous Code
 * Description: Plugin có dangerous code patterns - sẽ bị reject bởi security scan
 * Version: 1.0.0
 * Author: Anmi Test
 */

// This plugin contains dangerous patterns that should be detected

// Pattern 1: eval
function dangerous_eval() {
    $code = 'echo "This is dangerous";';
    eval($code);
}

// Pattern 2: base64_decode (often used in malware)
function dangerous_decode() {
    $encoded = base64_encode('malicious code');
    $decoded = base64_decode($encoded);
}

// Pattern 3: exec
function dangerous_exec() {
    if (function_exists('exec')) {
        exec('whoami');
    }
}

// Pattern 4: system
function dangerous_system() {
    if (function_exists('system')) {
        system('ls -la');
    }
}
