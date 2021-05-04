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
define( 'DB_NAME', 'finapi' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('ALTERNATE_WP_CRON', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'cp5fDeHI|agSWrS{(II#%%P.Gql!-4Z5c+-4KorJ=F:O0..s 5}bO$vhgk!SNz+#' );
define( 'SECURE_AUTH_KEY',  'b=w?g(07.?lR(WaPD&:L#iQk91G]T> AQD3((rDRZyegSX2Sk4od=zT(Cu/s?C T' );
define( 'LOGGED_IN_KEY',    'niX9[}jybzoPTaXs+I8?@eKa%;u0}zK]PREN9VOBUAa.JqebSti_*|s6cwD23QiP' );
define( 'NONCE_KEY',        '%:!xF0vQ>zhT;5o[jA=-nVHHrR#g7$t[jasDK.q`VxG;CSn#W?3 2YBV.t/PT3Lb' );
define( 'AUTH_SALT',        'Jt:P+I62f$DQGPA#CjPrUGN|+Q/-!]|oXTn`~+R}<_5N<4Qkx5`1?er){ .Tdw&U' );
define( 'SECURE_AUTH_SALT', 'A&b&S(KU!^!]+><=J#s$:eNjVp0G2dwPw8Lv2A|)I-~}<6[<_F ,8[SEdvx=Vuy*' );
define( 'LOGGED_IN_SALT',   'U,`($:FR$io +B:~l}AJPC}u^{iZ%)%,=0(*j9/OB(]7VoJPQ%?8G^AI47>*xvH5' );
define( 'NONCE_SALT',       '-%[CiRi^]*Bil=e%xwJzXgRrDV%o2s@Ym1INIg,M`..]Y>I{p$b Ycn2)50}hzV@' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
