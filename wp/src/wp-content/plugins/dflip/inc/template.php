<?php
/**
 * dFlip _
 *
 * it does _
 *
 * @since 1.0.0
 *
 * @package dFlip Book
 * @author  Deepak Ghimire
 */

class DFlip_{

    /**
     * Holds the singleton class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Holds the base DFlip class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        // Load the base class object.
        $this->base = DFlip::get_instance();

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object dFlip_PostType object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DFlip_ ) ) {
            self::$instance = new DFlip_();
        }

        return self::$instance;

    }
}

// Load the _ class.
$dflip_ = DFlip_::get_instance();

