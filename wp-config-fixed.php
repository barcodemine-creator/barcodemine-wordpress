<?php
/**
 * The base configuration for WordPress
 * FIXED VERSION - Resolves blog publishing issues
 */

// ** Database settings ** //
define( 'DB_NAME', 'kb_7ob15udm65' );
define( 'DB_USER', 'kb_7ob15udm65' );
define( 'DB_PASSWORD', 'S9F8Q2Ege825bRvq6W' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 * Generated on 2025-10-03 21:03:39
 */
define( 'AUTH_KEY', 'PZ]pR,a4/.0J|3yd1?%>m_6}AY:=&^V#je fgH/.<xbJ3ZB|W*$MycLp!+,+y*cS' );
define( 'SECURE_AUTH_KEY', 'wg-V`mfR7RjB_65#$#3oyY!a0_RN}&=i!07Eii4Z)~zDQvtESr|oT;+,>:N/_AF-' );
define( 'LOGGED_IN_KEY', '6`eTYNl+g@Ts{2esa_H||A3Ef.^gKv|vD}{sO|2u}E@eglP|Kl8Px3!P^ _NoPzp' );
define( 'NONCE_KEY', 'WE+U+|q4,>VK]WFTC)old}$*Q2~,TE N^!$dG<tdtavY%AX4/9O~46X;pjd.[=xV' );
define( 'AUTH_SALT', '3:qu)0Nn!}!)8>f_Eu-.Ku?u*9)c]C-#m3S`-9%,,=GS|mTjPhyMb<?JB3`m iI4' );
define( 'SECURE_AUTH_SALT', 'v!=(0M<k{30XRgJsEDcP%,*pC<*hK8D2AX4JQ+!wKtmi}z|)RExXNdp{3EEk.[Vu' );
define( 'LOGGED_IN_SALT', 'up$FqX{)>*e{6B]{]]_WEP*tK+8rvo9)-{qJC[rd+.-^!$&( h~9?]OF-@h<cCHr' );
define( 'NONCE_SALT', 'HoKHlSm_Fd/P8ZH /}zH&(g+jxSC6,*Jy`YI20dh41v[6n%0C+V5xo&?_DF#+$A#' );

/**#@-*/

$table_prefix = 'wp_';

/**
 * Debugging and Performance Settings
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'FS_METHOD', 'direct' );

/**
 * Security Settings
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_POST_REVISIONS', 3 );

/**
 * Performance Settings
 */
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';