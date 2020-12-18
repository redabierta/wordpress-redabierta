<?php
define('WP_CACHE', false);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'adventou_wp54537' );

/** MySQL database username */
define( 'DB_USER', 'adventou_wp54537' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Sdp9v@7J-0' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'lu4yobrjehyvh8ajxnjm5vq2xehmdvmp16pdlp6clz0jedlapma7votsx5r7kile' );
define( 'SECURE_AUTH_KEY',  'ngadikjew4fufqvflroy8uexvcsuvbgdxj0wtxthcawip4zhh4oaktzwmddjxphl' );
define( 'LOGGED_IN_KEY',    'kluz0cthqn1ap4mfeg2cbf646exvtnqmgicjiyrtsdm6zr1rg7febxl68keftnru' );
define( 'NONCE_KEY',        'jjud5xeqskwegegul6hfw0bvpkoljccepnz9upinakrab2fvl5jk0zo0msmho7eq' );
define( 'AUTH_SALT',        'c6ea6gywwslktdb1mfbytps6hkf3nlq4xsofvpavtdrvfolu2yvu2dr2atrax1yn' );
define( 'SECURE_AUTH_SALT', 'obcwbtlle3rp164zyuzlhyi56zipfjjaqt74potyyrxy97k43ejuceax8ltpsjvm' );
define( 'LOGGED_IN_SALT',   'jxlm3ajvvzxzplg3gv0hke7dxlfqhagfkuvcti4p2ybmrfqb1l4uisdctpd6caxy' );
define( 'NONCE_SALT',       'koty660czfzf7zkilee7vrivw9gvjhtt7x1y22ekq2bjpdtjg73bngoq0ts6wvym' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpra_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
