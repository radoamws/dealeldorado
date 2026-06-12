<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dealeldorado' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '_bX%YGyzr7t7_+R.Uw,78/7XSG0GM6!zff,^d0orP0x3b>s#Srq0KtRHq:dK+m~j' );
define( 'SECURE_AUTH_KEY',  'C2*Qh e[a}NQy,eP,.e/Y-wsJL/S#FGAZM/M^7X;BoT{O@!y`3qX}II.>}^=$d)9' );
define( 'LOGGED_IN_KEY',    ']0DG32$wYsaa0ZtKawU-}S8V^FsI]CVASy3m//[z/A(kHMnU/*y&=m,y)Us0X/[h' );
define( 'NONCE_KEY',        'agpQo)bomiK3a!}kZp&As=}129PR49{{>*CrzG8JkK0:4c9(.pef)K5(kr.k<o{|' );
define( 'AUTH_SALT',        '4md57XeO$MZ!x9I-NJ(z{euFTV,2ZEqJ0cSd5V#|rM]U,uR+gJ9oJ}^T$4VRrass' );
define( 'SECURE_AUTH_SALT', '7jsS[;)]2kZRdT^p@%Tp0mV)T/~]`=bmf=f?@&7h:wE7RdzNqMgm0UX7nxYAkE-Q' );
define( 'LOGGED_IN_SALT',   'qTSv<Bhy^SAW0}>|&<Y^rlQ^8`Vyw@LDn2f%>}EY0^$qY1ALkqqR*)w%=$N7`N;5' );
define( 'NONCE_SALT',       '#85h}usf@DV}lU#/d7?]cv&@hJ,h;IbbWH>K.%y:-*wv&C2mMRE2QF0$bF_5_rQ;' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'de_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
