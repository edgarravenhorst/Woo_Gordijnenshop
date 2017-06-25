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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'gordijnenshop');

/** MySQL database username */
define('DB_USER', 'homestead');

/** MySQL database password */
define('DB_PASSWORD', 'secret');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         ']BWYGZbW!_WPQN6l_n~K7,UzXkT5?UPE45zRKr+PWW?H2:ea #{{bq]*o++/=~,1');
define('SECURE_AUTH_KEY',  '>&8{kcQR(`G{Q1K!Q(xkWOzdu9@#.O?:G[@HU%9WBXAm67:EHq(Gz$s4W?LMQ<Cu');
define('LOGGED_IN_KEY',    'eL%g~{TnZ*g=%uT,hCzK8)~*>%v8`)0ULi5W /[8jf]1rtGFTI!w]{8q*],@tsC/');
define('NONCE_KEY',        'E(YlrF, q13(?IheMzXV_|Iyz+vMH9l;a3ZEy(d!abL?~S;}k]4X1 fMyCdyL;Ui');
define('AUTH_SALT',        'F^czG:-gKqL5Kjbv]^)M2/]Z1_qc@xH9e Zp!:0Kd&ns1Slq;1V5Sep,)g=Il?Nx');
define('SECURE_AUTH_SALT', 'Hp?O1@dCi<> iR5zGSkkD?YCA6&#eeH=AT)Hd>,vG[ p:P%Gk_eA%pVi#~E{dNTN');
define('LOGGED_IN_SALT',   'v+Kiw?+BvumW:=R{-d6~ho]EF0hZ*uinPkrO+F8b11}|xpEHCG8Lli.tLgqu,F_/');
define('NONCE_SALT',       'Rd2R#$d(?lE`oNR4P)c&{Xt#`2fhNm=_,CNPckN_366uZIY&R6%P~%VQN}J&idNK');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
