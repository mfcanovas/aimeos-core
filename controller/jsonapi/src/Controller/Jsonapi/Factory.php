<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jsonapi
 */


namespace Aimeos\Controller\Jsonapi;


/**
 * Factory which can create all JSON API controllers
 *
 * @package Controller
 * @subpackage Jsonapi
 */
class Factory
	extends \Aimeos\Controller\Jsonapi\Common\Factory\Base
	implements \Aimeos\Controller\Jsonapi\Common\Factory\Iface
{
	static private $cache = true;
	static private $controllers = array();


	/**
	 * Removes the controller objects from the cache.
	 *
	 * If neither a context ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer $id Context ID the objects have been created with (string of \Aimeos\MShop\Context\Item\Iface)
	 * @param string $path Path describing the controller to clear, e.g. "product/lists/type"
	 */
	static public function clear( $id = null, $path = null )
	{
		if( $id !== null )
		{
			if( $path !== null ) {
				self::$controllers[$id][$path] = null;
			} else {
				self::$controllers[$id] = array();
			}

			return;
		}

		self::$controllers = array();
	}


	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * Controllers are created by providing only the domain name, e.g. "product"
	 *  for the \Aimeos\Controller\Jsonapi\Product\Standard or a path of names to
	 * retrieve a specific sub-controller, e.g. "product/type" for the
	 * \Aimeos\Controller\Jsonapi\Product\Type\Standard controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @param string|null $name Name of the controller implementation ("Standard" if null)
	 * @throws \Aimeos\Controller\Jsonapi\Exception If the given path is invalid
	 */
	static public function createController( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $path, $name = null )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Controller path is empty' ), 400 );
		}

		$id = (string) $context;

		if( self::$cache === false || !isset( self::$controllers[$id][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $key => $part )
			{
				if( ctype_alnum( $part ) === false )
				{
					$msg = sprintf( 'Invalid controller "%1$s" in "%2$s"', $part, $path );
					throw new \Aimeos\Controller\Jsonapi\Exception( $msg, 400 );
				}

				$parts[$key] = ucwords( $part );
			}


			$factory = '\\Aimeos\\Controller\\Jsonapi\\' . join( '\\', $parts ) . '\\Factory';

			if( class_exists( $factory ) === true )
			{
				$args = array( $context, $templatePaths, $path, $name );

				if( ( $controller = @call_user_func_array( array( $factory, 'createController' ), $args ) ) === false ) {
					throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Invalid factory "%1$s"', $factory ), 400 );
				}
			}
			else
			{
				$controller = self::createControllerRoot( $context, $templatePaths, $path, $name );
			}


			self::$controllers[$id][$path] = $controller;
		}

		return self::$controllers[$id][$path];
	}


	/**
	 * Enables or disables caching of class instances.
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function setCache( $value )
	{
		$old = self::$cache;
		self::$cache = (boolean) $value;

		return $old;
	}


	/**
	 * Creates the top level controller
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @throws \Aimeos\Controller\Jsonapi\Exception If the controller couldn't be created
	 */
	protected static function createControllerRoot( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $path, $name = null )
	{
		/** controller/jsonapi/name
		 * Class name of the used JSON API controller implementation
		 *
		 * Each default JSON API controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Controller\Jsonapi\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Controller\Jsonapi\Mycntl
		 *
		 * then you have to set the this configuration option:
		 *
		 *  controller/jsonapi/name = Mycntl
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCntl"!
		 *
		 * @param string Last part of the class name
		 * @since 2015.12
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'controller/jsonapi/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\Controller\\Jsonapi\\' . $name : '<not a string>';
			throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\Controller\\Jsonapi\\Iface';
		$classname = '\\Aimeos\\Controller\\Jsonapi\\' . $name;

		$controller = self::createControllerBase( $classname, $iface, $context, $templatePaths, $path );

		/** controller/jsonapi/decorators/excludes
		 * Excludes decorators added by the "common" option from the JSON API controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "controller/jsonapi/common/decorators/default" before they are wrapped
		 * around the JSON API controller.
		 *
		 *  controller/jsonapi/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Controller\Jsonapi\Common\Decorator\*") added via
		 * "controller/extjs/common/decorators/default" for the JSON API controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonapi/common/decorators/default
		 * @see controller/jsonapi/decorators/global
		 */

		return self::addControllerDecorators( $controller, $context, $templatePaths, $path );
	}
}