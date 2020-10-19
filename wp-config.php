<?php
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
define( 'DB_NAME', 'fgrgkomz_pratu' );

/** MySQL database username */
define( 'DB_USER', 'fgrgkomz_pratu' );

/** MySQL database password */
define( 'DB_PASSWORD', '18x4p]rSQ(' );

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
define( 'AUTH_KEY',         '4cwchofhrxn9hcmpqqiupgnujuiqcogkbhllni5nk7xzxecjgayef2zaul6ofezv' );
define( 'SECURE_AUTH_KEY',  'llua1ixgnmzxgzoq9kyvmwxi29hs7dm3scfzmlnczvm0x067a4lxfhhv2fn7asrt' );
define( 'LOGGED_IN_KEY',    'wm61e9wztxooemafzbaybf3bpnelv6q7x8r4mpgzgolun0ctgwxkwmws0gewulpi' );
define( 'NONCE_KEY',        'jphhfbqnlkflp7rakcumydp2uqxmcvpvsnk0d5v6a5bhqqlnuncp0yy1djt7jc6v' );
define( 'AUTH_SALT',        'i0rd5dqxhcqlj5q54mucerwapaayd484wpus03gswwwysyrye86c1rddga03ksly' );
define( 'SECURE_AUTH_SALT', '7lulkwah7pdrqvoqqknelt5d8i7xgjkd15izyekauwsglqvyspvy74ghuq5ri32d' );
define( 'LOGGED_IN_SALT',   'ko1zpju299okvycn3inss0xn7xewuwre7akputbdbdkswx2crhsr2mhclhep9l9z' );
define( 'NONCE_SALT',       'rhfc6d5tzq9npipektmtyjwrd0oflity5pzzu7jfdtdgfn4jloyrgoeveyqlafzn' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'pratu_';

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
