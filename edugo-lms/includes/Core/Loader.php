<?php
/**
 * Hook Loader Class.
 *
 * @package Edugo_LMS\Core
 */

namespace Edugo_LMS\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Loader
 *
 * Registers all actions and filters for the plugin.
 *
 * @since 1.0.0
 */
class Loader {

    /**
     * Array of actions registered with WordPress.
     *
     * @var array
     */
    protected array $actions = array();

    /**
     * Array of filters registered with WordPress.
     *
     * @var array
     */
    protected array $filters = array();

    /**
     * Add a new action to the collection.
     *
     * @since 1.0.0
     * @param string $hook          The name of the WordPress action.
     * @param object $component     The object containing the callback.
     * @param string $callback      The callback method name.
     * @param int    $priority      Optional. Priority of the action. Default 10.
     * @param int    $accepted_args Optional. Number of arguments. Default 1.
     * @return void
     */
    public function add_action( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter to the collection.
     *
     * @since 1.0.0
     * @param string $hook          The name of the WordPress filter.
     * @param object $component     The object containing the callback.
     * @param string $callback      The callback method name.
     * @param int    $priority      Optional. Priority of the filter. Default 10.
     * @param int    $accepted_args Optional. Number of arguments. Default 1.
     * @return void
     */
    public function add_filter( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a hook to the collection.
     *
     * @since 1.0.0
     * @param array  $hooks         The collection of hooks.
     * @param string $hook          The name of the hook.
     * @param object $component     The object containing the callback.
     * @param string $callback      The callback method name.
     * @param int    $priority      Priority of the hook.
     * @param int    $accepted_args Number of arguments.
     * @return array Modified collection of hooks.
     */
    private function add( array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args ): array {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );

        return $hooks;
    }

    /**
     * Register all actions and filters with WordPress.
     *
     * @since 1.0.0
     * @return void
     */
    public function run(): void {
        foreach ( $this->filters as $hook ) {
            add_filter(
                $hook['hook'],
                array( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ( $this->actions as $hook ) {
            add_action(
                $hook['hook'],
                array( $hook['component'], $hook['callback'] ),
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }

    /**
     * Remove an action from the collection.
     *
     * @since 1.0.0
     * @param string $hook     The name of the WordPress action.
     * @param object $component The object containing the callback.
     * @param string $callback  The callback method name.
     * @return bool True if removed, false otherwise.
     */
    public function remove_action( string $hook, object $component, string $callback ): bool {
        return $this->remove( $this->actions, $hook, $component, $callback );
    }

    /**
     * Remove a filter from the collection.
     *
     * @since 1.0.0
     * @param string $hook     The name of the WordPress filter.
     * @param object $component The object containing the callback.
     * @param string $callback  The callback method name.
     * @return bool True if removed, false otherwise.
     */
    public function remove_filter( string $hook, object $component, string $callback ): bool {
        return $this->remove( $this->filters, $hook, $component, $callback );
    }

    /**
     * Remove a hook from the collection.
     *
     * @since 1.0.0
     * @param array  $hooks     The collection of hooks (passed by reference).
     * @param string $hook      The name of the hook.
     * @param object $component The object containing the callback.
     * @param string $callback  The callback method name.
     * @return bool True if removed, false otherwise.
     */
    private function remove( array &$hooks, string $hook, object $component, string $callback ): bool {
        foreach ( $hooks as $key => $registered_hook ) {
            if (
                $registered_hook['hook'] === $hook &&
                $registered_hook['component'] === $component &&
                $registered_hook['callback'] === $callback
            ) {
                unset( $hooks[ $key ] );
                return true;
            }
        }

        return false;
    }
}
