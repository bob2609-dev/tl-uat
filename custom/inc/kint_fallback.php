<?php
/**
 * Kint Fallback for PHP 8 Compatibility
 */

// Check if Kint class already exists (avoid redefinition)
if (!class_exists('Kint')) {
    class Kint {
        public static function dump() { return null; }
        public static function trace() { return null; }
        // Add other methods as needed
    }
}
