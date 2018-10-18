<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/srv/www/webroot/blog/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'db_cz6omwwga68');

/** MySQL database username */
define('DB_USER', 'user_cz6omwwga68');

/** MySQL database password */
define('DB_PASSWORD', 'b7f747b5-04d7-416e-91de-5eaeb90dd97c');


/** MySQL hostname */
define('DB_HOST', 'mysql');
/** stratus-aurora-1.cluster-c6bwmtzc1vzu.us-east-1.rds.amazonaws.com */

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'z<ACv=E]jOP*iVS_uW+:PFXAc9Rq8k.2me iDJ`HRK)-NU_A$D$N`W*+2|>WX)Cs');
define('SECURE_AUTH_KEY',  '47!On*fx>$1*i(@cz|<wNq@(f>F#B%~uE%rh9rz8-vnbr0jooI@}Y9MUKbmpdic(');
define('LOGGED_IN_KEY',    '71nA#j bn_Zk4Gzx[0[_qG!s|o`&I0{TkW2lDu`/=yo;~|ho~L%)I}npo|]Ne7EA');
define('NONCE_KEY',        'l7H|zV7D=QxP:}*5T/0N,OaDIG5}cc@Z2Q}PX}}F`Fz}y0W&+-#4/D{Fom3Y~Dm1');
define('AUTH_SALT',        '=NAXpS+,O>4*fiOz[=?f_7hD@)GJhUn<59Wzg#-%E*}2nm`z9/9/O?M`i<RGxj-y');
define('SECURE_AUTH_SALT', 'CYqX9+?AUK`IOI!UJ/3_NcBC1.@rER=wu,-W8^9Rmm)0VKry]W[_kA5O_lali; y');
define('LOGGED_IN_SALT',   '+78xXSHr)P$:l2B5:k+op+_Q.y5>31!n6PDT,4J50?;}Vl$,~N~bYT_*u3JR@^7!');
define('NONCE_SALT',       ' U/lrTLmoQ1-X4^VE#rU2y{sZWSC0N,_y{|7!~~XV?FDdax%XNIV,}D+3!/|{*-H');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
