<?php

if( !defined('WP_UNINSTALL_PLUGIN') ) {
	exit;
}


$meta_key = 'dmz_geo_mobile';
$deleted = delete_metadata( 'post', '', $meta_key, '', true ); 