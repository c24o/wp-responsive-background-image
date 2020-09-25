<?php
/**
 * Create dynamic styles for background images in WordPress to make them
 * responsive.
 * 
 * The class registers different image sizes in WordPress. Using CSS breakpoints
 * and media queries set images with different image sizes through the CSS
 * property background-image.
 */
namespace c24o\WpResponsiveBackgroundImage;
 
class ResponsiveBackground
{
    /**
     * Singleton instance
     */
    static $instance;

    /**
     * Image sizes registered
     */
    private $image_sizes;

    /**
     * Create an instance and register the image sizes in WordPress.
     * 
     * @param array $breakpoints array where the keys are the breakpoint used in
     * the CSS media queries and the values are the WP image size names or
     * arrays with parameters used by the function add_image_size to register
     * the images sizes.
     */
    private function __construct($breakpoints = [])
    {
        $this->image_sizes = [];
        foreach ($breakpoints as $breakpoint => $image_size) {
            // if it its the array with values to register the image size
            if (is_array($image_size)) {
                call_user_func_array('add_image_size', $image_size);
                $this->image_sizes[$breakpoint] = $image_size[0];
            }
            // then the name of the image size is used
            else {
                $this->image_sizes[$breakpoint] = $image_size;
            }
        }
    }

    /**
     * @see constructor
     */
    static public function create($breakpoints = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($breakpoints);
        }

        return self::$instance;
    }

    /**
     * Register the creation of the CSS code in the head with the responsive
     * behaviour for the images.
     * 
     * @param string $css_selector CSS selector to assign the background image.
     * @param mixed $func_get_image a callable to get the image of the current
     * post. The first argument is the the post or its ID and the second argument
     * is the image size for the image.
     */
    public function addBgStyles($css_selector, $func_get_image = 'get_the_post_thumbnail_url')
    {
        // check if there is a post
        if (!get_the_ID()) {
            return;
        }

        // get the images for each size in the breakpoints
        $images = [];
        foreach ($this->image_sizes as $breakpoint => $image_size) {
            $images[$breakpoint] = \call_user_func($func_get_image, get_the_ID(), $image_size);
        }

        // filter images that are not URLs
        $images = array_filter($images, function($image_url) {
            return filter_var($image_url, FILTER_VALIDATE_URL);
        });

        // if there are images to use
        if (!empty($images)) {
            // print the styles
            add_action('wp_head', function() use ($images, $css_selector) {
                echo '<style>';
                foreach ($images as $breakpoint => $image_url) {
                    $css_rule = "{$css_selector} { background-image: url(\"{$image_url}\"); }";
                    // if it is the default size
                    if (0 == $breakpoint) {
                        echo $css_rule;
                    }
                    // if it requires a media query
                    else {
                        echo "@media (min-width: {$breakpoint}px) { $css_rule }";
                    }
                }
                echo '</style>';
            });
        }
    }
}