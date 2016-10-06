<?php
/*
Plugin Name: link-log
Plugin URI: http://smartware.cc/wp-link-log
Description: Log external link clicks
Version: 2.1
Author: smartware.cc, Peter's Plugins
Author URI: http://smartware.cc
License: GPL2
*/

/*  Copyright 2015 Peter Raschendorfer (email : sw@smartware.cc)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LinkLog {
  public $plugin_name;
  public $plugin_slug;
  public $version;
  public $settings;
  public $admin_available_views;
    
  public function __construct() {
		$this->plugin_name = 'link-log';
    $this->plugin_slug = 'link-log';
		$this->version = '2.0';
    $this->get_settings();
    $this->init();
    $this->init_admin();
	} 
  
  // get all settings
  private function get_settings() {
    $this->settings = array();
    $this->settings['urlparam'] = get_option( 'swcc_linklog_urlparam', 'goto' );
    $this->settings['iplockparam'] = get_option( 'swcc_linklog_iplockparam', '0' );
    $this->settings['omitbotsparam'] = ( ( get_option( 'swcc_linklog_omitbotsparam', '0' ) == '1' ) ? true : false );
    $this->settings['nofollowparam'] = ( ( get_option( 'swcc_linklog_nofollowparam', '0' ) == '1' ) ? true : false );
    $this->settings['installed_version'] = get_option( 'swcc_linklog_version', 'NONE' );
    $this->settings['automationparam'] = get_option( 'swcc_linklog_automationparam', 'AUTO' );
    $this->settings['menutitle'] = get_option( 'swcc_linklog_menutitle', $this->plugin_name );
    $this->settings['pagetitle'] = get_option( 'swcc_linklog_pagetitle', 'link-log Link Click Analysis' );
  }
  
  // *** main ***
  private function init() {
    register_activation_hook( __FILE__, array( $this, 'install' ) );
    add_action( 'plugins_loaded', array( $this, 'update' ) ) ;
    add_action( 'wpmu_new_blog', array( $this, 'new_blog' ), 10, 6 );
    if ( $this->settings['automationparam'] != 'NEVER' ) {
      // If Automation is set to "AUTO" or "CUSTOM" we have to process all posts
      // IF Automation is set to "NEVER" we do not add the filter so does not run needless
      add_filter( 'the_content', array( $this, 'parse_content' ) );
    }
    if ( $this->settings['automationparam'] == 'CUSTOM' ) {
      // If Automation is set to "CUSTOM" we have to add a meta box to post and page Writing Screen 
      add_action( 'add_meta_boxes', array( $this, 'add_customization_meta_box' ) );
      add_action( 'save_post', array( $this, 'save_customization_meta_data' ) );
    }
    add_filter( 'query_vars', array( $this, 'add_queryvar' ) );
    add_action( 'init', array( $this, 'redirect' ), 1 );
    add_action( 'admin_menu', array( $this, 'adminmenu' ) );
    add_action( 'admin_init', array( $this, 'register_settings' ) );
    add_action( 'admin_head', array( $this, 'admin_style' ) );
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_list_links' ) ); 
    add_action( 'wp_ajax_linklog_list_descriptions', array( &$this, 'ajax_list_descriptions' ) );
   }
  
  // add links in plugin list
  function add_plugin_list_links( $links ) {
    return array_merge( $links, array( '<a href="' . admin_url( 'options-general.php?page=link-log-settings' ) . '">' . __( 'Settings' ) . '</a>', '<a href="' . admin_url( 'tools.php?page=link-log-log' ) . '">Click Analysis</a>') );
  }
  
  // only for backend
  private function init_admin() {
    if ( is_admin() ) {
        
      if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
      }
      
      $this->admin_available_views = array(   
        '7d' => array( 
          'title' => 'Last 7 Days', 
          'current_first' => strtotime( '-8 days' ),
          'current_last' => strtotime( '-1 day' ),
          'previous_first' => strtotime( '-16 days' ),
          'previous_last' => strtotime( '-9 days' )
        ),    
        '14d' => array( 
          'title' => 'Last 14 Days', 
          'current_first' => strtotime( '-15 days' ),
          'current_last' => strtotime( '-1 day' ),
          'previous_first' => strtotime( '-30 days' ),
          'previous_last' => strtotime( '-16 days' )
        ),
        '30d' => array( 
          'title' => 'Last 30 Days', 
          'current_first' => strtotime( '-31 days' ),
          'current_last' => strtotime( '-1 day' ),
          'previous_first' => strtotime( '-62 days' ),
          'previous_last' => strtotime( '-32 days' )
        ),
        'cm' => array( 
          'title' => 'Current Month', 
          'current_first' => mktime( 0, 0, 0, date( 'm' ), 1, date( 'Y' ) ),
          'current_last' => time(),
          'previous_first' => mktime( 0, 0, 0, date( 'm' ) - 1, 1, date( 'Y' ) ),
          'previous_last' => strtotime( '-1 month' )
        ),
        'lm' => array( 
          'title' => 'Last Month', 
          'current_first' => mktime( 0, 0, 0, date( 'm' ) - 1, 1, date( 'Y' ) ),
          'current_last' => mktime( 0, 0, 0, date( 'm' ), 0, date( 'Y' ) ),
          'previous_first' => mktime( 0, 0, 0, date( 'm' ) - 2, 1, date( 'Y' ) ),
          'previous_last' => mktime( 0, 0, 0, date( 'm' ) - 1, 0, date( 'Y' ) ),
        ),
        'cy' => array( 
          'title' => 'Current Year', 
          'current_first' => mktime( 0, 0, 0, 1, 1, date( 'Y' ) ),
          'current_last' => time(),
          'previous_first' => mktime( 0, 0, 0, 1, 1, date( 'Y' ) - 1 ),
          'previous_last' => strtotime( '-1 year' )
        ),
        'ly' => array( 
          'title' => 'Last Year', 
          'current_first' => mktime( 0, 0, 0, 1, 1, date( 'Y' ) - 1 ),
          'current_last' => mktime( 0, 0, 0, 12, 31, date( 'Y' ) - 1 ),
          'previous_first' => mktime( 0, 0, 0, 1, 1, date( 'Y' ) - 2 ),
          'previous_last' => mktime( 0, 0, 0, 12, 31, date( 'Y' ) - 2 )
        )
      );
    }
  }
  
  // parse content and rewrite all external urls
  function parse_content( $content ) {
    global $post;
    $process = true;
    if ( $this->settings['automationparam'] == 'CUSTOM' ) {
      if ( get_post_meta( $post->ID, '_linklog_custom_process_this', true ) != '1' ) {
        $process = false;
      }
    }
    if ( $process ) {
      $content = preg_replace_callback( "/<a(\s[^>]*)href=[\"\']??([^\" >]*?)[\"\']??([^>]*)>(.*)<\/a>/siU", array( $this, 'change_link' ), $content );
    }
    return $content;
  }
  
  // callback function to change the link
  function change_link( $linkparts ) {
    $add = '';
    if ( $this->settings['nofollowparam'] ) {
      if ( strpos( str_replace( "'", '"', strtolower( $linkparts[1] . $linkparts[3] ) ), 'rel="nofollow"' ) === false ) {
        $add = ' rel="nofollow"';
      }
    }
    return '<a' . $linkparts[1].' href="' . $this->make_url( $linkparts[2] ) . '"' . $linkparts[3] . $add . '>' . $linkparts[4] . '</a>'; 
  }
  
  // make the url
  function make_url ( $url ) {
    $url = str_replace( '&#038;', '&', str_replace( '&amp;', '&', str_replace( array( "\n", "\r") , '', $url ) ) );
    if ( ( substr( strtolower( $url ), 0, 7 ) == 'http://' || substr( strtolower( $url ), 0, 8 ) == 'https://' ) &&  substr( strtolower( $url ), 0, strlen( home_url() ) ) != strtolower( home_url() ) && substr( strtolower( $url ), 0, strlen( admin_url() ) ) != strtolower( admin_url() ) && substr( strtolower( $url ), 0, strlen( content_url() ) ) != strtolower( content_url() ) && substr( strtolower( $url ), 0, strlen( plugins_url() ) ) != strtolower( plugins_url() ) ) {
      $url = home_url() . '?' . $this->settings['urlparam'] . '=' . urlencode( base64_encode ( $this->xor_this( $url ) ) );
    }
    return $url;
  }
  
  // very simple encryption / decryption
  function xor_this( $in ) {
    // use NONCE_SALT 
    if ( defined( 'NONCE_SALT' ) && NONCE_SALT != '' ) {
      $key = NONCE_SALT;
    } else {
      $key = 'XRaXwx-6o-sJO~llAXj>}-Eto,yrW&qdj+]TE_wN7?bl|+`0JZ/d^u evtiRNQEQ';
    }
    $out = '';
    for( $i = 0; $i < strlen( $in ); ) {
      for( $j = 0; ( $j < strlen( $key ) && $i < strlen( $in) ); $j++ , $i++ ) {
         $out .= $in{$i} ^ $key{$j};
      }
    }  
    return $out;
  }

  // add given url parameter to query vars
  function add_queryvar ($qvars) {
    $qvars[] =  $this->settings['urlparam'];
    return $qvars;
  }
  
  // get ip address
  private function get_client_ip() {
    $ipaddress = '';
    if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    $ipaddress = trim( $ipaddress );
    if ( false === filter_var( $ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ) {
      $ipaddress = '';
    }
    $ipaddress = esc_attr( $ipaddress );
    return $ipaddress;
  }
  
  // function to detect if visitor is a bot
  function is_bot() {
    $bots = array( 
      'googlebot', 
      'msnbot', 
      'baiduspider', 
      'bingbot', 
      'slurp', 
      'yahoo', 
      'askjeeves', 
      'fastcrawler', 
      'infoseek', 
      'lycos', 
      'yandex', 
      'teoma', 
      'ia_archiver', 
      'webmon', 
      'webcrawler', 
      'findlink',
      'exabot',
      'gigabot',
      'msrbot',
      'seekbot',
      'yacybot',
      'mj12bot',
      'yanga',
      'domaincrawler',
      'facebookexternalhit',
      'openindexspider',
      'backlinkcrawler',
      'alexa',
      'froogle',
      'inktomi',
      'looksmart',
      'firefly',
      'ask jeeves',
      'webfindbot',
      'zyborg',
      'feedfetcher-google',
      'twitturls'
    );
    return ( ( preg_match( '/' . implode( '|', $bots ) . '/', strtolower( $_SERVER['HTTP_USER_AGENT'] ) ) > 0) ? true : false );
  }
  
  // log and redirect
  function redirect() {
    if ( !is_admin() and isset( $_GET ) ) {
      $urlparam =  $this->settings['urlparam'];
      if ( isset( $_GET[$urlparam] ) ) {
        // goto key exitst
        $url = str_replace ( ' ', '+', $this->xor_this ( base64_decode( urldecode( $_GET[$urlparam] ) ) ) );
        $ip = $this->get_client_ip();
        $is_bot = $this->is_bot();
        
        // redirect immediately
        ignore_user_abort( true );
        set_time_limit( 0 );
        header( 'Location: ' . $url, true );
        header( 'Connection: close', true );
        header( "Content-Encoding: none\r\n" );
        header( 'Content-Length: 0', true );
        flush();
        ob_flush();
        session_write_close();
        
        // do DB stuff after client was redirected
        $iplock = $this->settings['iplockparam'];
        $url = rtrim( esc_sql( $url ), '/' );
        $insert = true;
        global $wpdb;
        if ( $this->settings['omitbotsparam'] && $is_bot ) {
          $insert = false;
        }
        if ( $insert && $iplock != 0 && $ip != '' ) {
          $test = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'linklog WHERE linklog_url = "' . $url . '" AND linklog_ip = "' . $ip . '" AND linklog_clicked >=  DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -' . $iplock . ' SECOND)' );
          if ( ! is_null($wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'linklog WHERE linklog_url = "' . $url . '" AND linklog_ip = "' . $ip . '" AND linklog_clicked >=  DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -' . $iplock . ' SECOND)' ) ) ) {
            $insert = false;
          }
        }
        if ( $insert ) {
          $wpdb->query( 'INSERT INTO ' . $wpdb->prefix . 'linklog ( linklog_url, linklog_ip ) VALUES ( "' .  $url . '", "' . $ip . '" )' ) ;
        }
        exit;
      }
    }
  }
  
  // init backend 
  function adminmenu() {
    // settings page
    add_options_page( 'link-log', 'link-log', 'manage_options', 'link-log-settings', array( $this, 'admin_settings' ) );
    // log page
    add_menu_page( $this->settings['menutitle'], $this->settings['menutitle'], 'publish_pages', 'link-log-log', array( $this, 'admin_log' ), 'dashicons-admin-links', '25.00001' );
  }
  
  // show settings in admin / settings / link-log
  function admin_settings() {
    global $wpdb;
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
   
    if ( isset( $_POST ) && isset( $_POST['_wp_http_referer'] ) && strpos( $_POST['_wp_http_referer'], 'page=link-log-settings' ) && strpos( $_POST['_wp_http_referer'], 'tab=titles' ) ) {
      foreach ( $_POST['linkdescription'] as $key => $value ) {
        $url = base64_decode( $key );
        $desc = $value;
        if ( empty( $desc) || '' == $desc ) {
          $desc = $url;
        }
        $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'linklog_descriptions SET linklog_description = "' . $desc . '" WHERE linklog_url = "' . $url . '" AND linklog_description != "' . $desc . '";' );
      }      
      echo '<div class="updated"><p>Link Descriptions updated</p></div>';
    }
    
    $url = admin_url( 'options-general.php?page=' . $_GET['page'] . '&tab=' );
    $current_tab = 'general';
    if ( isset( $_GET['tab'] ) ) {
      $current_tab = $_GET['tab'];
    }
    if ( ! in_array( $current_tab, array( 'general', 'advanced', 'auto', 'strings', 'titles' ) ) ) {
      $current_tab = 'general';
    }
    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2 style="min-height: 32px; line-height: 32px; padding-left: 40px; background-image: url(<?php echo plugins_url( 'pluginicon.png', __FILE__ ); ?>); background-repeat: no-repeat; background-position: left center"><a href="http://smartware.cc/free-wordpress-plugins/link-log/">link-log</a> Settings</h2>
      <hr />
      <p>Plugin Version: <?php echo $this->version; ?></p>
      <h2 class="nav-tab-wrapper">
        <a href="<?php echo $url . 'general'; ?>" class="nav-tab<?php if ( 'general' == $current_tab ) { echo ' nav-tab-active'; } ?>">General</a>
        <a href="<?php echo $url . 'advanced'; ?>" class="nav-tab<?php if ( 'advanced' == $current_tab ) { echo ' nav-tab-active'; } ?>">Advanced</a>
        <a href="<?php echo $url . 'auto'; ?>" class="nav-tab<?php if ( 'auto' == $current_tab ) { echo ' nav-tab-active'; } ?>">Automation</a>
        <a href="<?php echo $url . 'strings'; ?>" class="nav-tab<?php if ( 'strings' == $current_tab ) { echo ' nav-tab-active'; } ?>">Analysis Page</a>
        <a href="<?php echo $url . 'titles'; ?>" class="nav-tab<?php if ( 'titles' == $current_tab ) { echo ' nav-tab-active'; } ?>">Link Descriptions</a>
      </h2>
      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
          <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
              <?php 
                if ( 'titles' != $current_tab ) {
                  $action = 'options.php';
                } else {
                  $action = '';
                }
              ?>
              <form method="post" action="<?php echo $action; ?>" class="linklog_settings_form">
                <div class="postbox">
                  <div class="inside">
                    <?php
                      settings_fields( 'linklog_settings_' . $current_tab );   
                      do_settings_sections( 'linklog_settings_section_' . $current_tab );
                      if ( 'titles' != $current_tab ) {
                        submit_button(); 
                      }
                      ?>
                   </div>
                </div>
              </form>
            </div>
          </div>
          <?php $this->show_meta_boxes(); ?>
        </div>
        <br class="clear">
      </div>    
    </div>  
    <?php
  }
  
  // show admin page log
  function admin_log() {
    if ( !current_user_can( 'publish_pages' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $swcc_linklog_stats_table = new Swcc_Linklog_Stats_Table();
    $swcc_linklog_stats_table->prepare_items();
    ?>
    <div class="wrap">
      <div id="icon-tools" class="icon32"></div>
      <h2><?php echo $this->settings['pagetitle']; ?></h2>
      <p class="description">Proudly powered by the free <a href="http://smartware.cc/free-wordpress-plugins/link-log">link-log</a> plugin.</p>
      <p>
      <form id="linklogstats" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $swcc_linklog_stats_table->views(); ?>
        <?php $swcc_linklog_stats_table->display() ?>
      </form>
    </div>
    <?php
  }
  
  // register settings
  function register_settings() {
    add_settings_section( 'linklog-settings-general', '', array( $this, 'admin_section_general_title' ), 'linklog_settings_section_general' );
    register_setting( 'linklog_settings_general', 'swcc_linklog_urlparam', array( $this, 'admin_urlparam_validate' ) );
    register_setting( 'linklog_settings_general', 'swcc_linklog_iplockparam' );
    register_setting( 'linklog_settings_general', 'swcc_linklog_omitbotsparam' );
    add_settings_field( 'swcc_linklog_settings_urlparam', 'Parameter Name to use in URL <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_general_parametername"></a>', array( $this, 'admin_urlparam' ), 'linklog_settings_section_general', 'linklog-settings-general', array( 'label_for' => 'swcc_linklog_urlparam' ) );
    add_settings_field( 'swcc_linklog_settings_iplockparam', 'IP Lock Setting <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_general_iplock"></a>', array( $this, 'admin_iplockparam' ), 'linklog_settings_section_general', 'linklog-settings-general', array( 'label_for' => 'swcc_linklog_iplockparam' ) );
    add_settings_field( 'swcc_linklog_settings_omitbotsparam', 'Exclude Search Engines and other Robots <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_general_bots"></a>', array( $this, 'admin_omitbotsparam' ), 'linklog_settings_section_general', 'linklog-settings-general', array( 'label_for' => 'swcc_linklog_omitbotsparam' ) );
    
    add_settings_section( 'linklog-settings-advanced', '', array( $this, 'admin_section_advanced_title' ), 'linklog_settings_section_advanced' );
    register_setting( 'linklog_settings_advanced', 'swcc_linklog_nofollowparam' );
    add_settings_field( 'swcc_linklog_settings_nofollow', 'Add <code>rel="nofollow"</code> to processed links <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_advanced_nofollow"></a>', array( $this, 'admin_nofollowparam' ), 'linklog_settings_section_advanced', 'linklog-settings-advanced', array( 'label_for' => 'swcc_linklog_nofollowparam' ) );
    
    add_settings_section( 'linklog-settings-auto', '', array( $this, 'admin_section_auto_title' ), 'linklog_settings_section_auto' );
    register_setting( 'linklog_settings_auto', 'swcc_linklog_automationparam' );
    add_settings_field( 'swcc_linklog_settings_automationparam', 'Process links <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_automation_process"></a>', array( $this, 'admin_automationparam' ), 'linklog_settings_section_auto', 'linklog-settings-auto', array( 'label_for' => 'swcc_linklog_automationparam' ) );
    
    add_settings_section( 'linklog-settings-strings', '', array( $this, 'admin_section_strings_title' ), 'linklog_settings_section_strings' );
    register_setting( 'linklog_settings_strings', 'swcc_linklog_menutitle', array( $this, 'admin_menutitle_validate' ) );
    register_setting( 'linklog_settings_strings', 'swcc_linklog_pagetitle', array( $this, 'admin_pagetitle_validate' ) );
    add_settings_field( 'swcc_linklog_settings_menutitle', 'Title for Link Click Analysis page to show in menu <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_analysis_page_menu"></a>', array( $this, 'admin_menutitle' ), 'linklog_settings_section_strings', 'linklog-settings-strings', array( 'label_for' => 'swcc_linklog_menutitle' ) );
    add_settings_field( 'swcc_linklog_settings_pagetitle', 'Page Title for Link Click Analysis page <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_analysis_page_title"></a>', array( $this, 'admin_pagetitle' ), 'linklog_settings_section_strings', 'linklog-settings-strings', array( 'label_for' => 'swcc_linklog_pagetitle' ) );
    
    add_settings_section( 'linklog-settings-titles', '', array( $this, 'admin_section_titles_fullcontent' ), 'linklog_settings_section_titles' );
  }
  
  // settings section : general
  function admin_section_general_title() {
    echo '<p><strong>General Click Tracking Settings</strong></p><hr />';
  }
  
  // handle the settings field : url
  function admin_urlparam() {
    echo '<input class="regular-text" type="text" name="swcc_linklog_urlparam" id="swcc_linklog_urlparam" value="' .  $this->settings['urlparam'] . '" /><p class="description">e.g. <code>' . home_url() . '?<strong style="text-decoration: underline;">' . $this->settings['urlparam'] . '</strong>=ENCRYPTEDURL</code></p>';
  }
  
  // check input : url
  function admin_urlparam_validate( $input ) {
    if ( empty( $input ) ) {
      $new = $this->settings['urlparam'];
    }  elseif ( ctype_alnum( $input ) ) {
      $new = $input;
    } else {
      $new =  $this->settings['urlparam'];
      add_settings_error( 'link-log-settings-url-err', 'link-log-settings-url-error', 'The parameter name must only contain letters and/or digits.', 'error' );	
    }
    return $new;
  }
  
  // handle the settings field : iplock
  function admin_iplockparam() {
    $curvalue = $this->settings['iplockparam'];
    echo '<select name="swcc_linklog_iplockparam" id="swcc_linklog_iplockparam">';
    echo '<option value="0"' . ( ( $curvalue == 0 ) ? ' selected="selected"' : '' ) . '>Count all clicks</option>';
    echo '<option value="30"' . ( ( $curvalue == 30 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 30 seconds</option>';
    echo '<option value="60"' . ( ( $curvalue == 60 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 1 minute</option>';
    echo '<option value="300"' . ( ( $curvalue == 300 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 5 minutes</option>';
    echo '<option value="900"' . ( ( $curvalue == 900 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 15 minutes</option>';
    echo '<option value="1800"' . ( ( $curvalue == 1800 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 30 minutes</option>';
    echo '<option value="3600"' . ( ( $curvalue == 3600 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 1 hour</option>';
    echo '<option value="10800"' . ( ( $curvalue == 10800 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 3 hours</option>';
    echo '<option value="21600"' . ( ( $curvalue == 21600 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 6 hours</option>';
    echo '<option value="43200"' . ( ( $curvalue == 43200 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 12 hours</option>';
    echo '<option value="86400"' . ( ( $curvalue == 86400 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 24 hours</option>';
    echo '<option value="129600"' . ( ( $curvalue == 129600 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 36 hours</option>';
    echo '<option value="172800"' . ( ( $curvalue == 172800 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 48 hours</option>';
    echo '<option value="259200"' . ( ( $curvalue == 259200 ) ? ' selected="selected"' : '' ) . '>Do not count multiple clicks from the same IP within 72 hours</option>';
    echo '</select>';
  }
    
  // handle the settings field : omitbots
  function admin_omitbotsparam() {
    echo '<input type="checkbox" name="swcc_linklog_omitbotsparam" id="swcc_linklog_omitbotsparam" value="1"' . ( ( $this->settings['omitbotsparam'] ) ? ' checked="checked"' : '' ) . ' /><label for="swcc_linklog_omitbotsparam" class="check"></label>';
  }
  
  // settings section : advanced
  function admin_section_advanced_title() {
    echo '<p><strong>Do not follow external links</strong></p><hr />';
  }
  
  // handle the settings field : nofollow
  function admin_nofollowparam() {
    echo '<input type="checkbox" name="swcc_linklog_nofollowparam" id="swcc_linklog_nofollowparam" value="1"' . ( ( $this->settings['nofollowparam'] ) ? ' checked="checked"' : '' ) . ' /><label for="swcc_linklog_nofollowparam" class="check"></label>';
  }
  
  // settings section : automation
  function admin_section_auto_title() {
    echo '<p><strong>Customize link process automation</strong></p><hr />';
  }
  
  // handle the settings field : automation
  function admin_automationparam() {
    $curvalue = $this->settings['automationparam'];
    echo '<select name="swcc_linklog_automationparam" id="swcc_linklog_automationparam">';
    echo '<option value="AUTO"' . ( ( $curvalue == 'AUTO' ) ? ' selected="selected"' : '' ) . '>Process all links fully automated (recommended)</option>';
    echo '<option value="CUSTOM"' . ( ( $curvalue == 'CUSTOM' ) ? ' selected="selected"' : '' ) . '>Only process links on specified posts or pages (shows option)</option>';
    echo '<option value="NEVER"' . ( ( $curvalue == 'NEVER' ) ? ' selected="selected"' : '' ) . '>Never process links automatically (template functions are used)</option>';
    echo '</select>';
  }
  
  // settings section : analysis page
  function admin_section_strings_title() {
    echo '<p><strong>Customize Analysis Page</strong></p><hr />';
  }
  
  // handle the settings field : menutitle
  function admin_menutitle() {
    echo '<input class="regular-text" type="text" name="swcc_linklog_menutitle" id="swcc_linklog_menutitle" value="' .  $this->settings['menutitle'] . '" />';
  }
  
  // check input : menutitle
  function admin_menutitle_validate( $input ) {
    if ( empty( $input ) ) {
      $input = $this->plugin_name;
    }
    return $input;
  }
  
  // handle the settings field : pagetitle
  function admin_pagetitle() {
    echo '<input class="regular-text" type="text" name="swcc_linklog_pagetitle" id="swcc_linklog_pagetitle" value="' .  $this->settings['pagetitle'] . '" />';
  }
  
  // check input : pagetitle
  function admin_pagetitle_validate( $input ) {
    if ( empty( $input ) ) {
      $input = 'link-log Link Click Analysis';
    }
    return $input;
  }
  
 
  // settings section : link descriptions
  function admin_section_titles_fullcontent() {
    // this is different from all other sections because it does not use setting fields but creates the content itself
    echo '<p><strong>Edit Link Descriptions</strong> <a class="dashicons dashicons-editor-help" href="http://smartware.cc/docs/link-log/#settings_link_descriptions"></a></p><hr /><div class="form-table" id="linklog-custom-link-descriptions-table"><img src="'. plugins_url( 'loader.gif', __FILE__ ) . '" style="width: 54px; height: 55px; margin: 0 25px 25px 0; float: left" /><p style="height: 55px; line-height: 55px; white-space: nowrap">Please wait while all links are collected...</div>';
    // print out JS function in footer to generate list via AJAX
    add_action( 'admin_print_footer_scripts', array( $this, 'add_list_descriptions_js' ) );
  }
  
  // add css
  function admin_style() {
    if ( get_current_screen()->id == 'settings_page_link-log-settings' ) { 
      ?>
      <style type="text/css">
        .linklog_settings_form input[type="text"], .linklog_settings_form select { 
          border-width:2px; 
          padding:10px; 
          border-style:solid; 
          border-radius:5px; 
          height: auto !important;
        }
        .linklog_settings_form input[type="text"]:not(:focus), .linklog_settings_form select:not(:focus) { 
          box-shadow: 0px 0px 5px 0px rgba(42,42,42,.75); 
        }
        .linklog_settings_form input[type="checkbox"] {
            display: none;
        }
        .linklog_settings_form input[type="checkbox"] + label.check {
          display: inline-block;  
          border: 2px solid #DDD;
          box-shadow: 0px 0px 5px 0px rgba(42,42,42,.75); 
          border-style:solid; 
          border-radius:5px; 
          width: 30px;
          height: 30px;
          line-height: 30px;
          text-align: center;
          font-family: dashicons;
          font-size: 2em;
          margin-right: 10px;
        }
        .linklog_settings_form input[type="checkbox"]:disabled + label.check {
          background-color: #DDD;
        }
        .linklog_settings_form input[type="checkbox"] + label.check:before {
          content: "";  
        }
        .linklog_settings_form input[type="checkbox"]:checked + label.check:before {
          content: "\f147";
        }
      </style>
      <?php
    } elseif ( get_current_screen()->id == 'toplevel_page_link-log-log' ) { 
      ?>
      <style type="text/css">
        th:not(.column-primary) a span {
          float: right;
        }
      </style>
      <?php
    }
  }
  
  // show meta boxes
  function show_meta_boxes() {
    ?>
    <div id="postbox-container-1" class="postbox-container">
      <div class="meta-box-sortables">
        <div class="postbox">
          <h3><span>Like this Plugin?</span></h3>
          <div class="inside">
            <ul>
              <li><div class="dashicons dashicons-wordpress"></div>&nbsp;&nbsp;<a href="https://wordpress.org/plugins/<?php echo $this->plugin_slug; ?>/">Please rate the plugin</a></li>
              <li><div class="dashicons dashicons-admin-home"></div>&nbsp;&nbsp;<a href="http://smartware.cc/free-wordpress-plugins/<?php echo $this->plugin_slug; ?>/">Plugin homepage</a></li>
              <li><div class="dashicons dashicons-admin-home"></div>&nbsp;&nbsp;<a href="http://smartware.cc/">Author homepage</a></li>
              <li><div class="dashicons dashicons-googleplus"></div>&nbsp;&nbsp;<a href="http://g.smartware.cc/">Authors Google+ Page</a></li>
              <li><div class="dashicons dashicons-facebook-alt"></div>&nbsp;&nbsp;<a href="http://f.smartware.cc/">Authors facebook Page</a></li>
            </ul>
          </div>
        </div>
        <div class="postbox">
          <h3><span>Need help?</span></h3>
          <div class="inside">
            <ul>
              <li><div class="dashicons dashicons-book-alt"></div>&nbsp;&nbsp;<a href="http://smartware.cc/docs/<?php echo $this->plugin_slug; ?>/">Take a look at the Plugin Doc</a></li>
              <li><div class="dashicons dashicons-wordpress"></div>&nbsp;&nbsp;<a href="https://wordpress.org/plugins/<?php echo $this->plugin_slug; ?>/faq/">Take a look at the FAQ section</a></li>
              <li><div class="dashicons dashicons-wordpress"></div>&nbsp;&nbsp;<a href="https://wordpress.org/support/plugin/<?php echo $this->plugin_slug; ?>">Take a look at the Support section</a></li>
              <li><div class="dashicons dashicons-admin-comments"></div>&nbsp;&nbsp;<a href="http://smartware.cc/contact/">Feel free to contact the Author</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
  
  // This function adds the meta box to specify if external links should be processed or not
  // This only runs when Automation is set to "CUSTOM"
  function add_customization_meta_box() {
    $screens = array( 'post', 'page' );
    foreach ( $screens as $screen ) {
      add_meta_box( 'linklog_automation_process', 'Track external links', array( $this, 'show_customization_meta_box' ), $screen, 'side' );
    }
  }
  
  // shows the content of the customization meta box
  function show_customization_meta_box( $post ) {
    wp_nonce_field( 'linklog_customization', 'linklog_customization_nonce' );
    $value = ( get_post_meta( $post->ID, '_linklog_custom_process_this', true ) == '1' );
    echo '<input type="checkbox" name="linklog_custom_process_this" id="linklog_custom_process_this" value="1"' . ( ( $value ) ? ' checked="checked"' : '' ) . ' /><label for="linklog_custom_process_this">Count clicks on external links</label>';
  }

  // saves the meta data of the customization meta box
  function save_customization_meta_data( $post_id ) {
    if ( ! isset( $_POST['linklog_customization_nonce'] ) ) {
      return;
    }
    if ( ! wp_verify_nonce( $_POST['linklog_customization_nonce'], 'linklog_customization' ) ) {
      return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }
    $value = '0';
    if ( isset( $_POST['linklog_custom_process_this'] ) ) {
      $value = '1';
    }
    update_post_meta( $post_id, '_linklog_custom_process_this', $value );
  }
  
  // ***
  // *** AJAX
  // ***
  
  // print out the script for list generation
  function add_list_descriptions_js() {
    $ajax_nonce = wp_create_nonce( "linklog_ajax_list_descriptions" );
    ?>
    <script type='text/javascript'>
      jQuery( document ).ready(function() {
        jQuery.ajax( { 
          type: 'POST', 
          url: ajaxurl, 
          data: { 'action': 'linklog_list_descriptions', 'security' : '<?php echo $ajax_nonce; ?>' }, 
          success: function( response ) {  
            jQuery( '#linklog-custom-link-descriptions-table' ).html( response );
          }
        });
      });
    </script>
    <?php  
  }
  
  // create the link description list for customizing
  function ajax_list_descriptions() {
    global $wpdb;
    if ( ! check_ajax_referer( 'linklog_ajax_list_descriptions', 'security', false ) ) {
      echo '<p>Security Check failed.</p>';
      echo '<p>Maybe a timeout?</p>';
      echo '<p>Please reload page or logoff/logon if message repeats.</p>';
    } else {
      $wpdb->query( 'INSERT INTO ' . $wpdb->prefix . 'linklog_descriptions ( linklog_url, linklog_description ) SELECT DISTINCT linklog_url, linklog_url FROM ' . $wpdb->prefix . 'linklog WHERE linklog_url NOT IN ( SELECT linklog_url FROM ' . $wpdb->prefix . 'linklog_descriptions);' );
      $data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'linklog_descriptions ORDER BY linklog_description', ARRAY_A );
      foreach ( $data as $ll ) {
        echo '<p><input type="text" style="width: 100%" name="linkdescription[' . base64_encode( $ll['linklog_url'] ) . ']" value="' . $ll['linklog_description'] . '" /><br />URL: <code>' . $ll['linklog_url'] . '</code></p>';
      }
      submit_button(); 
    }
    die();
  }
  
  // ***
  // *** install / activate / new multisite blog
  // ***

  // on plugin installation
  function install( $network_wide ) {
    if( $network_wide ) {
      $this->install_network();
    } else {
      $this->install_single();
    }
  }
  
  // for installation on wp single site or for a single blog within a multi site installation
  function install_single() {
    $this->create_table();
  }

  // for network wide installation on wp multi site
  function install_network() {
    global $wpdb;
    $activeblog = $wpdb->blogid;
    $blogids = $wpdb->get_col( esc_sql( 'SELECT blog_id FROM ' . $wpdb->blogs ) );
    foreach ($blogids as $blogid) {
      switch_to_blog($blogid);
      $this->create_table();
    }
    switch_to_blog( $activeblog );
  }

  // when a new blog is added on wp multi site 
  function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
    if ( is_plugin_active_for_network( 'link-log/link-log.php' ) ) {
      $current = $wpdb->blogid;
      switch_to_blog( $blog_id );
      $this->create_table();
      switch_to_blog( $current );
    }
  }

  // create tables for single blog
  function create_table() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( 'CREATE TABLE ' . $wpdb->prefix . 'linklog (
      linklog_url VARCHAR(500) NOT NULL, 
      linklog_clicked TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
      linklog_ip VARCHAR(50) 
      );'
    );
    dbDelta( 'CREATE TABLE ' . $wpdb->prefix . 'linklog_descriptions (
      linklog_url VARCHAR(500) NOT NULL, 
      linklog_description VARCHAR(100) NOT NULL
      );'
    );
    update_option( "swcc_linklog_version", $this->version );
  }

  // update
  function update() {
    if ( $this->settings['installed_version'] != $this->version )  {
      $this->create_table();
    }
    if ( $this->settings['installed_version'] < '1.3' )  {
      global $wpdb;
      $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'linklog SET linklog_url = TRIM(TRAILING "/" FROM linklog_url)' ) ;
    }
  }
  
  // ***
  // *** uninstall
  // ***

  // uninstall main function
  function uninstall( ) {
    if( is_multisite() ) {
      $this->uninstall_network();
    } else {
      $this->uninstall_single();
    }
  }

  // for uninstall on wp single site
  function uninstall_single() {
    $this->delete_table();
    $this->delete_settings();
  }

  function uninstall_network() {
    // for network wide uninstall on wp multi site
    global $wpdb;
    $activeblog = $wpdb->blogid;
    $blogids = $wpdb->get_col( esc_sql( 'SELECT blog_id FROM ' . $wpdb->blogs ) );
    foreach ($blogids as $blogid) {
      switch_to_blog($blogid);
      $this->delete_table();
      $this->delete_settings();
    }
    switch_to_blog( $activeblog );
  }

  function delete_table() {
    // delete tables
    global $wpdb;
    $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'linklog' );
    $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'linklog_descriptions' );
  }
  
  // delete settings from database
  function delete_settings() {
    foreach ( $this->settings as $key => $value) {
      delete_option( 'swcc_linklog_' . $key );
    }
  }
 
}

// Here the magic happens...
$linklog = new LinkLog();

// this function can be used in theme
// returns the new url
function get_linklog_url( $url ) {
  return $linklog->make_url( $url );
}

// this function can be used in theme
// prints the new url
function the_linklog_url( $url ) {
  echo $linklog->make_url( $url );
}

if ( is_admin() ) {
   
  // use WP_List_Table to display stats
  if( ! class_exists( 'WP_List_Table' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }
      
  class Swcc_Linklog_Stats_Table extends WP_List_Table {
    
    function __construct(){
      global $status, $page;
      
      parent::__construct( array(
        'singular'  => 'click',
        'plural'    => 'clicks',
        'ajax'      => false
      ) );
        
    }
    
    function column_default( $item, $column_name ){
      switch ( $column_name ) {
        case 'current':
        case 'previous':
          return '<div class="textright">' . number_format( $item[$column_name] ) . '</div>';
        default:
          return print_r( $item, true );
      }
    }
    
    function column_title( $item ) {
      return '<strong><span class="row-title">' . $item['title'] . '</span></strong>';
    }
    
    function get_columns() {
      $current_view = $this->get_current_view_array();
      
      return array(
        'title' => 'Link',
        'current' => '<div class="textright">Clicks<br /> ' . date( 'Y-m-d', $current_view['current_first'] ) . '<br /> -&nbsp;' . date( 'Y-m-d', $current_view['current_last'] ) . '</div>',
        'previous' => '<div class="textright">Clicks<br /> ' . date( 'Y-m-d', $current_view['previous_first'] ) . '<br /> -&nbsp;' . date( 'Y-m-d', $current_view['previous_last'] ) . '</div>'
      );
    }

    function get_sortable_columns() {
      return array(
        'title' => array( 'title', false ),
        'current' => array( 'current', false ),
        'previous' => array('previous', false )
      );
    } 

    function prepare_items() {
      global $wpdb;
      $per_page = 50;
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array( $columns, $hidden, $sortable );
      $current_view = $this->get_current_view_array();
     
      $current_first = date( 'Y-m-d', $current_view['current_first'] ) . ' 00:00:00';
      $current_last = date( 'Y-m-d', $current_view['current_last'] )  . ' 23:59:59';
      $previous_first = date( 'Y-m-d', $current_view['previous_first'] )  . ' 00:00:00';
      $previous_last = date( 'Y-m-d', $current_view['previous_last'] ) . ' 23:59:59';
      
      //$data = $wpdb->get_results( "SELECT linklog_url AS title, SUM( IF( linklog_clicked BETWEEN '" . $current_first . "' AND '" . $current_last . "', 1, 0 ) ) AS current, SUM( IF( linklog_clicked BETWEEN '" . $previous_first . "' AND '" . $previous_last . "', 1, 0 ) ) AS previous FROM " . $wpdb->prefix . "linklog WHERE ( linklog_clicked BETWEEN '" . $current_first . "' AND '" . $current_last . "' ) OR ( linklog_clicked BETWEEN '" . $previous_first . "' AND '" . $previous_last . "' ) GROUP BY linklog_url", ARRAY_A );
      $data = $wpdb->get_results( "SELECT COALESCE( description.linklog_description, log.title ) AS title, log.current, log.previous FROM ( SELECT linklog_url AS title, SUM( IF( linklog_clicked BETWEEN '" . $current_first . "' AND '" . $current_last . "', 1, 0 ) ) AS current, SUM( IF( linklog_clicked BETWEEN '" . $previous_first . "' AND '" . $previous_last . "', 1, 0 ) ) AS previous FROM " . $wpdb->prefix . "linklog WHERE ( linklog_clicked BETWEEN '" . $current_first . "' AND '" . $current_last . "' ) OR ( linklog_clicked BETWEEN '" . $previous_first . "' AND '" . $previous_last . "' ) GROUP BY linklog_url ) AS log LEFT OUTER JOIN " . $wpdb->prefix . "linklog_descriptions as description ON log.title = description.linklog_url ORDER BY title" , ARRAY_A );
      
      function usort_reorder( $a, $b ){
        $orderby = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title';
        $order = ( !empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
        if ( $orderby == 'title' ) {
          $result = strcasecmp( $a[$orderby], $b[$orderby] );
        } else {
          $result = $a[$orderby] - $b[$orderby];
        }
        return ( $order === 'asc' ) ? $result : -$result;
      }
      usort( $data, 'usort_reorder' );
      
      $current_page = $this->get_pagenum();
      $total_items = count($data);
      $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
      
      $this->items = $data;
      
      $this->set_pagination_args( array(
        'total_items' => $total_items,
        'per_page'    => $per_page,
        'total_pages' => ceil($total_items/$per_page)
      ) );
      
    }    
    
    function get_views() {
      global $linklog;
      $views = array();
      $current = $this->get_current_view();

      foreach ( $linklog->admin_available_views as $key => $value) {
        if ( $key == $current ) {
          $class =' class="current"';
        } else {
          $class = '';
        }
        if ( $key == '7d' ) {
          $url = remove_query_arg( 'view' );
        } else {
          $url = add_query_arg( 'view' ,$key );
        }
        $views[$key] = '<a href="' . $url .'"' . $class . '>' . $value['title'] . '</a>';
      }
      return $views;
    }
    
    function get_current_view() {
      global $linklog;
      $current_view = ( ! empty($_REQUEST['view']) ? $_REQUEST['view'] : '7d');
      if ( ! array_key_exists( $current_view, $linklog->admin_available_views ) ) {
        $current_view = '7d';
      }
      return $current_view;
    }
    
    function get_current_view_array() {
      global $linklog;
      return $linklog->admin_available_views[$this->get_current_view()];
    }
    
    function no_items() {
      echo 'No clicks during selected period';
    }
  }
}

?>