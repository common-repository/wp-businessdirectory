<?php
/**
 * @package    JBD.Libraries
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
if (! defined ( 'ABSPATH' ))
	die ( 'Restricted access' );

if (! defined ( '_JDEFINES' )) {
	define ( 'JPATH_BASE', __DIR__ );
}

// Global definitions
$parts = explode ( DIRECTORY_SEPARATOR, JPATH_BASE );

// Defines.
define ( 'JPATH_ROOT', implode ( DIRECTORY_SEPARATOR, $parts ) );
define ( 'JPATH_SITE', JPATH_ROOT );
define ( 'JPATH_CONFIGURATION', JPATH_ROOT );
define ( 'JPATH_ADMINISTRATOR', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );
define ( 'JPATH_LIBRARIES', JPATH_ROOT . DIRECTORY_SEPARATOR . '' );
define ( 'JPATH_PLUGINS', JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' );
define ( 'JPATH_INSTALLATION', JPATH_ROOT . DIRECTORY_SEPARATOR . 'installation' );
define ( 'JPATH_THEMES', JPATH_BASE . DIRECTORY_SEPARATOR . 'templates' );
define ( 'JPATH_CACHE', JPATH_BASE . DIRECTORY_SEPARATOR . 'cache' );
define ( 'JPATH_MANIFESTS', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'manifests' );

// Set the platform root path as a constant if necessary.
if (! defined ( '_JEXEC' )) {
    define ( '_JEXEC', 1 );
}

// Set the platform root path as a constant if necessary.
if (! defined ( 'JPATH_PLATFORM' )) {
	define ( 'JPATH_PLATFORM', __DIR__ );
}

// Import the library loader if necessary.
if (! class_exists ( 'JLoader' )) {
	require_once JPATH_PLATFORM . '/loader.php';
}

// Make sure that the Joomla Platform has been successfully loaded.
if (! class_exists ( 'JLoader' )) {
	throw new RuntimeException ( 'MVC Platform Loader not loaded.' );
}

// Setup the autoloaders.
JLoader::setup ();
JLoader::registerPrefix ( 'J', JPATH_PLATFORM . '/' );

JLoader::registerNamespace ( 'MVC', JPATH_PLATFORM . '/', false, false, 'psr4' );

JLoader::registerNamespace('MVC\\CMS', JPATH_PLATFORM . '/cms', false, false, 'psr4');

require_once JPATH_PLATFORM . '/classloader.php';
// Create the Composer autoloader
$loader = require JPATH_LIBRARIES . '/vendor/autoload.php';
$loader->unregister ();

// var_dump($loader);
// Decorate Composer autoloader
spl_autoload_register ( array (
		new JClassLoader ( $loader ),
		'loadClass' 
), true, true );

// Register the class aliases for Framework classes that have replaced their Platform equivilents
require_once JPATH_LIBRARIES . '/classmap.php';
// Register JArrayHelper due to JRegistry moved to composer's vendor folder
JLoader::register ( 'JArrayHelper', JPATH_PLATFORM . '/utilities/arrayhelper.php' );

// Instantiate the application.
$app = JFactory::getApplication('administrator');

global $wpdb;