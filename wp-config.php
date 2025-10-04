<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'cw^Y+S#E =[7uf55^ji{gor/sFT}|br|QB+y~*Sl}VMh*;4Y>U8m63U?nJ+0ELeF' );
define( 'SECURE_AUTH_KEY',   'AoSZqXF-8430PR)or6fn;~6=TTs>#Ill>CO&a<j_g=6$,J<@.tWJ1|yIyQ1XiT9N' );
define( 'LOGGED_IN_KEY',     't/*+yr?_wfo(:&t=-](aLP#Z+}$}j|ATtz,W{sBRxVy~/Pb?VnzNn/jH#v.Uu[t7' );
define( 'NONCE_KEY',         'paLwp9pNvxI@W)QN;5]Z!rqH&vq2-3J9u16zlJyR+0:1=$Rcm89#7FVjZRC|,n{!' );
define( 'AUTH_SALT',         'io4$O#: xdS fKvuojCbhhefL>8510@oxd&8213c`ZeDR {<8f^ uH`U*RQ,aC(:' );
define( 'SECURE_AUTH_SALT',  '#|AfyO3F6TQp]8{b_5#JQbk;kej{t,k@70|!/W>Dk/=}&13A}AL4#6rq}XdC/e%>' );
define( 'LOGGED_IN_SALT',    '`6yQ%j2qX6I<VF^P::2e6QAjG7wtwOw<N>seH3o+Kw5f3U=zZ9gok7*FMYSa/,:i' );
define( 'NONCE_SALT',        'EG$]Dx#CiE7/R =SN! &,0E7({uL?%j3$mE5bdPE+wK?6!3{^#pHG~}`*;#ZV8~H' );
define( 'WP_CACHE_KEY_SALT', '6qE 2Krh+~`5@/D,7/U1TRt-P~TR%PN;Yu2L/jF~c,kb)rkYUB^_tWinBH_ to+N' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

// Enable debug logging
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
