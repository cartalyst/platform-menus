<?php

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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class MenusController extends AdminController
{
    /**
     * The Menus repository.
     *
     * @var \Platform\Menus\Repositories\MenuRepositoryInterface
     */
    protected $menus;

    /**
     * Holds all the mass actions we can execute.
     *
     * @var array
     */
    protected $actions = [
        'enable',
        'delete',
        'disable',
    ];

    /**
     * Constructor.
     *
     * @param  \Platform\Menus\Repositories\MenuRepositoryInterface  $menus
     * @return void
     */
    public function __construct(MenuRepositoryInterface $menus)
    {
        parent::__construct();

        $this->menus = $menus;
    }

    /**
     * Display a listing of menus.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('platform/menus::index');
    }

    /**
     * Datasource for the menus Data Grid.
     *
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function grid()
    {
        $settings = [
            'columns' => [
                'id',
                'name',
                'slug',
                'items_count',
                'enabled',
                'created_at',
            ],
            'sort'      => 'created_at',
            'direction' => 'desc',
            'pdf_view'  => 'pdf',
            'transformer' => function ($element) {
                $element['edit_uri'] = route('admin.menu.edit', $element['id']);

                return $element;
            },
        ];

        return datagrid($this->menus->grid(), $settings);
    }

    /**
     * Shows the form for creating a new menu.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->showForm('create');
    }

    /**
     * Handles posting of the form for creating a new menu.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        return $this->processForm('create');
    }

    /**
     * Shows the form for updating a menu.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        return $this->showForm('update', $id);
    }

    /**
     * Handles posting of the form for updating a menu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        return $this->processForm('update', $id);
    }

    /**
     * Removes the specified menu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        if ($this->menus->delete($id)) {
            $this->alerts->success(trans('platform/menus::message.success.delete'));

            return redirect()->route('admin.menus.all');
        }

        $this->alerts->error(trans('platform/menus::message.error.delete'));

        return redirect()->route('admin.menus.all');
    }

    /**
     * Executes the mass action.
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAction()
    {
        $action = request()->input('action');

        if (in_array($action, $this->actions)) {
            foreach (request()->input('rows', []) as $entry) {
                $this->menus->{$action}($entry);
            }

            return response('Success');
        }

        return response('Failed', 500);
    }

    /**
     * Shows the form.
     *
     * @param  string  $mode
     * @param  mixed  $id
     * @return mixed
     */
    protected function showForm($mode, $id = null)
    {
        if (! $data = $this->menus->getPreparedMenu($id)) {
            $this->alerts->error(trans('platform/menus::message.not_found', compact('id')));

            return redirect()->route('admin.menus.all');
        }

        // Get the menu object
        $menu = $data['menu'];

        // Get all the available user roles
        $roles = $data['roles'];

        // Get the registered menu types
        $types = $data['types'];

        // Get the menu children items
        $children = $data['children'];

        // Get all the current menu slugs
        $persistedSlugs = $data['persistedSlugs'];

        // Share some variables, because of views inheritance
        view()->share(compact('roles', 'types'));

        return view('platform/menus::manage', compact(
            'menu', 'children', 'persistedSlugs', 'mode'
        ));
    }

    /**
     * Processes the form.
     *
     * @param  string  $mode
     * @param  mixed  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function processForm($mode, $id = null)
    {
        // Store the menu
        list($messages) = $this->menus->store($id, request()->all());

        // Do we have any errors?
        if ($messages->isEmpty()) {
            $this->alerts->success(trans("platform/menus::message.success.{$mode}"));

            return redirect()->route('admin.menus.all');
        }

        $this->alerts->error($messages, 'form');

        return redirect()->back()->withInput();
    }
}
