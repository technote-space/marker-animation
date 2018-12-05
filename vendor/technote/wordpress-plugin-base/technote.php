<?php
/*  Copyright 2018 technote-space (email : technote.space@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) && ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	exit;
}

if ( defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}

define( 'TECHNOTE_PLUGIN', 'technote' );

define( 'TECHNOTE_BOOTSTRAP', __FILE__ );

define( 'TECHNOTE_VERSION', '1.1.71' );

define( 'TECHNOTE_REQUIRED_PHP_VERSION', '5.6' );

if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	require_once dirname( __FILE__ ) . DS . 'classes' . DS . 'technote_mock.php';

	return;
}

if ( version_compare( phpversion(), TECHNOTE_REQUIRED_PHP_VERSION, '<' ) ) {
	// unsupported version
	require_once dirname( __FILE__ ) . DS . 'classes' . DS . 'technote_mock.php';

	return;
}

require_once dirname( __FILE__ ) . DS . 'classes' . DS . 'technote.php';
