<?php

    /**
     * ReduxFramework Barebones Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     *
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     *
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "burocrate_bourbon";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'burocrate_bourbon',
        'display_name' => 'BOURBON',
        'use_cdn' => TRUE,
        // 'display_version' => 'ver 1.0.0',
        'page_slug' => 'bourbon',
        'page_title' => 'Options',
        'update_notice' => TRUE,
        'dev_mode' => FALSE,
        'admin_bar' => TRUE,
        'menu_type' => 'menu',
        'menu_title' => 'Theme Options',
        'admin_bar_icon'       => 'dashicons-admin-generic',
        'menu_icon'            => 'dashicons-admin-generic',
        'allow_sub_menu' => TRUE,
        'page_parent_post_type' => 'your_post_type',
        'page_priority' => TRUE,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
        'intro_text' => '<a href="http://burocrate.us3.list-manage1.com/subscribe?u=f11d4ce61cc68d2ad7d9f3cef&id=76713dba23">Subscribe</a> to our newsletter about new templates or important updates!'
    );

    // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
    // $args['share_icons'][] = array(
    //     'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
    //     'title' => 'Visit us on GitHub',
    //     'icon'  => 'el el-github'
    //     //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
    // );
    $args['share_icons'][] = array(
        'url'   => 'https://www.facebook.com/burocratecom',
        'title' => 'Like us on Facebook',
        'icon'  => 'el el-facebook'
    );
    $args['share_icons'][] = array(
        'url'   => 'http://twitter.com/burocratecom',
        'title' => 'Follow us on Twitter',
        'icon'  => 'el el-twitter'
    );
    $args['share_icons'][] = array(
        'url'   => 'http://themeforest.net/user/burocrate/portfolio?ref=burocrate',
        'title' => 'Our site',
        'icon'  => 'el el-link'
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    // $tabs = array(
    //     array(
    //         'id'      => 'redux-help-tab-1',
    //         'title'   => __( 'Theme Information 1', 'admin_folder' ),
    //         'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
    //     ),
    //     array(
    //         'id'      => 'redux-help-tab-2',
    //         'title'   => __( 'Theme Information 2', 'admin_folder' ),
    //         'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
    //     )
    // );
    // Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
    Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    /**
     * Information
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Information', 'bourbon' ),
        'id'         => 'opt-information',
        'icon'       => 'fa fa-info',
        'fields'     => array(
            array(
                'id'    => 'bourbon-info-demo',
                'type'  => 'info',
                'style' => 'warning',
                'icon'  => 'fa fa-cog',
                'title' => __( 'IMPORTANT! For right work Template DO NOT ACTIVATE the Redux Plugin demo installation!', 'bourbon' ),
                'desc'  => __( 'Please ignore this message "Redux Framework has an embedded demo"', 'bourbon' )
            ),
            array(
                'id'    => 'bourbon-info',
                'type'  => 'info',
                'style' => 'success',
                'icon'  => 'fa fa-cog',
                'title' => __( 'More settings', 'bourbon' ),
                'desc'  => __( 'You can also use the WordPress Customizer for some standard settings. ( Appearance / Customize )', 'bourbon' )
            ),
        )

    ) );

    /**
     * General
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Global', 'bourbon' ),
        'id'         => 'opt-global',
        'icon'      => 'fa fa-globe',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'       => 'pagination',
                'type'     => 'button_set',
                'title'    => __( 'Pagination', 'bourbon' ),
                'desc'     => __( 'Select Pagination for shows page numbers. Select Navigation for "Next/Previous" buttons only.', 'bourbon' ),
                //Must provide key => value pairs for radio options
                'options'  => array(
                    '1' => 'Pagination',
                    '2' => 'Navigation'
                ),
                'default'  => '1'
            ),
            // array(
            //     'id'       => 'navigation-invert',
            //     'type'     => 'switch',
            //     'title'    => __( 'Invert Navigation Text', 'bourbon' ),
            //     // 'subtitle' => __( 'Invert Posts Navigation Text', 'bourbon' ),
            //     'default'  => false,
            // ),
            array(
                'id'       => 'infinite-scroll',
                'type'     => 'switch',
                'title'    => __( 'Infinite scroll', 'bourbon' ),
                'desc'     => __( 'Will be shown only in Grid Templates.', 'bourbon' ),
                //'options' => array('on', 'off'),
                'default'  => false,
            ),
            array(
                'id'       => 'human-date',
                'type'     => 'switch',
                'title'    => __( 'Human date', 'bourbon' ),
                'subtitle' => __( 'Human date in articles footer', 'bourbon' ),
                'default'  => false,
            ),
            array(
                'id'    => 'general-background-color-info',
                'type'  => 'info',
                'style' => 'success',
                'icon'  => 'fa fa-paint-brush',
                'title' => __( 'Background Color', 'bourbon' ),
                'desc'  => __( 'You can change the background color in the standard WordPress Customizer ( Appearance / Customize / Colors )', 'bourbon' )
            ),
            array(
                'id'    => 'general-background-image-info',
                'type'  => 'info',
                'style' => 'success',
                'icon'  => 'fa fa-picture-o',
                'title' => __( 'Background Image', 'bourbon' ),
                'desc'  => __( 'You can change the background image in the standard WordPress Customizer ( Appearance / Customize / Background )', 'bourbon' )
            ),
        )
    ) );
    /**
     * Buttons
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Buttons', 'bourbon' ),
        'id'         => 'opt-buttons',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'button-background-color',
                'type'     => 'color',
                'title'    => __( 'Background Color', 'bourbon' ),
                'desc'     => __( 'Select Buttons global hover color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'background-color' => '.extralink, .more-link, input.submit, input[type="submit"], .nav-previous a, .nav-next a, .page-numbers, .entry__tags a, .widget_tag_cloud a, .footer-widget.widget_tag_cloud a, .inverse-widget .widget:not(.footer-widget).widget_tag_cloud a, .widget_bourbon_social_widget a i, .woocommerce a.button, .woocommerce button.button, .woocommerce .widget_price_filter .price_slider_amount .button, .woocommerce button.button, .woocommerce #respond input#submit, .widget_tag_cloud a, .widget_product_tag_cloud a,  .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce input.button, .woocommerce #respond input#submit',
                    'border-color' => '.extralink, .more-link, input.submit, input[type="submit"], .nav-previous a, .nav-next a, .page-numbers, .woocommerce a.button, .woocommerce button.button, .woocommerce .widget_price_filter .price_slider_amount .button, .woocommerce button.button, .woocommerce #respond input#submit, .woocommerce input.button, .woocommerce #respond input#submit',
                    ),
            ),
            array(
                'id'       => 'button-text-color',
                'type'     => 'color',
                'title'    => __( 'Text Color', 'bourbon' ),
                'desc'     => __( 'Select Buttons global hover text color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'color' => '.extralink, .more-link, input.submit, input[type="submit"], .nav-previous a, .nav-next a, .page-numbers, .entry__tags a, .widget_tag_cloud a, .widget_bourbon_social_widget a i, .woocommerce a.button, .woocommerce button.button, .woocommerce .widget_price_filter .price_slider_amount .button, .woocommerce button.button, .woocommerce #respond input#submit, .widget_tag_cloud a, .widget_product_tag_cloud a, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce input.button, .woocommerce #respond input#submit',
                    ),
            ),
            array(
                'id'       => 'button-background-color-hover',
                'type'     => 'color',
                'title'    => __( 'Hover Background Color', 'bourbon' ),
                'desc'     => __( 'Select Buttons global hover color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'background-color' => '.extralink:hover, .more-link:hover, input.submit:hover, input[type="submit"]:hover, .nav-previous a:hover, .nav-next a:hover, .page-numbers:hover, .entry__tags a:hover, .widget_tag_cloud a:hover, .footer-widget.widget_tag_cloud a:hover, .inverse-widget .widget:not(.footer-widget).widget_tag_cloud a:hover, .widget_bourbon_social_widget a:hover i, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce button.button:hover, .woocommerce #respond input#submit:hover, .widget_tag_cloud a:hover, .widget_product_tag_cloud a:hover,  .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit:hover',
                    'border-color' => '.extralink:hover, .more-link:hover, input.submit:hover, input[type="submit"]:hover, .nav-previous a:hover, .nav-next a:hover, .page-numbers:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce button.button:hover, .woocommerce #respond input#submit:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit:hover, .widget_tag_cloud a:hover, .entry__tags a:hover, .widget_tag_cloud a:focus, .entry__tags a:focus, .widget_product_tag_cloud a:hover, .widget_product_tag_cloud a:focus',
                    ),
            ),
            array(
                'id'       => 'button-text-color-hover',
                'type'     => 'color',
                'title'    => __( 'Hover Text Color', 'bourbon' ),
                'desc'     => __( 'Select Buttons global hover text color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'color' => '.extralink:hover, .more-link:hover, input.submit:hover, input[type="submit"]:hover, .nav-previous a:hover, .nav-next a:hover, .page-numbers:hover, .entry__tags a:hover, .widget_tag_cloud a:hover, .widget_bourbon_social_widget a:hover i, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce button.button:hover, .woocommerce #respond input#submit:hover, .widget_tag_cloud a:hover, .widget_product_tag_cloud a:hover, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit:hover',
                    ),
            ),
        )
    ) );
    /**
     * Links
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Links', 'bourbon' ),
        'id'         => 'opt-links',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'link-background-color',
                'type'     => 'color',
                'title'    => __( 'Links Color', 'bourbon' ),
                'desc'     => __( 'Select Buttons global links color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'color' => 'a, p a, .nav__name, .recent__title, .best__title, a:hover, .best__title:hover, .featured__title, .entry__meta a, .pp__footer a, .entry__posted_on .entry-date:hover, .widget_archive a, .widget_categories a, .widget_nav_menu a, .widget_categories select, .widget_nav_menu select, .widget_pages a, .widget_calendar table tr td a, .entry__share--icon a:hover i, .top-bar-section ul.dropdown li:not(.has-form):not(.active):hover > a:not(.button), .top-bar-section ul.dropdown li.active:not(.has-form) > a:not(.button), .featured__footer .comments-link a:hover',
                    ),
            ),
        )
    ) );
    /**
     * Fonts
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Fonts', 'bourbon' ),
        'id'         => 'opt-fonts',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'          => 'font-body',
                'type'        => 'typography',
                'title'       => __( 'Body font', 'bourbon' ),
                'font-backup' => true,
                'letter-spacing'=> true,  // Defaults to false
                'all_styles'  => true,
                'text-align'     => false,
                'output'      => array( 'body' ),
                'compiler'    => array( 'body' ),
                'units'       => 'px',
            ),
            array(
                'id'          => 'font-post-title',
                'type'        => 'typography',
                'title'       => __( 'Post Title', 'bourbon' ),
                //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                //'google'      => false,
                // Disable google fonts. Won't work if you haven't defined your google api key
                'font-backup' => true,
                // Select a backup non-google font in addition to a google font
                //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                //'subsets'       => false, // Only appears if google is true and subsets not set to false
                'font-size'     => false,
                'text-align'     => false,
                'line-height'   => false,
                //'word-spacing'  => true,  // Defaults to false
                //'letter-spacing'=> true,  // Defaults to false
                'color'         => true,
                //'preview'       => false, // Disable the previewer
                'all_styles'  => true,
                // Enable all Google Font style/weight variations to be added to the page
                'output'      => array( 'h1.entry__title a, h2, h3, h4, h5, h6, h2.best__title a, h5.featured__title a, .entry--quote .entry__content p' ),
                // An array of CSS selectors to apply this font style to dynamically
                'compiler'    => array( 'h1.entry__title a, h2, h3, h4, h5, h6, h2.best__title a, h5.featured__title a, .entry--quote .entry__content p' ),
                // An array of CSS selectors to apply this font style to dynamically
                'units'       => 'px',
                // Defaults to px
                'default'     => array(),
            ),
            array(
                'id'          => 'font-hero-title',
                'type'        => 'typography',
                'title'       => __( 'Hero Title', 'bourbon' ),
                'font-backup' => true,
                'letter-spacing'=> true,  // Defaults to false
                'all_styles'  => true,
                'text-align'     => false,
                'output'      => array( '.page__title' ),
                'compiler'    => array( '.page__title' ),
                'units'       => 'px',
            ),
            array(
                'id'          => 'font-widget-title',
                'type'        => 'typography',
                'title'       => __( 'Widget Title', 'bourbon' ),
                'font-backup' => true,
                'letter-spacing'=> true,  // Defaults to false
                'all_styles'  => true,
                'text-align'  => false,
                'output'      => array( 'h5.widget__title, .widget_calendar caption' ),
                'compiler'    => array( 'h5.widget__title, .widget_calendar caption' ),
                'units'       => 'px',
            ),
            array(
                'id'          => 'font-category',
                'type'         => 'typography',
                'title'         => __( 'Category link', 'bourbon' ),
                'font-backup'   => true,
                'text-transform' => true,
                'letter-spacing'=> true,  // Defaults to false
                'all_styles'  => true,
                'text-align'     => false,
                'output'      => array( '.entry__meta, .byline, .entry__posted_on' ),
                'compiler'    => array( '.entry__meta, .byline, .entry__posted_on' ),
                'units'       => 'px',
            ),
            array(
                'id'          => 'font-topbar',
                'type'         => 'typography',
                'title'         => __( 'Top Menu', 'bourbon' ),
                'font-backup'   => true,
                'text-transform' => true,
                'letter-spacing'=> true,  // Defaults to false
                'all_styles'  => true,
                'text-align'     => false,
                'output'      => array( '.top-bar-section ul li:not(.has-form) a:not(.button)' ),
                'compiler'    => array( '.top-bar-section ul li:not(.has-form) a:not(.button)' ),
                'units'       => 'px',
            ),
        )
    ) );

    /**
     * Home Page
     */
    Redux::setSection( $opt_name, array(
        'title' => __( 'Home Page', 'bourbon' ),
        'id'    => 'homepage',
        'desc'  => __( 'Home Page layout settings', 'bourbon' ),
        'icon'  => 'fa fa-home',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'    => 'homepage-info',
                'type'  => 'info',
                'style' => 'warning',
                'title' => __( 'Information', 'bourbon' ),
                'desc'  => __( 'The section will not be shown if it is added to the column Disabled or shown in the settings Hero (Hero Slider Inside)', 'bourbon' )
            ),
            array(
                'id'       => 'homepage-sorter',
                'type'     => 'sorter',
                'title'    => 'Homepage Layout Manager',
                'desc'     => __('Organize how you want the layout to appear on the homepage.', 'bourbon' ),
                'compiler' => 'true',
                'options'  => array(
                    'disabled' => array(
                        'hero' => 'Hero',
                    ),
                    'enabled'  => array(
                        'best'     => 'Best Slider',
                        'featured' => 'Featured Slider',
                        'posts'   => 'Posts'
                    ),
                ),
            ),
            array(
                'id'       => 'homepage-template',
                'type'     => 'select',
                'title'    => __( 'Homepage Template', 'bourbon' ),
                'desc'     => __( 'Select the template for displaying Posts.', 'bourbon' ),
                //Must provide key => value pairs for radio options
                'options'  => array(
                    'classic'       => 'Classic',
                    'column'        => 'Column',
                    'classic-grid'  => 'Classic with Grid',
                    'grid-2'        => '2 Columns Grid',
                    'grid-3'        => '3 Columns Grid',
                ),
                'default'  => 'classic'
            ),
            array(
                'id'       => 'sidebar-position',
                'type'     => 'image_select',
                'title'    => __( 'Sidebar Position', 'bourbon' ),
                'desc'     => __( 'Select the sidebar position (only in Classic Template)', 'bourbon' ),
                'required' => array( 'homepage-template', '=', 'classic' ),
                //Must provide key => value(array:title|img) pairs for radio options
                'options'  => array(
                    '1' => array(
                        'alt' => __( 'Sidebar Right', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                    ),
                    '2' => array(
                        'alt' => __( 'Sidebar Left', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                    ),
                    '3' => array(
                        'alt' => __( 'Without Sidebar', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                    ),
                    // '4' => array(
                    //     'alt' => 'Two Sidebars',
                    //     'img' => ReduxFramework::$_url . 'assets/img/3cm.png'
                    // ),
                ),
                'default'  => '1'
            ),
        )

    ) );
    /**
     * Top Bar
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Topbar', 'bourbon' ),
        'id'         => 'opt-topbar',
        'icon'       => 'fa fa-bars',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'       => 'topbar-background',
                'type'     => 'color',
                'title'    => __( 'Topbar background color', 'bourbon' ),
                'default'  => '',
                'output'   => array(
                    'background-color' => '.top-bar-background',
                    ),
            ),
            array(
                'id'       => 'topbar-invert',
                'type'     => 'switch',
                'title'    => __( 'Invert Text', 'bourbon' ),
                'subtitle' => __( 'Topbar invert text', 'bourbon' ),
                'default'  => false,
            ),
            array(
                'id'       => 'topbar-fullwidth',
                'type'     => 'switch',
                'title'    => __( 'Fullwidth', 'bourbon' ),
                'subtitle' => __( 'Topbar fullwidth', 'bourbon' ),
                'default'  => false,
            ),
            array(
                'id'       => 'topbar-sticker',
                'type'     => 'button_set',
                'title'    => __('Sticky Topbar', 'bourbon'),
                'desc'     => __('Select the display type for the Sticky Topbar', 'bourbon'),
                'multi'    => true,
                //Must provide key => value pairs for options
                'options' => array(
                    'small'  => 'small',
                    'medium' => 'medium',
                    'large'  => 'large'
                 ),
                'default' => array(),
            ),
        )
    ) );
    /**
     * HERO
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Hero', 'bourbon' ),
        'desc'       => __( 'Homepage Hero settings.', 'bourbon' ),
        'id'         => 'opt-hero',
        'icon'      => 'fa fa-diamond',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'       => 'hero-logo',
                'type'     => 'media',
                'url'      => false,
                'title'    => __( 'Hero Picture', 'bourbon' ),
                'compiler' => 'true',
                // 'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                'default'  => array( 'url' => '' ),
            ),
            array(
                'id'       => 'hero-title',
                'type'     => 'text',
                'title'    => __( 'Title', 'bourbon' ),
                'default'  => __( 'Welcome', 'bourbon' ),
            ),
            array(
                'id'       => 'hero-description',
                'type'     => 'editor',
                'title'    => __( 'Description', 'bourbon' ),
                'desc'     => __( 'Hero description text', 'bourbon' ),
                'default'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque condimentum aliquet leo non pulvinar. Sed euismod quam nec purus vehicula, a rhoncus lacus posuere. Integer nibh tellus, dapibus commodo suscipit congue, mollis sit amet enim. Donec arcu mi, pellentesque sed accumsan ut, ultricies quis libero. ',
            ),
            array(
                'id'       => 'hero-line-color',
                'type'     => 'color',
                'title'    => __( 'Divider Color', 'bourbon' ),
                'desc'     => __( 'Divider color on the home page Hero', 'bourbon' ),
                'default'  => '',
            ),
            array(
                'id'       => 'hero-extralink-title',
                'type'     => 'text',
                'title'    => __( 'Extra button', 'bourbon' ),
                'desc'     => __( 'Extra Button text', 'bourbon' ),
                'default'  => __( 'BUY THEME', 'bourbon' )
            ),
            array(
                'id'       => 'hero-extralink-url',
                'type'     => 'text',
                'desc'     => __( 'Extra Button url', 'bourbon' ),
                'default'  => 'http://themeforest.net/user/burocrate/portfolio?ref=burocrate',
            ),
            array(
                'id'       => 'hero-background-image',
                'type'     => 'media',
                'url'      => false,
                'title'    => __( 'Hero Background Image', 'bourbon' ),
                'compiler' => 'true',
                // 'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                'subtitle' => __( 'Upload any media using uploader', 'bourbon' ),
                'default'  => array( 'url' => '' ),
            ),
            array(
                'id'       => 'hero-background-color',
                'type'     => 'color_rgba',
                'title'    => __( 'Hero Background Color', 'bourbon' ),
                'default'  => array(
                    'color' => '#efefef',
                    'alpha' => '1',
                    'rgba' => 'rgba(255, 255, 255, 0)'
                ),
            ),
            array(
                'id'       => 'hero-text-inverse',
                'type'     => 'switch',
                'title'    => __( 'Inverse Text', 'bourbon' ),
                'default'  => false,
            ),
        )
    ) );



    /**
     * Sliders
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Sliders', 'bourbon' ),
        'id'         => 'opt-sliders',
        'icon'      => 'fa fa-th',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'    => 'sliders-info',
                'type'  => 'info',
                'style' => 'warning',
                'icon'  => 'fa fa-info',
                'title' => __( 'More color settings', 'bourbon' ),
                'desc'  => __( 'You can change Link and Title color in ( Global / Links ) ', 'bourbon' )
            ),
            array(
                'id'       => 'best-section-start',
                'type'     => 'section',
                'title'    => __( 'Best Slider', 'bourbon' ),
                'subtitle' => __( 'Best slider shows only the Best posts. To mark post as Best see in the edit post ', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'      => 'best-items',
                'type'    => 'spinner',
                'title'   => __( 'Items', 'bourbon' ),
                'subtitle'    => __( 'Number of items. ', 'bourbon' ),
                'default' => '8',
                'min'     => '1',
                'step'    => '1',
                'max'     => '50',
            ),
            array(
                'id'       => 'best-fullwidth',
                'type'     => 'switch',
                'title'    => __( 'Fullwidth', 'bourbon' ),
                'subtitle' => __( 'Slider fullwidth', 'bourbon' ),
                'default'  => true,
            ),
            array(
                'id'       => 'best-autoplay',
                'type'     => 'switch',
                'title'    => __( 'Autoplay', 'bourbon' ),
                'default'  => true,
            ),
            array(
                'id'     => 'best-section-end',
                'type'   => 'section',
                'indent' => false, // Indent all options below until the next 'section' option is set.
            ),

            array(
                'id'       => 'featured-section-start',
                'type'     => 'section',
                'title'    => __( 'Featured Slider', 'bourbon' ),
                'subtitle' => __( 'Featured Slider shows the latest posts in the current category', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'       => 'featured-background-color',
                'type'     => 'color_rgba',
                'title'    => __( 'Background Color', 'bourbon' ),
                'default'  => array(
                    'color' => '',
                    'alpha' => '',
                    'rgba' => ''
                ),
                'output'  => array(
                  'background-color' => '.section__featured',
                )
            ),
            array(
                'id'       => 'featured-text-color',
                'type'     => 'color',
                'title'    => __( 'Text Color', 'bourbon' ),
                'default'  => '',
                'output'    => array(
                    'color' => '.featured__footer',
                    ),
            ),
            array(
                'id'      => 'featured-items',
                'type'    => 'spinner',
                'title'   => __( 'Items', 'bourbon' ),
                'subtitle'    => __( 'Number of items. ', 'bourbon' ),
                'default' => '8',
                'min'     => '4',
                'step'    => '1',
                'max'     => '50',
            ),
            array(
                'id'       => 'featured-columns',
                'type'     => 'button_set',
                'title'    => __( 'Columns', 'bourbon' ),
                'subtitle' => __( 'Number of columns ', 'bourbon' ),
                'options'  => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ),
                'default'  => '4'
            ),
            array(
                'id'       => 'featured-divider',
                'type'     => 'switch',
                'title'    => __( 'Padding', 'bourbon' ),
                'subtitle' => __( 'Padding between columns', 'bourbon' ),
                'default'  => true,
            ),
            array(
                'id'       => 'featured-fullwidth',
                'type'     => 'switch',
                'title'    => __( 'Fullwidth', 'bourbon' ),
                'subtitle' => __( 'Slider fullwidth', 'bourbon' ),
                'default'  => false,
            ),
            array(
                'id'       => 'featured-autoplay',
                'type'     => 'switch',
                'title'    => __( 'Autoplay', 'bourbon' ),
                'default'  => true,
            ),
            array(
                'id'     => 'featured-section-end',
                'type'   => 'section',
                'indent' => false, // Indent all options below until the next 'section' option is set.
            ),

        )
    ) );
    /**
     * Posts
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Posts', 'bourbon' ),
        'id'         => 'opt-posts',
        'icon'       => 'fa fa-file-o',
        'fields'     => array(
            array(
                'id'       => 'hot-background-color',
                'type'     => 'color',
                'title'    => __( 'Post Label Background Color', 'bourbon' ),
                'subtitle' => __( 'Pick a color for the Post Label', 'bourbon' ),
                'default'  => '',
                'output'   => array( 'background-color' => '.hot' ),
            ),
            array(
                'id'       => 'author-post',
                'type'     => 'switch',
                'title'    => __( 'Post Author after Post', 'bourbon' ),
                'default'  => true,
            ),

            array(
                'id'       => 'share-icons',
                'type'     => 'checkbox',
                'title'    => __('Post share icons', 'bourbon'),
                'desc'     => __('Select the services you want to use when sharing', 'bourbon'),

                //Must provide key => value pairs for multi checkbox options
                'options'  => array(
                    'facebook' => 'Facebook',
                    'twitter' => 'Twitter',
                    'linkedin' => 'Linkedin',
                    'google' => 'Google',
                    'pinterest' => 'Pinterest'
                ),

                //See how default has changed? you also don't need to specify opts that are 0.
                'default' => array(
                    'facebook' => '1',
                    'twitter' => '1',
                    'linkedin' => '1',
                    'google' => '1',
                    'pinterest' => '1'
                )
            ),
            array(
                'id'       => 'post-layout',
                'type'     => 'image_select',
                'title'    => __( 'Posts Layout', 'bourbon' ),
                'desc'     => __( 'Select a layout for displaying a single post', 'scotch' ),
                'options'  => array(
                    '1' => array(
                        'alt' => __( 'Sidebar Right', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                    ),
                    '2' => array(
                        'alt' => __( 'Sidebar Left', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                    ),
                    '3' => array(
                        'alt' => __( 'Full Width', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                    ),
                    '4' => array(
                        'alt' => __( 'Column', 'bourbon' ),
                        'img' => ReduxFramework::$_url . 'assets/img/3cm.png'
                    ),
                ),
                'default'  => '1'
            ),

            array(
                'id'       => 'quote-section-start',
                'type'     => 'section',
                'title'    => __( 'Quote', 'bourbon' ),
                'subtitle' => __( 'Quote Post Format settings', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'       => 'quote-background-color',
                'type'     => 'color',
                'title'    => __( 'Quote Background Color', 'bourbon' ),
                'subtitle' => __( 'Pick a color for the Quote post format', 'bourbon' ),
                'default'  => '',
                'output'   => array( 'background-color' => '.entry--quote .entry__content p, .entry--quote .entry__content p a, .entry--quote .blockquote p' ),
            ),
            array(
                'id'       => 'quote-text-color',
                'type'     => 'color',
                'title'    => __( 'Quote Text Color', 'bourbon' ),
                'subtitle' => __( 'Pick a color for the Quote post format', 'bourbon' ),
                'default'  => '',
                'output'   => array( 'color' => '.entry--quote .entry__content p, .entry--quote .entry__content p a, .entry--quote .blockquote p' ),
            ),
            array(
                'id'       => 'news-section-start',
                'type'     => 'section',
                'title'    => __( 'News Section', 'bourbon' ),
                'subtitle' => __( 'Recent posts of category displayed under each article', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'      => 'news-items',
                'type'    => 'spinner',
                'title'   => __( 'Items', 'bourbon' ),
                'subtitle' => __( 'The number of items', 'bourbon' ),
                'desc'    => __( '0 items will be removed section', 'bourbon' ),
                'default' => '2',
                'min'     => '0',
                'step'    => '1',
                'max'     => '10',
            ),
            array(
                'id'       => 'news-format',
                'type'     => 'button_set',
                'title'    => __( 'News type', 'bourbon' ),
                'subtitle' => __( 'Select type Section of News', 'bourbon' ),
                'options'  => array(
                    'recent' => 'Slider',
                    'news' => 'Rows',
                ),
                'default'  => 'recent'
            ),
        )

    ) );
    /**
     * Archives & Categories
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Archives & Categories', 'bourbon' ),
        'id'         => 'opt-categories',
        'icon'      => 'fa fa-archive',
        'desc'       => __( 'Archives and Categories settings', 'bourbon' ),
        'subsection' => false,
        'fields'     => array(
            array(
                'id'       => 'categories-section-start',
                'type'     => 'section',
                'title'    => __( 'Categories', 'bourbon' ),
                'subtitle' => __( 'Featured Slider shows the latest posts in the current category', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'       => 'categories-template',
                'type'     => 'select',
                'title'    => __( 'Categories Template', 'bourbon' ),
                'desc'     => __( 'Select the template for displaying Categories.', 'bourbon' ),
                //Must provide key => value pairs for radio options
                'options'  => array(
                  'rows' => __( 'Rows', 'bourbon'),
                  'classic' =>  __( 'Classic', 'bourbon'),
                  'classic-grid'  => __( 'Classic with Grid', 'bourbon'),
                  'column'  =>  __( '1-Column', 'bourbon'),
                  'grid-2'  =>  __( '2-Columns Grid', 'bourbon'),
                  'grid-3'  =>  __( '3-Columns Grid', 'bourbon'),
                ),
                'default'  => 'classic'
            ),
            array(
                'id'       => 'archives-section-start',
                'type'     => 'section',
                'title'    => __( 'Archives', 'bourbon' ),
                'subtitle' => __( 'Featured Slider shows the latest posts in the current category', 'bourbon' ),
                'indent'   => true, // Indent all options below until the next 'section' option is set.
            ),
            array(
                'id'       => 'archives-template',
                'type'     => 'select',
                'title'    => __( 'Archives Template', 'bourbon' ),
                'desc'     => __( 'Select the template for displaying Archives.', 'bourbon' ),
                //Must provide key => value pairs for radio options
                'options'  => array(
                    'rows' => __( 'Rows', 'bourbon'),
                    'classic' =>  __( 'Classic', 'bourbon'),
                    'classic-grid'  => __( 'Classic with Grid', 'bourbon'),
                    'column'  =>  __( '1-Column', 'bourbon'),
                    'grid-2'  =>  __( '2-Columns Grid', 'bourbon'),
                    'grid-3'  =>  __( '3-Columns Grid', 'bourbon'),
                ),
                'default'  => 'rows'
            ),
        )
    ) );


    /**
     * Sidebar
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Sidebar', 'bourbon' ),
        'id'         => 'opt-sidebar',
        'icon'       => 'fa fa-columns',
        'subsection' => false,
        'fields'     => array(
            array(
                'id'       => 'sidebar-text-inverse',
                'type'     => 'switch',
                'title'    => __( 'Sidebar Inverse Text', 'bourbon' ),
                'subtitle' => __( 'Inverse text for the sidebar widgets.', 'bourbon' ),
                'default'  => false,
            ),
          array(
              'id'    => 'sidebar-info',
              'type'  => 'info',
              'style' => 'warning',
              'icon'  => 'fa fa-info',
              'title' => __( 'More color settings', 'bourbon' ),
              'desc'  => __( 'You can change Link and Title color in ( Global / Links ) ', 'bourbon' )
          ),
        )

    ) );

    /**
     * Sidebar
     */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Footer', 'bourbon' ),
        'id'         => 'opt-footer',
        'icon'       => 'fa fa-dedent',
        'subsection' => false,
        'fields'     => array(
          array(
              'id'       => 'footer-text-color',
              'type'     => 'color',
              'title'    => __( 'Footer Text Color', 'bourbon' ),
              'default'  => '',
              'output'   => array( 'color' => '.footer-widget .textwidget, .footer-widget .widget__title, .footer-widget caption,  .footer-widget table tr th, .footer-widget table tr td, .footer-widget p, .footer-widget a i, .footer-widget li, .footer-widget .recentcomments, .footer-widget .post-date' ),
          ),
          array(
              'id'       => 'footer-link-color',
              'type'     => 'color',
              'title'    => __( 'Footer Link Color', 'bourbon' ),
              'default'  => '',
              'output'   => array( 'color' => '.footer-widget a' ),
          ),
          array(
              'id'       => 'footer-background-color',
              'type'     => 'color',
              'title'    => __( 'Footer Background Color', 'bourbon' ),
              'subtitle' => __( 'Pick a background color for the Footer', 'bourbon' ),
              'default'  => '',
              'output'   => array( 'background-color' => '.footer' ),
          ),
          array(
              'id'    => 'footer-info',
              'type'  => 'info',
              'style' => 'warning',
              'icon'  => 'fa fa-info',
              'title' => __( 'More color settings', 'bourbon' ),
              'desc'  => __( 'You can change Link and Title color in ( Global / Links ) ', 'bourbon' )
          ),
        )

    ) );

    /*
     * <--- END SECTIONS
     */
