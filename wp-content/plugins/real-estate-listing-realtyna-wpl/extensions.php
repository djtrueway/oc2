<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Extensions Library
 * @author Howard <howard@realtyna.com>
 * @package WPL
 * @since 1.0.0
 */
class wpl_extensions
{
    /**
     *
     * @var array of objects
     */
	public $extensions;

    /**
     * For getting an extension
     * @author Howard <howard@realtyna.com>
     * @param int $extension_id
     * @return object
     */
	public function get_extension($extension_id)
	{
		return wpl_db::get('*', 'wpl_extensions', 'id', $extension_id);
	}

    /**
     * For getting extensions
     * @author Howard <howard@realtyna.com>
     * @param int $enabled
     * @param string $type
     * @param int $client
     * @return object
     */
	public function get_extensions($enabled = 1, $type = '', $client = NULL)
	{
		$query = "SELECT * FROM `#__wpl_extensions` WHERE `enabled`>='$enabled' ".(trim($type) != '' ? "AND `type`='$type'" : "")." ".(trim($client) != '' ? "AND (`client`='$client' OR `client`='2')" : "")." ORDER BY `index` ASC";
		$this->extensions = wpl_db::select($query);

		return $this->extensions;
	}

    /**
     * For importing extensions automatically
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function import_extensions()
	{
		if(!$this->extensions) return;

		foreach($this->extensions as $extension)
		{
			if($extension->type == 'action') $this->import_action($extension);
			elseif($extension->type == 'shortcode') $this->import_shortcode($extension);
			elseif($extension->type == 'library') $this->import_library($extension);
			elseif($extension->type == 'service') $this->import_service($extension);
		}
	}

    /**
     * Returns extension types
     * @author Howard <howard@realtyna.com>
     * @param int $enabled
     * @return object
     */
	public function get_extensions_types($enabled = 0)
	{
		$query = "SELECT `id`, `type` FROM `#__wpl_extensions` WHERE `enabled`>='$enabled' GROUP BY `type` ORDER BY `type` ASC";
		return wpl_db::select($query);
	}

    /**
     * for importing actions
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public function import_action($extension)
	{
        $priority = trim($extension->param3) != '' ? $extension->param3 : 10;
        $args = trim($extension->param4) != '' ? $extension->param4 : 1;

		if(strpos($extension->param2, '->') === false)
		{
			add_action($extension->param1, $extension->param2, $priority, $args);
		}
		else
		{
			$ex = explode('->', $extension->param2);
			$class_name = $ex[0];

			/** generate object **/
			$class_obj = new $class_name();
			$function_name = $ex[1];

