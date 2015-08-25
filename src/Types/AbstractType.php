<?php namespace Platform\Menus\Types;
/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Menus extension
 * @version    1.2.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;
use Illuminate\Container\Container;

abstract class AbstractType {

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The URL Generator.
	 *
	 * @var \Illuminate\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * The View Factory.
	 *
	 * @var \Illuminate\View\Factory
	 */
	protected $view;

	/**
	 * The Translator.
	 *
	 * @var \Illuminate\Translation\Translator
	 */
	protected $translator;

	/**
	 * Create a new type.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;

		$this->url = $this->app['url'];

		$this->view = $this->app['view'];

		$this->translator = $this->app['translator'];
	}

	/**
	 * Returns the type identifier.
	 *
	 * @return string
	 */
	abstract public function getIdentifier();

	/**
	 * Get a human friendly name for the type.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->translator->trans("platform/menus::model.general.type_{$this->getIdentifier()}");
	}

	/**
	 * Get the name for the menu child.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return string
	 */
	public function getNameAttribute(Menu $child)
	{
		return $child->name;
	}

	/**
	 * Get the URL for the menu child.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @param  array  $options
	 * @return string
	 */
	public function getUrlAttribute(Menu $child, array $options = [])
	{
		$uri = $child->uri;

		if ($beforeUri = array_get($options, 'before_uri'))
		{
			$uri = "{$beforeUri}/{$uri}";
		}

		return $this->url->to($uri, [], $child->secure);
	}

	/**
	 * Return the form HTML template for a edit child of this type as well
	 * as creating new children.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return \View
	 */
	public function getFormHtml(Menu $child = null)
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/form", compact('child'));
	}

	/**
	 * Return the HTML template used when creating a menu child of this type.
	 *
	 * @return \View
	 */
	public function getTemplateHtml()
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/template");
	}

	/**
	 * Event that is called after a menu children is saved.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return void
	 */
	abstract public function afterSave(Menu $child);

	/**
	 * Event that is called before a children is deleted.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return void
	 */
	abstract public function beforeDelete(Menu $child);

}
