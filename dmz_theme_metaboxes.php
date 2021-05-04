<?php
/** 
* Plugin Name: 	Metaboxes dmzTheme
* Description: 	Плагин для создания метабоксов вашей темы.
* Version: 		1.0.0
* Author: 		Dm Zabelin
* Author URI: 	https://github.com/dmZabelin
* Text Domain: 	dmz_theme
* Domain Path:	/languages
**/

if( !defined('ABSPATH') ) {
	exit;
}

/**
* *Инициализация класса, получение данных $meta_options
**/

function call_DmzMetaBoxes() 
{
	
	if ( !function_exists( 'dmz_do_meta_boxes' ) ) {
		return;
	}
	
	add_filter( 'dmz_meta_boxes', 'dmz_do_meta_boxes' );
	
	$meta_options = [];
	$meta_options = apply_filters ( 'dmz_meta_boxes', $meta_options );

	foreach ( $meta_options as $meta_option ) {
		new DmzMetaBoxes( $meta_option );
	}	
}

if (is_admin()) {
	add_action( 'init', 'call_DmzMetaBoxes' );
}

/**
 * *Создаем мета боксы
**/

class DmzMetaBoxes 
{
	protected $meta_options;

	function __construct( $meta_option ) 
	{

		if ( !is_admin() ) return;

		$this->meta_options = $meta_option;

		$upload = false;
		foreach ( $meta_option['fields'] as $field ) 
		{
			if ( $field['type'] == 'file' || $field['type'] == 'gallery') 
			{
				$upload = true;
				break;
			}
		}
		
		global $pagenow;
		if ( $upload && in_array( $pagenow, [ 'page.php', 'page-new.php', 'post.php', 'post-new.php' ] ) ) 
		{
			add_action( 'admin_head', [ &$this, 'add_post_enctype' ] );
			add_action( 'admin_print_scripts', [ &$this, 'dmz_admin_scripts' ] );
		}

		add_action( 'add_meta_boxes', [ &$this, 'create' ] );
		add_action( 'save_post', [ &$this, 'save' ] );

		add_filter( 'dmz_show_on', [ &$this, 'dmz_add_for_page_template' ], 10, 2 );
	}

	function dmz_admin_scripts() 
	{
		wp_enqueue_style( 'dmz-meta-css', plugins_url( '/src/css/main.css' , __FILE__ ) );
		wp_enqueue_media();
		wp_register_script( 'dmz-file-upload-scripts', plugins_url( '/src/js/file-upload.js' , __FILE__ ), [ 'jquery' ] );
		wp_register_script( 'dmz-gallery-upload-scripts', plugins_url( '/src/js/gallery-upload.js', __FILE__ ), [ 'jquery', 'jquery-ui-sortable' ] );
		wp_enqueue_script( 'dmz-file-upload-scripts' );
		wp_enqueue_script( 'dmz-gallery-upload-scripts' );
	}

	function add_post_enctype() 
	{
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	/**
	 * *Добавляем метабоксы
	**/

	function create()
	{
		$this->meta_options['context'] = empty( $this->meta_options['context'] ) ? 'normal' : $this->meta_options['context'];
		$this->meta_options['priority'] = empty( $this->meta_options['priority'] ) ? 'high' : $this->meta_options['priority'];
		$this->meta_options['show_on'] = empty( $this->meta_options['show_on'] ) ? ['key' => false, 'value' => false] : $this->meta_options['show_on'];
		

		foreach ( $this->meta_options['post_type'] as $post_type ) 
		{
			if( apply_filters( 'dmz_show_on', true, $this->meta_options ) )
			add_meta_box( 
				$this->meta_options['id'], 
				$this->meta_options['title'], 
				[&$this, 'show_meta_content'], 
				$post_type, 
				$this->meta_options['context'], 
				$this->meta_options['priority']
			);
		}
	}

	/**
	* *Добавляю фильтр
	* *Используйте фильтр «dmz_show_on» для дальнейшего уточнения условий, при которых отображается метабокс.
	* *Ниже вы можете ограничить его по шаблону страницы
	**/
	
	function dmz_add_for_page_template( $display, $meta_option ) 
	{
		if( 'page-template' !== $meta_option['show_on']['key'] )
			return $display;

		if( isset( $_GET['post'] ) ) $post_id = $_GET['post']; 
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID']; 
		if( !( isset( $post_id ) || is_page() ) ) return false;
	
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );

		$meta_option['show_on']['value'] = !is_array( $meta_option['show_on']['value'] ) ? [$meta_option['show_on']['value']] : $meta_option['show_on']['value'];

		if( in_array( $current_template, $meta_option['show_on']['value'] ) ) 
			return true;
		else
			return false;
	}

