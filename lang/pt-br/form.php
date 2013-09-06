<?php
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

return array(

	'legend' => 'Propriedades do Menu',

	'create' => array(
		'legend' => 'Criar Menu',
	),

	'update' => array(
		'legend' => 'Editar :menu',
	),

	'root' => array(
		'name' => 'Nome',
		'slug' => 'Apelido',
	),

	'child' => array(
		'create' => array(
			'legend' => 'Novo Nó',
		),

		'update' => array(
			'legend' => ':menu Detalhes',
		),

		'name' => 'Nome',
		'slug' => 'Apelido',

		'type' => array(
			'title' => 'Tipo',

			'static' => 'Etático',
			'page'   => 'Página',
		),

		'uri' => 'URL',

		'secure' => 'Url Segura (HTTPS)',

		'visibility' => array(
			'title' => 'Visibilidade',

			'always'     => 'Mostrar para todos',
			'logged_in'  => 'Logado',
			'logged_out' => 'Deslogado',
			'admin'      => 'Apenas Administrador',
		),

		'target' => array(
			'title' => 'Alvo',

			'self'   => 'Mesma Janela',
			'blank'  => 'Nova Janela',
			'parent' => 'Novo Frame',
			'top'    => 'Frame Superior (Documento Principal)',
		),

		'class' => 'Classe CSS',

		'enabled' => 'Status',

	),

);
