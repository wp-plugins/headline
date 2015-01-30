<?php
$headline_font_names = [];
$headline_font_list = '';
class HeadlinePlugin{

    private $plugin_path;
    private $plugin_url;
    private $l10n;
    private $pluginTemplate;
    private $namespace = _headline_plugin;
    private $settingName = 'Headline Plugin';

    function __construct() 
    {	
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );
        $this->l10n = 'wp-settings-framework';
        add_action( 'admin_menu', array(&$this, 'admin_menu'), 99 );
        
        // Include and create a new WordPressSettingsFramework
        require_once( $this->plugin_path .'wp-settings-framework.php' );
        
        $this->pluginTemplate = new WordPressSettingsFramework( $settings_file, $this->namespace, $this->get_settings() );
        
        add_action( 'init', array(&$this, 'headline_plugin_register_shortcodes'));
        add_action('wp_footer', array(&$this, 'headline_plugin_print_fonts'));
        add_action('init', array(&$this, 'add_headline_button_icon'));
       
    }
    
    function admin_menu()
    {
        $page_hook = add_menu_page( __( $this->settingName, $this->l10n ), __( $this->settingName, $this->l10n ), 'update_core', $this->settingName, array(&$this, 'settings_page') );
        add_submenu_page( $this->settingName, __( 'Settings', $this->l10n ), __( 'Settings', $this->l10n ), 'update_core', $this->settingName, array(&$this, 'settings_page') );
    }
    
    function settings_page()
	{
	   
	    
	    ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php $this->settingName ?></h2>
			
			<h3>Headline Instructions</h3>
			<p>Create a headline by placing the shortcode tag <code>[headline text="Check me out" font_size="60" color="red" font="Artifika"]</code> on your page or post.</p>
			<p>Use any Google Font you like in the font attribute.</p>
			<p>See how to use the headline plugin at our site,<a href="http://www.thinklandingpages.com/headline-plugin/">http://www.thinklandingpages.com/headline-plugin/</a></p>				
			
			<?php 
			$this->pluginTemplate->settings(); 
			?>
			
		</div>
		<?php
		
	}
	
	function validate_settings( $input ){
    		return $input;
	}
	
	
        
        function get_settings(){
        	$wpsf_settings[] = array(
		    'section_id' => 'general',
		    'section_title' => $this->settingName.' Settings',
		    //'section_description' => 'Some intro description about this section.',
		    'section_order' => 5,
		    'fields' => array(
		    /*
		      		 array(
			            'id' => 'to_email',
			            'title' => 'To Email',
			            'desc' => 'Set the email address you want your forms submitted to.',
			            'type' => 'text',
			            'std' => '',
			        ),    
		*/    
		        )
		        
        
    );
    return $wpsf_settings;
        }
       
        function headline_plugin_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'color' => 'gray',
			'text' => 'This is a headline',
			//'url' => '',
			'font_size' => '',
			'font' =>'',
		), $atts ) );
		global $headline_font_names;
		global $headline_font_list;
		ob_start();
		array_push($headline_font_names, $font);
		$headline_font_list = $headline_font_list.$font."|";
		echo '<span style="font-size:'.$font_size.'px;font-family:'.$font.';color:'.$color.'">'.$text.'</span>';
		return ob_get_clean();
		
	}
	function headline_plugin_register_shortcodes(){
		add_shortcode( 'headline', array(&$this, 'headline_plugin_shortcode') );
		
	}


	function headline_plugin_print_fonts(){
		global $headline_font_names;
		global $headline_font_list;
		
		foreach ($headline_font_names as $font) {
		    //echo "Current value of \$a: $v.\n";
		}
		wp_register_style("headline_fonts_style", "http://fonts.googleapis.com/css?family=".$headline_font_list);
		wp_enqueue_style("headline_fonts_style");
		
	}
	
	/* for headline button tinymce icon button   	*/
    	function add_headline_button_icon() {
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
	     return;
	   if ( get_user_option('rich_editing') == 'true') {
	     add_filter('mce_external_plugins', array(&$this,'add_headline_button_icon_tinymce_plugin'));
	     add_filter('mce_buttons',  array(&$this,'register_headline_button_icon'));
	   }
	}
	
	function register_headline_button_icon($buttons) {
	   array_push($buttons, "|", "headline_button");
	   return $buttons;
	}
	
	function add_headline_button_icon_tinymce_plugin($plugin_array) {
	   $plugin_array['headline_button'] = plugin_dir_url(__FILE__).'/js/headline_button_icon.js';
	   return $plugin_array;
	}

}
new HeadlinePlugin();

?>