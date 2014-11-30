<?php 

# require_once 'PHPUnit/Framework.php';
use \Ribbon\Map;

require_once __DIR__ . "/../../Ribbon/ClassLoader.php";
spl_autoload_register( array('\Ribbon\ClassLoader','loadClass'));

//require_once __DIR__ . '/../../Ribbon/Component.php';
//require_once __DIR__ . "/../../Ribbon/Map.php";
//require_once __DIR__ . "/../../Ribbon/Vector.php";

require_once __DIR__ . '/../Object.php';

class ObjectTest extends PHPUnit_Framework_testCase {
	
	public function testSample() {
		$this->assertTrue(true);
	}
	
	public function testBlog() {
		\MT\Object::install_properties(
		new Map(array(   "column_defs" => new Map(array(
			'id'                        => 'integer not null auto_increment',
			'parent_id'                 => 'integer',
			'theme_id'                  => 'string(255)',
			'name'                      => 'string(255) not null',
			'description'               => 'text',
			'archive_type'              => 'string(255)',
			'archive_type_preferred'    => 'string(25)',
			'site_path'                 => 'string(255)',
			'site_url'                  => 'string(255)',
			'days_on_index'             => 'integer',
			'entries_on_index'          => 'integer',
			'file_extension'            => 'string(10)',
			'email_new_comments'        => 'boolean',
			'allow_comment_html'        => 'boolean',
			'autolink_urls'             => 'boolean',
			'sort_order_posts'          => 'string(8)',
			'sort_order_comments'       => 'string(8)',
			'allow_comments_default'    => 'boolean',
			'server_offset'             => 'float',
			'convert_paras'             => 'string(30)',
			'convert_paras_comments'    => 'string(30)',
			'allow_pings_default'       => 'boolean',
			'status_default'            => 'smallint',
			'allow_anon_comments'       => 'boolean',
			'words_in_excerpt'          => 'smallint',
			'moderate_unreg_comments'   => 'boolean',
			'moderate_pings'            => 'boolean',
			'allow_unreg_comments'      => 'boolean',
			'allow_reg_comments'        => 'boolean',
			'allow_pings'               => 'boolean',
			'manual_approve_commenters' => 'boolean',
			'require_comment_emails'    => 'boolean',
			'junk_folder_expiry'        => 'integer',
			'ping_weblogs'              => 'boolean',
			'mt_update_key'             => 'string(30)',
			'language'                  => 'string(5)',
			'date_language'             => 'string(5)',
			'welcome_msg'               => 'text',
			'google_api_key'            => 'string(32)',
			'email_new_pings'           => 'boolean',
			'ping_blogs'                => 'boolean',
			'ping_technorati'           => 'boolean',
			'ping_google'               => 'boolean',
			'ping_others'               => 'text',
			'autodiscover_links'        => 'boolean',
			'sanitize_spec'             => 'string(255)',
			'cc_license'                => 'string(255)',
			'is_dynamic'                => 'boolean',
			'remote_auth_token'         => 'string(50)',
			'children_modified_on'      => 'datetime',
			'custom_dynamic_templates'  => 'string(25)',
			'junk_score_threshold'      => 'float',
			'internal_autodiscovery'    => 'boolean',
			'basename_limit'            => 'smallint',
			'use_comment_confirmation'  => 'boolean',
			'allow_commenter_regist'    => 'boolean',
			'use_revision'              => 'boolean',
			'archive_url'               => 'string(255)',
			'archive_path'              => 'string(255)',
			'content_css'               => 'string(255)',
			## Have to keep these around for use in mt-upgrade.cgi.
			'old_style_archive_links' => 'boolean',
			'archive_tmpl_daily'      => 'string(255)',
			'archive_tmpl_weekly'     => 'string(255)',
			'archive_tmpl_monthly'    => 'string(255)',
			'archive_tmpl_category'   => 'string(255)',
			'archive_tmpl_individual' => 'string(255)',
			## end of fields for mt-upgrade.cgi
		
			# meta properties
			'image_default_wrap_text'  => 'integer meta',
			'image_default_align'      => 'string meta',
			'image_default_thumb'      => 'integer meta',
			'image_default_width'      => 'integer meta',
			'image_default_wunits'     => 'string meta',
			'image_default_constrain'  => 'integer meta',
			'image_default_popup'      => 'integer meta',
			'commenter_authenticators' => 'string meta',
			'require_typekey_emails'   => 'integer meta',
			'nofollow_urls'            => 'integer meta',
			'follow_auth_links'        => 'integer meta',
			'update_pings'             => 'string meta',
			'captcha_provider'         => 'string meta',
			'publish_queue'            => 'integer meta',
			'nwc_smart_replace'        => 'integer meta',
			'nwc_replace_field'        => 'string meta',
			'template_set'             => 'string meta',
			'page_layout'              => 'string meta',
			'include_system'           => 'string meta',
			'include_cache'            => 'integer meta',
			'max_revisions_entry'      => 'integer meta',
			'max_revisions_template'   => 'integer meta',
			'theme_export_settings'    => 'hash meta',
			'category_order'           => 'text meta',
			'folder_order'             => 'text meta',
		
		)),
		"meta"    => 1,
		"audit"   => 1,
		"indexes" => new Map(array(
			"name"      => 1,
			"parent_id" => 1,
		)),
		"defaults"      => new Map(array( 'custom_dynamic_templates' => 'none', )),
		"child_classes" => new Map(array(
		'MT::Entry',        'MT::Page',
		'MT::Template',     'MT::Asset',
		'MT::Category',     'MT::Folder',
		'MT::Notification', 'MT::Log',
		'MT::ObjectTag',    'MT::Association',
		'MT::Comment',      'MT::TBPing',
		'MT::Trackback',    'MT::TemplateMap',
		'MT::Touch',
		)),
		"datasource"  => 'blog',
		"primary_key" => 'id',
		"class_type"  => 'blog',
		)
		));
		
	}

}


?>