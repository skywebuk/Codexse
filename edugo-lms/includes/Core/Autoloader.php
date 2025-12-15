<?php
/**
 * PSR-4 Autoloader for Edugo LMS.
 *
 * @package Edugo_LMS\Core
 */

namespace Edugo_LMS\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Autoloader
 *
 * Handles automatic loading of plugin classes following PSR-4 standards.
 *
 * @since 1.0.0
 */
class Autoloader {

    /**
     * Namespace prefix for the plugin.
     *
     * @var string
     */
    private const NAMESPACE_PREFIX = 'Edugo_LMS\\';

    /**
     * Base directory for the namespace prefix.
     *
     * @var string
     */
    private static string $base_dir;

    /**
     * Register the autoloader.
     *
     * @since 1.0.0
     * @return void
     */
    public static function register(): void {
        self::$base_dir = EDUGO_LMS_PATH . 'includes/';
        spl_autoload_register( array( __CLASS__, 'autoload' ) );
    }

    /**
     * Autoload callback function.
     *
     * @since 1.0.0
     * @param string $class The fully-qualified class name.
     * @return void
     */
    public static function autoload( string $class ): void {
        // Check if the class uses our namespace prefix.
        $prefix_length = strlen( self::NAMESPACE_PREFIX );
        if ( strncmp( self::NAMESPACE_PREFIX, $class, $prefix_length ) !== 0 ) {
            return;
        }

        // Get the relative class name.
        $relative_class = substr( $class, $prefix_length );

        // Convert namespace separators to directory separators.
        $file = self::$base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

        // If the file exists, require it.
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }

    /**
     * Get all class files in a directory.
     *
     * @since 1.0.0
     * @param string $directory The directory to scan.
     * @return array Array of class file paths.
     */
    public static function get_class_files( string $directory ): array {
        $files = array();

        if ( ! is_dir( $directory ) ) {
            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $directory, \RecursiveDirectoryIterator::SKIP_DOTS ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $iterator as $item ) {
            if ( $item->isFile() && $item->getExtension() === 'php' ) {
                $files[] = $item->getPathname();
            }
        }

        return $files;
    }
}
