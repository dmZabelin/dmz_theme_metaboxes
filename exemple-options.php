<?php
	function dmz_do_meta_boxes ( $meta_options ) 
	{

		$meta_options = [];
		$prefix = "dmz_";

		$meta_options[] = [
			'id'				=> 'meta_id',
			'title'			=> esc_html__( 'Заголовок мета блока', 'dmz_hram_site' ),
			'post_type'		=> [ 'about', 'page' ],
			//'show_on'		=> ['key' => 'page-template', 'value' => ['template-your-page.php']], - Строка для фильтра по шаблону страницы;
			//'show_on'		=> ['key' => 'term', 'value' => ['slug-your-term']], - Строка для фильтра по термам;
			//'taxonomy'	=> 'your-taxonomy' - В этой строке указываем таксономию по умолчанию 'Category';
			'context'		=> 'normal',
			'priority'		=>	'high',
			'fields'			=> [ 
				[
					'type'		=>	'title',
					'id'			=>	$prefix . 'field_title',
					'title'		=>	'Заголовок поля "title"',
					'std'			=> '',
				], [
					'type'		=>	'text',
					'id'			=>	$prefix . 'field_text',
					'title'		=>	'Заголовок поля "text"',
					'desc'		=>	'Описание поля',
					'std'			=> '',
				], [
					'type'		=>	'textarea',
					'id'			=>	$prefix . 'field_textarea',
					'title'		=>	esc_html__( 'Заголовок поля "textarea"', 'dmz_hram_site' ),
					'desc'		=>	esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> '',
				], [
					'type'		=>	'textarea_code',
					'id'			=>	$prefix . 'field_textarea_code',
					'title'		=>	esc_html__( 'Заголовок поля "textarea_code"', 'dmz_hram_site' ),
					'desc'		=>	esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> '',
				],[
					'type'		=> 'wysiwyg',
					'id'			=> $prefix . 'field_wysiwyg',
					'title' 		=> esc_html__( 'Заголовок поля "wysiwyg"', 'dmz_hram_site' ),
					'desc' 		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> '',
				],[
					'type'		=> 'radio',
					'id'			=> $prefix . 'field_radio',
					'title'		=> esc_html__( 'Заголовок поля "radio"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> 'enable',
					'options' 	=> [
						[ 'name' => esc_html__( 'name','dmz_hram_site' ), 'value' => 'value1', ],
						[ 'name' => esc_html__( 'name2','dmz_hram_site' ), 'value' => 'value2w', ],
					],
				],[
					'type'		=> 'radio_on_of',
					'id'			=> $prefix . 'field_radio_on_of',
					'title'		=> esc_html__( 'Заголовок поля "radio_on_of"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> 'enable',
					'options' 	=> [
						[ 'name' => esc_html__( 'Включить','dmz_hram_site' ), 'value' => 'enable', ],
						[ 'name' => esc_html__( 'Выключить','dmz_hram_site' ), 'value' => 'disable', ],
					],
				],[
					'type'    	=> 'select',
					'id'   		=> $prefix . 'field_select',
					'title' 		=> esc_html__( 'Заголовок поля "select"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'  		=> '',
					'options' 	=> [
						[ 'name' => esc_html__( 'name1','dmz_hram_site' ), 'value' => 'val1', ],
						[ 'name' => esc_html__( 'name2','dmz_hram_site' ), 'value' => 'val2', ],
					],
				],[
					'type'		=> 'checkbox',
					'id'			=> $prefix . 'field_checkbox',
					'title' 		=> esc_html__( 'Заголовок поля "checkbox"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
				],[
					'type'		=> 'file',
					'id'			=> $prefix . 'field_file',
					'title'		=> esc_html__( 'Заголовок поля "file"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> '',
					'valbtn'		=> esc_html__( 'Загрузить', 'dmz_hram_site' ),
				],[
					'type'		=> 'gallery',
					'id'			=> $prefix . 'field_gallery',
					'title'		=> esc_html__( 'Заголовок поля "gallery"', 'dmz_hram_site' ),
					'desc'		=> esc_html__( 'Описание поля', 'dmz_hram_site' ),
					'std'			=> '',
					'valbtn'		=> esc_html__( 'Загрузить', 'dmz_hram_site' ),
				],
			],
		];
		
		return $meta_options;
	}