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
	 * @var Illuminate\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * The View Environment.
	 *
	 * @var Illuminate\View\Environment
	 */
	protected $view;

	protected $translator;

	/**
	 * Create a new static type.
	 *
	 * @param  Illuminate\Routing\UrlGenerator  $url
	 * @param  Illuminate\View\Environment  $view
	 * @return void
	 */
	public function __construct(UrlGenerator $url, Environment $view, Translator $translator)
	{
		$this->url  = $url;
		$this->view = $view;
		$this->translator = $translator;
	}

	/**
	 * Return the HTML template used when creating a menu child of this type.
	 *
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getTemplateHtml()
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/template");
	}

	/**
	 * Return the form HTML template for a new child of this type.
	 *
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getTemplateFormHtml()
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/template/form");
	}

	/**
	 * Return the HTML template used when editing a menu child of this type.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getEditHtml(Menu $child)
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/child", compact('child'));
	}

	/**
	 * Return the form HTML template for a edit child of this type as well
	 * as creating new children.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getFormHtml(Menu $child = null)
	{
		return $this->view->make("platform/menus::types/{$this->getIdentifier()}/form", compact('child'));
	}

}