	/**
	* *Функция отображения полей
	**/

	function show_meta_content( $post ) 
	{

		// Использую одноразовый номер для проверки
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
		echo '<ul class="dmz_do_meta_boxes">';

			foreach ( $this->meta_options['fields'] as $field ) 
			{

				// Устанавливаю пустые значения по умолчанию
				if ( !isset( $field['title'] ) ) $field['title'] = '';
				if ( !isset( $field['desc'] ) ) $field['desc'] = '';
				if ( !isset( $field['std'] ) ) $field['std'] = '';
				if ( !isset( $field['valbtn'] ) ) $field['valbtn'] = '';

				$meta = get_post_meta( $post->ID, $field['id'], true ); 

				echo '<li>';

				switch ( $field['type'] ) 
				{
					case 'title':
						echo '<h5 class="dmz_meta_title">', esc_attr( $field['title'] ), '</h5>';
						break;
					case 'text':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<input type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', '' !== $meta ? $meta : esc_attr( $field['std'] ), '" />';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'textarea':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<textarea name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" cols="60" rows="10">', '' !== $meta ? $meta : esc_attr( $field['std'] ), '</textarea>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'textarea_code':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<textarea name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" cols="60" rows="10" class="dmz_textarea_code">', '' !== $meta ? $meta : $field['std'], '</textarea>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'wysiwyg':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						wp_editor( $meta ? $meta : $field['std'], $field['id'] );
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'radio':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<ul>';
						$i = 1;
						foreach ( $field['options'] as $option ) 
						{
							echo '<li><input type="radio" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), $i,'" value="', esc_attr( $option['value'] ), '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', esc_attr( $field['id'] ), $i, '">', esc_attr( $option['name'] ).'</label></li>';
							$i++;
						}
						echo '</ul>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'radio_on_of':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						if( empty( $meta ) && !empty( $field['std'] ) ) $meta = $field['std'];
						echo '<ul>';
						$i = 1;
						foreach ( $field['options'] as $option ) 
						{
							echo '<li class="dmz_radio_btn"><input type="radio" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), $i,'" value="', esc_attr( $option['value'] ), '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', esc_attr( $field['id'] ), $i, '">', esc_attr( $option['name'] ).'</label></li>';
							$i++;
						}
						echo '</ul>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'select':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<select name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '">';
						foreach ( $field['options'] as $option ) 
						{
							echo '<option value="', esc_attr( $option['value'] ), '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', esc_attr( $option['name'] ), '</option>';
						}
						echo '</select>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'checkbox':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<input type="checkbox" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '"', $meta ? ' checked="checked"' : '', ' />';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'file':
						$check = get_attached_file( $meta );
						$image_url = wp_get_attachment_image_url( $meta, 'thumbnail', 1 );
						$file_mime = wp_check_filetype( basename( get_attached_file( $meta ) ) );
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<span class="btn-wrap', ( $check ) ? ' dmz_hidden' : '', '">';
						echo '<a data-field-id="', esc_attr( $field['id'] ), '" class="', esc_attr( $field['id'] ), '-file-add file-add button" href="#" data-uploader-title="', esc_html__( 'Добавить файл','dmz_hram_site' ), '" data-uploader-button-text="', esc_html__( 'Добавить','dmz_hram_site' ), '">';
						echo $field['valbtn'];
						echo '</a>';
						echo '<span>Файл не выбран</span>';
						echo '</span>';
						echo '<div id="', esc_attr( $field['id'] ), '-file-box" class="file-box">';
						echo '<div class="file-holder">';
						if ( $check && $file_mime['type'] == 'image/jpeg' || $file_mime['type'] == 'image/png' || $file_mime['type'] == 'image/gif') :
							echo '<input type="hidden" name="', esc_attr( $field['id'] ), '" value="', esc_attr( $meta ), '">';
							echo '<span class="file-image-preview file-item">';
							echo '<img  src="', esc_url( $image_url ), '">';
							echo'</span>';
							echo '<div class="buttons_manage">';
							echo '<a data-field-id="', esc_attr( $field['id'] ), '" class="change-file button button-primary button-medium" href="#" data-uploader-title="', esc_html__( 'Изменить добавленный файл','dmz_hram_site' ), '" data-uploader-button-text="', esc_html__( 'Изменить','dmz_hram_site' ), '"><i class="fa fa-cog" aria-hidden="true"></i></a>';
							echo '<a data-field-id="', esc_attr( $field['id'] ), '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>';
							echo '</div>';
						elseif( $check ): 
							echo '<input type="hidden" name="', esc_attr( $field['id'] ), '" value="', esc_attr( $meta ), '">';
							echo '<div class="file-item">';
							echo '<span class="file-image-preview">';
							echo '<img  src="', esc_url( $image_url ), '">';
							echo'</span>';
							echo '<div class="file-info">';
							echo '<h4>', get_the_title( $meta ), '</h4>';
							echo '<strong>', esc_html__( 'Имя файла: ', 'dmz_hram_site' ), '</strong><a href="', wp_get_attachment_url( $meta ), '" download>', basename( get_attached_file( $meta ) ), '</a><br>';
							echo '<p class="file-size"><strong>', esc_html__( 'Размер файла: ', 'dmz_hram_site' ), '</strong>', FileSizeConvert( filesize(get_attached_file( $meta ) ) ), '</p>';
							echo '</div>';
							echo'</div>';
							echo '<div class="buttons_manage">';
							echo '<a data-field-id="', esc_attr( $field['id'] ), '" class="change-file button button-primary button-medium" href="#" data-uploader-title="', esc_html__( 'Изменить добавленный файл','dmz_hram_site' ), '" data-uploader-button-text="', esc_html__( 'Изменить','dmz_hram_site' ), '"><i class="fa fa-cog" aria-hidden="true"></i></a>';
							echo '<a data-field-id="', esc_attr( $field['id'] ), '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>';
							echo '</div>';
						endif;
					echo '</div></div>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					case 'gallery':
						echo '<label class="dmz_meta_label" for="', $field['id'], '">', $field['title'], '</label>';
						echo '<a data-list="', esc_attr( $field['id'] ), '" class="gallery-add button" href="#" data-uploader-title="', esc_html__( 'Добавить изображение(я) в Галерею','dmz_hram_site' ), '" data-uploader-button-text="', esc_html__( 'Добавить','dmz_hram_site'), '">';
						echo $field['valbtn'];
						echo '</a>';
						echo '<ul id="', esc_attr( $field['id'] ),'-metabox-list" class="gallery_metabox_list">';
						if ($meta) : foreach ( $meta as $key => $value ) : $image = wp_get_attachment_image_src( $value );
							echo '<li class="image_holder">';
							echo '<input type="hidden" name="', esc_attr( $field['id'] . '[' . $key .']' ), '" value="', esc_attr( $value ), '">';
							echo '<img class="image-preview" src="' , esc_url( $image[0] ), '">';
							echo '<div class="buttons_manage">';
							echo '<a class="change-image button button-primary button-medium" href="#" data-uploader-title="', esc_html__( 'Изменить изображение','dmz_hram_site' ), '" data-uploader-button-text="', esc_html__( 'Изменить','dmz_hram_site' ), '"><i class="fa fa-cog" aria-hidden="true"></i></a>';
							echo '<a data-list="', esc_attr( $field['id'] ), '" class="remove-image button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>';
							echo '</div></li>';
						endforeach; endif;
						echo '</ul>';
						echo '<p class="dmz_meta_desc">', esc_attr( $field['desc'] ), '</p>';
						break;
					default:
						do_action( 'dmz_render_' . $field['type'] , $field, $meta );
				}
				echo '</li>';
			}
		echo '</ul>';
	}	
	
// Save data from metabox
	function save( $post_id )
	{
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;

		foreach ( $this->meta_options['fields'] as $field ) 
		{
			$name = $field['id'];

			$old = get_post_meta( $post_id, $name, true );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;

			if ( ( $field['type'] == 'textarea' ) ) {
				$new = dmz_wp_kses( $new );
			}
			if ( ( $field['type'] == 'textarea_code' ) ) {
				$new = htmlspecialchars_decode( $new );
			}
			
			$new = apply_filters( 'dmz_validate_' . $field['type'], $new, $post_id, $field );
		
			if ( '' !== $new && $new != $old ) {
				update_post_meta( $post_id, $name, $new );
			} elseif ( '' == $new ) {
				delete_post_meta( $post_id, $name );
			}
			if ( 'file' == $field['type'] ) {
				$name = $field['id'] . "_id";
				$old = get_post_meta( $post_id, $name, true );
				if ( isset( $field['save_id'] ) && $field['save_id'] ) {
					$new = isset( $_POST[$name] ) ? $_POST[$name] : null;
				} else {
					$new = "";
				}

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $name, $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $name, $old );
				}
			}
		}
	}	
}

// *Функция вывода метаданных
function dmz_meta( $key, $single = true, $post_id = null ) 
{
	echo dmz_get_meta( $key, $single, $post_id );
}

// *Функция получения метаданных
function dmz_get_meta( $key, $single = true, $post_id = null ) 
{
	if ( null === $post_id ):
		$post_id = get_the_ID();
	endif;
	$key = 'dmz_' . $key;
	return get_post_meta( $post_id, $key, $single );
}