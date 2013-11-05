<?php namespace Platform\Menus;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;
use Illuminate\View\Environment;
use Platform\Menus\Models\Menu;

abstract class BaseType {

	/**
	 * The URL Generator.
	 *
	 * @var \Illuminate\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * The View Environment.
	 *
	 * @var \Illuminate\View\Environment
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
	 * @param  \Illuminate\Routing\UrlGenerator    $url
	 * @param  \Illuminate\View\Environment        $view
	 * @param  \Illuminate\Translation\Translator  $translator
	 * @return void
	 */
	public function __construct(UrlGenerator $url, Environment $view, Translator $translator)
	{
		$this->url = $url;
		$this->view = $view;
		$this->translator = $translator;
	}

	/**
	 * Get the name for the menu child.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return string
	 */
	public function getNameAttribute(Menu $child)
	{
		return $child->name;
	}

	/**
	 * Get the URL for the menu child.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @param  array  $options
	 * @return string
	 */
	public function getUrlAttribute(Menu $child, array $options = array())
	{
		$uri = $child->uri;

		if ($beforeUri = array_get($options, 'before_uri'))
		{
			$uri = "{$beforeUri}/{$uri}";
		}

		$uri = ltrim($uri, '/');

		return $child->secure ? $this->url->secure($uri) : $this->url->to($uri);
	}

	/**
	 * Event that is called after a menu children is saved.
	 * Attach any links and relationships.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	public function afterSave(Menu $child){}

	/**
	 * Called before a child is deleted. Detach any links
	 * and relationships.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	public function beforeDelete(Menu $child) {}

}