			add_action($extension->param1, array($class_obj, $function_name), $priority, $args);
		}
	}

    /**
     * Imports a shortcode
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public function import_shortcode($extension)
	{
		if(strpos($extension->param2, '->') === false)
		{
			add_shortcode($extension->param1, $extension->param2);
		}
		else
		{
			$ex = explode('->', $extension->param2);
			$class_name = $ex[0];

			/** generate object **/
			$class_obj = new $class_name();
			$function_name = $ex[1];

			add_shortcode($extension->param1, array($class_obj, $function_name));
		}
	}

    /**
     * Imports a stylesheet
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public static function import_style($extension)
	{
		/** render style_url **/
		$style_url = (isset($extension->external) or (isset($extension->param5) and trim($extension->param5))) ? $extension->param2 : wpl_global::get_wpl_asset_url($extension->param2);

		if(trim($extension->param2) != '') wp_register_style($extension->param1, $style_url);
    	wp_enqueue_style($extension->param1);
	}

    /**
     * Import a JS file
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @param boolean $footer
     * @return void
     */
	public static function import_javascript($extension, $footer = false)
	{
		/** render script_url **/
		$script_url = (isset($extension->external) or (isset($extension->param5) and trim($extension->param5))) ? $extension->param2 : wpl_global::get_wpl_asset_url($extension->param2);
		$in_footer = (isset($extension->param4) and trim($extension->param4)) ? $extension->param4 : $footer;

		if(trim($extension->param2) != '') wp_register_script($extension->param1, $script_url, array(), false, $in_footer);

	    wp_enqueue_script($extension->param1);
	}

    /**
     * Include a PHP library
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public function import_library($extension)
	{
		$function_name = $extension->param2;
		$function_name($extension->param1);
	}

    /**
     * Imports a widget
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public function import_widget($extension)
	{
		$path = _wpl_import($extension->param1, true, true);

		if(wpl_file::exists($path))
		{
			require_once $path;
            register_widget($extension->param3);
		}
	}

    /**
     * Imports a service
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return boolean
     */
	public function import_service($extension)
	{
		$ex = explode('->', $extension->param2);
		$class_file = $ex[0];
		$class_name = 'wpl_service_'.$ex[0];

		/** first validation **/
		if(trim($class_file) == '') return false;

		/** generate object **/
		_wpl_import('libraries.services.'.$class_file);

        /** return if service file is not exists **/
        if(!class_exists($class_name)) return false;

		$class_obj = new $class_name();
		$function_name = $ex[1];
		$priority = trim($extension->param3) != '' ? $extension->param3 : 10;

		return add_action($extension->param1, array($class_obj, $function_name), $priority);
	}

    /**
     * Imports a language file
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function import_language()
	{
        $locale = apply_filters('plugin_locale', get_locale(), 'real-estate-listing-realtyna-wpl');

        $text_domain_slug = wpl_path::clean(WP_LANG_DIR .DS. 'plugins' .DS. 'real-estate-listing-realtyna-wpl-'.$locale.'.mo');
        $text_domain_wpl = wpl_path::clean(WP_LANG_DIR .DS. WPL_BASENAME .DS. 'wpl'.'-'.$locale.'.mo');

        // 'real-estate-listing-realtyna-wpl' Text Domain
        if(wpl_file::exists($text_domain_slug)) load_textdomain('real-estate-listing-realtyna-wpl', $text_domain_slug);
        else load_plugin_textdomain('real-estate-listing-realtyna-wpl', false, WPL_BASENAME . DS . 'languages');

        // 'wpl' Text Domain
		if(wpl_file::exists($text_domain_wpl)) load_textdomain('wpl', $text_domain_wpl);
        else load_plugin_textdomain('wpl', false, WPL_BASENAME . DS . 'languages');
	}

    /**
     * For importing permalink
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function import_permalink()
	{
		add_action('wp_loaded', array($this, 'wpl_flush_rules'), 1);
		add_filter('rewrite_rules_array', array($this, 'wpl_insert_rewrite_rules'));
        add_filter('query_vars', array($this, 'wpl_insert_query_vars'));

		$sef = new wpl_sef();
		add_shortcode('WPL', array($sef, 'process'));
	}

    /**
     * For importing sidebar
     * @author Howard <howard@realtyna.com>
     * @param object $extension
     * @return void
     */
	public function import_sidebar($extension)
	{
        $name = (isset($extension->title) and trim($extension->title)) ? $extension->title : 'WPL sidebar';
        $id = (isset($extension->param1) and trim($extension->param1)) ? $extension->param1 : 'wpl-sidebar-id';
        $description = (isset($extension->description) and trim($extension->description)) ? $extension->description : 'WPL sidebar description';
        $before_widget = (isset($extension->param2) and trim($extension->param2)) ? $extension->param2 : '<aside id="%1$s" class="widget %2$s">';
        $after_widget = (isset($extension->param3) and trim($extension->param3)) ? $extension->param3 : '</aside>';
        $before_title = (isset($extension->param4) and trim($extension->param4)) ? $extension->param4 : '<h3 class="widget-title">';
        $after_title = (isset($extension->param5) and trim($extension->param5)) ? $extension->param5 : '</h3>';

		register_sidebar(array(
			'name'          => __($name, 'real-estate-listing-realtyna-wpl'),
			'id'            => $id,
			'description'   => __($description, 'real-estate-listing-realtyna-wpl'),
			'before_widget' => $before_widget,
			'after_widget'  => $after_widget,
			'before_title'  => $before_title,
			'after_title'   => $after_title,
		));
	}

    /**
     * Flushes Rewrite rules if WPL rules are not yet included
     * @author Howard <howard@realtyna.com>
     * @global object $wp_rewrite
     * @return void
     */
	public function wpl_flush_rules()
	{
		$rules = get_option('rewrite_rules');
        $wpl_rules = wpl_sef::get_main_rewrite_rule();

        $flushed = false;
        foreach($wpl_rules as $wpl_rule)
        {
            if($flushed or isset($rules[$wpl_rule['regex']])) continue;

            global $wp_rewrite;
            $wp_rewrite->flush_rules();
            $flushed = true;
        }
	}

    /**
     * Adds WPL rewrite rukes
     * @author Howard <howard@realtyna.com>
     * @param array $rules
     * @return array
     */
	public function wpl_insert_rewrite_rules($rules)
	{
        $wpl_rules = wpl_sef::get_main_rewrite_rule();

		$newrules = array();
		foreach($wpl_rules as $wpl_rule) $newrules[$wpl_rule['regex']] = $wpl_rule['url'];

		return $newrules + $rules;
	}

    /**
     * Adding the wpl query string var so that WP recognizes it
     * @author Howard <howard@realtyna.com>
     * @param array $vars
     * @return array
     */
	public function wpl_insert_query_vars($vars)
	{
		array_push($vars, 'wpl_qs');
		return $vars;
	}

    /**
     * Adding wpl TinyMCE buttons
     * @author Howard <howard@realtyna.com>
     */
	public function import_mce_buttons()
	{
        if(current_user_can('edit_posts') or current_user_can('edit_pages'))
        {
            add_filter('mce_external_plugins', array($this, 'register_shortcode_buttons'));
            add_filter('mce_buttons', array($this, 'add_shortcode_wizard'));
        }
    }

    /**
     * Adding shortcode wizard
     * @author Howard <howard@realtyna.com>
     * @param array $buttons
     * @return array
     */
	public function add_shortcode_wizard($buttons)
	{
		array_push($buttons, 'wplshortcode');
        return $buttons;
    }

    /**
     * Registering shortcode buttons
     * @author Howard <howard@realtyna.com>
     * @param array $plugin_array
     * @return array
     */
	public function register_shortcode_buttons($plugin_array)
	{
		$plugin_array['wplbuttons'] = wpl_global::get_wpl_asset_url('packages/mce_editor/wpl.js');
        return $plugin_array;
    }

    /**
     * Registering active and deactive functions for WPl
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function wpl_active_deactive()
	{
		register_activation_hook(WPL_ABSPATH.'WPL.php', array($this, 'activate_wpl'));
		register_deactivation_hook(WPL_ABSPATH.'WPL.php', array($this, 'deactivate_wpl'));
		register_uninstall_hook(WPL_ABSPATH.'WPL.php', array('wpl_extensions', 'uninstall_wpl'));
    }

    /**
     * Running installation queries and initializing WPL
     * @author Howard <howard@realtyna.com>
     * @param boolean $network_activate
     * @return void
     */
	public function activate_wpl($network_activate = false)
	{
        // Automatically Call WPL Tour
        update_option('wpl_auto_tour', 1);

        /** Call Franchise activate function **/
        if(wpl_global::is_multisite())
        {
            $fswpl = new wpl_addon_franchise_wpl();
            $fswpl->activate($network_activate);

            return;
        }

		if(wpl_folder::exists(WPL_ABSPATH. 'assets' .DS. 'install' .DS. 'files'))
		{
			/** copy files **/
			wpl_folder::copy(WPL_ABSPATH. 'assets' .DS. 'install' .DS. 'files', ABSPATH, '', true);

			/** delete files **/
			wpl_folder::delete(WPL_ABSPATH. 'assets' .DS. 'install' .DS. 'files');
		}

		/** run queries **/
		$query_file = WPL_ABSPATH. 'assets' .DS. 'install' .DS. 'queries.sql';
		if(wpl_file::exists($query_file))
		{
			$queries = wpl_file::read($query_file);
			$queries = str_replace(";\r\n", "-=++=-", $queries);
			$queries = str_replace(";\r", "-=++=-", $queries);
			$queries = str_replace(";\n", "-=++=-", $queries);

			$sqls = explode("-=++=-", $queries);

            foreach($sqls as $sql)
            {
                try{wpl_db::q(trim($sql));} catch (Exception $e){}
            }

            /** delete query file **/
            wpl_file::delete($query_file);
		}

		/** run script **/
		$script_file = WPL_ABSPATH. 'assets' .DS. 'install' .DS. 'script.php';
		if(wpl_file::exists($script_file))
		{
			include $script_file;

			/** delete script file **/
			wpl_file::delete($script_file);
		}

		/** create propertylisting page **/
        $pages = array('Properties'=>'[WPL]', 'For Sale'=>'[WPL sf_select_listing="9"]', 'For Rent'=>'[WPL sf_select_listing="10"]', 'Vacation Rental'=>'[WPL sf_select_listing="12"]');
        foreach($pages as $title=>$content)
        {
            if(wpl_db::select("SELECT COUNT(post_content) FROM `#__posts` WHERE `post_content` LIKE '%$content%' AND `post_status` IN ('publish', 'private')", 'loadResult') != 0) continue;

            $post = array('post_title'=>$title, 'post_content'=>$content, 'post_type'=>'page', 'post_status'=>'publish', 'comment_status'=>'closed', 'ping_status'=>'closed', 'post_author'=>1);
            $post_id = wp_insert_post($post);

            if($content == '[WPL]')
            {
                _wpl_import('libraries.settings');
                wpl_settings::save_setting('main_permalink', $post_id);
            }
        }

        /** Add admin user to WPL **/
        wpl_users::add_user_to_wpl(wpl_users::get_blog_admin_id());

        /** upgrade WPL **/
		self::upgrade_wpl();
    }

    /**
     * Running necesarry queries and functions for upgrading
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function upgrade_wpl()
	{
        /** Call Franchise upgrade function **/
        if(wpl_global::is_multisite())
        {
            $fswpl = new wpl_addon_franchise_wpl();
            $fswpl->upgrade();

            return;
        }

		if(wpl_folder::exists(WPL_ABSPATH. 'assets' .DS. 'upgrade' .DS. 'files'))
		{
			/** copy files **/
			wpl_folder::copy(WPL_ABSPATH. 'assets' .DS. 'upgrade' .DS. 'files', ABSPATH, '', true);

			/** delete files **/
			wpl_folder::delete(WPL_ABSPATH. 'assets' .DS. 'upgrade' .DS. 'files');
		}

		/** run queries **/
		$query_file = WPL_ABSPATH. 'assets' .DS. 'upgrade' .DS. 'queries.sql';
		if(wpl_file::exists($query_file))
		{
			$queries = wpl_file::read($query_file);
			$queries = str_replace(";\r\n", "-=++=-", $queries);
			$queries = str_replace(";\r", "-=++=-", $queries);
			$queries = str_replace(";\n", "-=++=-", $queries);

			$sqls = explode("-=++=-", $queries);

			foreach($sqls as $sql)
            {
                try{wpl_db::q(trim($sql));} catch (Exception $e){}
            }

            /** delete query file **/
            wpl_file::delete($query_file);
		}

		/** run script **/
		$script_file = WPL_ABSPATH. 'assets' .DS. 'upgrade' .DS. 'script.php';
		if(wpl_file::exists($script_file))
		{
			include $script_file;

			/** delete script file **/
			wpl_file::delete($script_file);
		}

		/** update WPL version in db **/
		update_option('wpl_version', wpl_global::wpl_version());
    }

    /**
     * Deactivating WPL
     * @author Howard <howard@realtyna.com>
     * @param boolean $network_deactivate
     * @return void
     */
	public function deactivate_wpl($network_deactivate = false)
	{
        /** Call Franchise deactivate function **/
        if(wpl_global::is_multisite())
        {
            $fswpl = new wpl_addon_franchise_wpl();
            $fswpl->deactivate($network_deactivate);

            return;
        }
	}

    /**
     * Uninstalling WPL
     * @author Howard <howard@realtyna.com>
     * @return boolean
     */
	public static function uninstall_wpl()
	{
        $tables = wpl_db::select('SHOW TABLES');
		$database = wpl_db::get_DBO();

		foreach($tables as $table_name=>$table)
		{
			if(strpos($table_name, $database->prefix.'wpl_') !== false)
			{
				/** drop table **/
				wpl_db::q("DROP TABLE `$table_name`");
			}
		}

        /** delete options **/
        wpl_db::q("DELETE FROM `#__options` WHERE `option_name` LIKE 'wpl_%' AND `option_name` NOT LIKE 'wpl_theme%'", 'delete');

        $upload_path = wpl_global::get_upload_base_path();
        if(wpl_file::exists($upload_path)) wpl_file::delete($upload_path);

        /** Call Franchise uninstall function **/
        if(wpl_global::is_multisite())
        {
            $fswpl = new wpl_addon_franchise_wpl();
            $fswpl->uninstall();
        }

        return true;
	}

    /**
     * Adding js dynamic vars to the head of page
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function import_dynamic_js()
	{
		echo '<script type="text/javascript">';
		echo 'wpl_baseUrl="'.wpl_global::get_wordpress_url().'";';
		echo 'wpl_baseName="'.WPL_BASENAME.'";';
		echo '</script>';
	}

    /**
     * Adding js dynamic vars to the head of page
     * @author Howard <howard@realtyna.com>
     * @param object $wp_admin_bar
     * @return void
     */
	public function plus_new_menu($wp_admin_bar)
	{
		$cur_user_id = wpl_users::get_cur_user_id();
		$cur_user_data = wpl_users::get_user($cur_user_id);

		if(wpl_users::is_administrator($cur_user_id) or isset($cur_user_data->data->wpl_data->id))
		{
			$wp_admin_bar->add_menu(array(
			   'id'=>'wpl_add_listings',
			   'title'=>__('WPL Listing', 'real-estate-listing-realtyna-wpl'),
			   'parent'=>'new-content',
			   'href'=>wpl_global::get_wp_admin_url().'admin.php?page=wpl_admin_add_listing'
			));
		}
	}

    /**
     * @deprecated
     */
    public function wpl_admin_pages()
    {
        self::wpl_admin_menus();
    }

    /**
     * For creating admin menus
     * @author Howard <howard@realtyna.com>
     */
	public function wpl_admin_menus()
	{
		$cur_user_id = wpl_users::get_cur_user_id();
		$cur_user_data = wpl_users::get_user($cur_user_id);

		$cur_role = wpl_users::get_role($cur_user_id, false);
		$wpl_roles = wpl_users::get_wpl_roles();
		$menus = wpl_global::get_menus('menu', 'backend');
		$submenus = wpl_global::get_menus('submenu', 'backend');

        // is any update available
        $available_updates = wpl_global::get_updates_count();

		/** generate pages object **/
		$controller = new wpl_controller();

		if(wpl_users::is_administrator($cur_user_id) or isset($cur_user_data->data->wpl_data->id))
		{
			/** add menus **/
			foreach($menus as $menu)
			{
				$role = $menu->capability == 'current' ? $cur_role : $wpl_roles[$menu->capability];
				$position = $menu->position ? $menu->position : NULL;
                $menu_title = __($menu->menu_title, 'real-estate-listing-realtyna-wpl').((wpl_users::is_administrator($cur_user_id) and $available_updates >= 1) ? '<span class="update-plugins update-wpl count-'.$available_updates.'"><span class="wpl-count">'.$available_updates.'</span></span>' : '');

				add_menu_page(__($menu->page_title, 'real-estate-listing-realtyna-wpl'), $menu_title, $role, $menu->menu_slug, array($controller, $menu->function), '', $position);
			}

			/** add sub menus **/
			foreach($submenus as $submenu)
			{
                if(!wpl_users::has_menu_access($submenu->menu_slug, $cur_user_id)) continue;

				$role = $submenu->capability == 'current' ? $cur_role : $wpl_roles[$submenu->capability];
				$menu_title = $submenu->separator ? $controller->wpl_add_separator().__($submenu->menu_title, 'real-estate-listing-realtyna-wpl') : __($submenu->menu_title, 'real-estate-listing-realtyna-wpl');

				add_submenu_page($submenu->parent, __($submenu->page_title, 'real-estate-listing-realtyna-wpl'), $menu_title, $role, $submenu->menu_slug, array($controller, $submenu->function));
			}
		}
	}

    /**
     * For creating admin bar menu
     * @author Howard <howard@realtyna.com>
     * @global object $wp_admin_bar
     * @return boolean
     */
	public function wpl_admin_bar_menu()
	{
        /** Don't show top bar menu on network admin **/
        if(is_network_admin()) return false;

		$cur_user_id = wpl_users::get_cur_user_id();
		$cur_user_data = wpl_users::get_user($cur_user_id);

		$menus = wpl_global::get_menus('menu', 'backend');
		$submenus = wpl_global::get_menus('submenu', 'backend');

		global $wp_admin_bar;

        // is any update available
        $available_updates = wpl_global::get_updates_count();

		/** generate pages object **/
		$controller = new wpl_controller();

		if(wpl_users::is_administrator($cur_user_id) or isset($cur_user_data->data->wpl_data->id))
		{
			/** add menus **/
			foreach($menus as $menu)
			{
				$menu_slug = (!wpl_users::is_administrator($cur_user_id) and $menu->capability != 'current') ? 'wpl_admin_profile' : $menu->menu_slug;
				$menu_title = __($menu->menu_title, 'real-estate-listing-realtyna-wpl').((wpl_users::is_administrator($cur_user_id) and $available_updates >= 1) ? '<span class="wpl-update-plugin-admin-bar update-wpl count-'.$available_updates.'"><span class="wpl-count">'.$available_updates.'</span></span>' : '');

				$wp_admin_bar->add_menu(array(
					'id'=>$menu->menu_slug,
					'title'=>$menu_title,
					'href'=>wpl_global::get_wp_admin_url().'admin.php?page='.$menu_slug,
				));
			}

			/** add sub menus **/
			foreach($submenus as $submenu)
			{
                if(!wpl_users::has_menu_access($submenu->menu_slug)) continue;
				if(!wpl_users::is_administrator($cur_user_id) and $submenu->capability != 'current') continue;

				$menu_title = $submenu->separator ? $controller->wpl_add_separator().__($submenu->menu_title, 'real-estate-listing-realtyna-wpl') : __($submenu->menu_title, 'real-estate-listing-realtyna-wpl');

				$wp_admin_bar->add_menu(array(
					'id'=>$submenu->menu_slug,
					'parent'=>$submenu->parent,
					'title'=>$menu_title,
					'href'=>wpl_global::get_wp_admin_url().'admin.php?page='.$submenu->menu_slug,
				));
			}
		}

		return true;
	}

    /**
     * for adding page number to listing pages
     * @author Howard <howard@realtyna.com>
     * @param string $title
     * @return string
     */
	public function wp_title($title)
	{
		$wplview = wpl_request::getVar('wplview');
		$wplpage = wpl_request::getVar('wplpage');

		if(in_array($wplview, array('property_listing', 'profile_listing')) and $wplpage >= 2)
		{
            if(is_string($title) and trim($title) != '')
            {
                /** has HTML tag **/
                if(strpos($title, '</') != false) return $title;

                return wpl_global::clean($title.' -- '.__('Page', 'real-estate-listing-realtyna-wpl').' '.$wplpage);
            }
            elseif(is_array($title))
            {
                $title['title'] .= ' -- '.sprintf(__('Page %s', 'real-estate-listing-realtyna-wpl'), $wplpage);
            }
		}

		return $title;
	}

    /**
     * For adding styles and scripts
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public static function import_styles_scripts()
	{
		$wpl_extensions = new wpl_extensions();

		$javascripts = $wpl_extensions->get_extensions(1, 'javascript', wpl_global::get_client());
		foreach($javascripts as $javascript)
		{
			$wpl_extensions->import_javascript($javascript);
		}

		$styles = $wpl_extensions->get_extensions(1, 'style', wpl_global::get_client());
		foreach($styles as $style)
		{
			$wpl_extensions->import_style($style);
		}

        // Load WPL RTL Style
        if(is_rtl())
        {
            // Frontend
            if(!wpl_global::get_client())
            {
                wp_dequeue_style('wpl_frontend_main_style');
                wp_deregister_style('wpl_frontend_main_style');

                $wpl_extensions->import_style((object) array('param1'=>'wpl_rtl_style', 'param2'=>'css/frontend-rtl.css'));
            }
            // Backend
            elseif(wpl_global::get_client())
            {
                wp_dequeue_style('wpl_backend_main_style');
                wp_deregister_style('wpl_backend_main_style');

                $wpl_extensions->import_style((object) array('param1'=>'wpl_rtl_style_backend', 'param2'=>'css/backend-rtl.css'));
            }
        }
	}

    public static function register_sidebars()
    {
        $wpl_extensions = new wpl_extensions();

        $sidebars = $wpl_extensions->get_extensions(1, 'sidebar', wpl_global::get_client());
        foreach($sidebars as $sidebar)
        {
            $wpl_extensions->import_sidebar($sidebar);
        }
    }

	public static function register_widgets()
	{
		$wpl_extensions = new wpl_extensions();

		$widgets = $wpl_extensions->get_extensions(1, 'widget', wpl_global::get_client());
		foreach($widgets as $widget)
		{
			$wpl_extensions->import_widget($widget);
		}
	}

    /**
     * Get plugin links
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $links
     * @param string $file
     * @return array
     */
    public static function wpl_plugin_links($links, $file)
    {
        if(strpos($file, WPL_BASENAME) !== false)
        {
            $links[] = '<a href="'.wpl_global::get_wp_admin_url().'admin.php?page=wpl_admin_settings">'.__('Settings', 'real-estate-listing-realtyna-wpl').'</a>';
            $links[] = '<a href="http://wpl.realtyna.com/wassets/wpl-manual.pdf" target="_blank">'.__('WPL Manual', 'real-estate-listing-realtyna-wpl').'</a>';
            $links[] = '<a href="http://wpl.realtyna.com/redirect.php?action=shop" target="_blank">'.__('WPL Add-ons', 'real-estate-listing-realtyna-wpl').'</a>';
        }

        return $links;
    }

    /**
     * This function called on init hook of WordPress so you can include any code to this function
     * @author Howard <howard@realtyna.com>
     */
    public function init()
    {
        /** import languages **/
        $this->import_language();

        /** import TinyMCE buttons **/
        $this->import_mce_buttons();

        /** Import WordPress pluggable functions **/
        _wp_import('wp-includes.pluggable');
    }

    public function import_page_builders()
    {
        // Divi Builder
		add_action('divi_extensions_init', array($this, 'import_page_builders_divi'));

        // Visual Composer
        _wpl_import('libraries.page_builders.vc');

        // Elementor Page Builder
        _wpl_import('libraries.page_builders.elementor');
    }

    public function import_page_builders_divi()
    {
        _wpl_import('libraries.page_builders.divi');
    }
}

