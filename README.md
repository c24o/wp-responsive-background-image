# wp-responsive-background-image
Create CSS background-image rules dynamically for background images in WordPress to make them responsive.


# Getting Started
WordPress uses the field featured image to link a representative image with an article. Sometimes this image is used in the header of the website when you visit the article. Using this library, you can easily use the featured images with the CSS rule *background-image* in a responsive way. The next solution can be used too if the image is not in the featured image field but in a custom field of the article or in another source.

# Installation
In your theme files or in your plugin files, you can use composer to install the library:

```
composer require c24o/wp-responsive-background-image
```

# Usage

First you need to create an object with the configuration of the WordPress images sizes and the CSS breakpoints for each one:

```
ResponsiveBackground::create([
    '0' => ['header-image-xs', 0, 122],
    '576' => ['header-image-sm', 0, 150],
    '768' => ['header-image-md', 0, 200],
    '992' => ['header-image-lg', 0, 300],
    '1200' => ['header-image-xl', 0, 372],
]);
```

The library uses a mobile-first approach which means that `@media (min-width: ...)` is used. A breakpoint for an image means that the image should be used when the width of the viewport is at least as wide as the breakpoint.

The values in the configuration array are the parameters used by the function [add_image_size](https://developer.wordpress.org/reference/functions/add_image_size/). If the image size is already registered, then use the name of the image size only:

```
ResponsiveBackground::create([
    '0' => 'header-image-xs',
    '576' => 'header-image-sm',
    '768' => 'header-image-md',
    '992' => 'header-image-lg',
    '1200' => 'header-image-xl',
]);
```

To print the CSS code with the responsive rules in the head of the page, then use the WordPress hook [wp](https://developer.wordpress.org/reference/hooks/wp/). This hook is executed before printing the head but after loading the global post:

```
add_action('wp', function() {
    // CSS selctor for the element with the background images
    $css_selector = '.site-content .header-image-container';

    // print the bg styles
    ResponsiveBackground::create()->addBgStyles($css_selector);
});
```

By default, the function [get_the_post_thumbnail_url](https://developer.wordpress.org/reference/functions/get_the_post_thumbnail_url/) is used to get the images. A custom function can be used too:

```
function get_my_image($post_id, $size) {
    // return URL of the image
}

add_action('wp', function() {
    // CSS selctor for the element with the background images
    $css_selector = '.site-content .header-image-container';

    // print the bg styles
    ResponsiveBackground::create()->addBgStyles($css_selector, 'get_my_image');
});
```
# The result

The library adds in the <head> section of the page CSS rules like:

```
<style>
.site-content .header-image-container {
    background-image: url("http://localhost:8080/wp-content/uploads/2020/08/welcome-header-image-e1598034101966-419x122.png");
}
@media (min-width: 576px) {
    .site-content .header-image-container {
        background-image: url("http://localhost:8080/wp-content/uploads/2020/08/welcome-header-image-e1598034101966-515x150.png");
    }
}
@media (min-width: 768px) {
    .site-content .header-image-container {
        background-image: url("http://localhost:8080/wp-content/uploads/2020/08/welcome-header-image-e1598034101966-686x200.png");
    }
}
@media (min-width: 992px) {
    .site-content .header-image-container {
        background-image: url("http://localhost:8080/wp-content/uploads/2020/08/welcome-header-image-e1598034101966-1029x300.png");
    }
}
@media (min-width: 1200px) {
    .site-content .header-image-container {
        background-image: url("http://localhost:8080/wp-content/uploads/2020/08/welcome-header-image-e1598034101966-1277x372.png");
    }
}
</style>
```

# License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.