/** load extensions **/
$wpl_extensions = new wpl_extensions();

/** active deactive functions **/
$wpl_extensions->wpl_active_deactive();

/** include some addon libraries **/
_wpl_import('libraries.addon_pro');
_wpl_import('libraries.addon_franchise');

// WPL is installed
if(wpl_global::get_wp_option('wpl_version'))
{
	$wpl_extensions->get_extensions(1, '', wpl_global::get_client());
	$wpl_extensions->import_extensions();

    // Registering Page Builder Compatibilities
    $wpl_extensions->import_page_builders();

	if(version_compare(wpl_global::get_wp_option('wpl_version'), wpl_global::wpl_version(), '<'))
	{
		/** upgrading WPL **/
		$wpl_extensions->upgrade_wpl();
	}
}

// Initialize WPL
add_action('init', array($wpl_extensions, 'init'));

// listing menu in +new menu
add_action('admin_bar_menu', array($wpl_extensions, 'plus_new_menu'), 99);

// import dynamic js
add_action('wp_head', array($wpl_extensions, 'import_dynamic_js'), 1);
add_action('admin_print_scripts', array($wpl_extensions, 'import_dynamic_js'), 1);

// add javascripts and styles
if(wpl_global::get_client() == '0') add_action('wp_enqueue_scripts', array($wpl_extensions, 'import_styles_scripts'), 0);
elseif(wpl_global::get_client() == '1') add_action('admin_enqueue_scripts', array($wpl_extensions, 'import_styles_scripts'), 0);

// Registering sidebars
add_action('widgets_init', array($wpl_extensions, 'register_sidebars'), 0);
add_action('widgets_init', array($wpl_extensions, 'register_widgets'), 0);

add_action('login_enqueue_scripts', array($wpl_extensions, 'import_styles_scripts'), 0);

// filter title
add_filter('wp_title', array($wpl_extensions, 'wp_title'), 999);
add_filter('document_title_parts', array($wpl_extensions, 'wp_title'), 999);

// plugin links
add_filter('plugin_row_meta', array($wpl_extensions, 'wpl_plugin_links'), 10, 2);

// import permalink
$wpl_extensions->import_permalink();