<?php
/**
 * The main file of the Advanced JS Defer
 *
 * @package advanced-js-defer
 * @version 1.0.0
 *
 * Plugin Name: Advanced JS Defer
 * Plugin URI: https://wordpress.org/plugins/advanced-js-defer/
 * Description: Prioritize CSS, Images, Fonts over JavaScript by delaying JS execution 
 * Author: Gijo Varghese
 * Author URI: https://wpspeedmatters.com/
 * Version: 1.0.0
 * Text Domain: advanced-js-defer
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

// Define constant with current version
if (!defined('ADVANCED_JS_DEFER_VERSION'))
    define('ADVANCED_JS_DEFER_VERSION', '1.0.0');

// Set default config on plugin load if not set
function advanced_js_defer_set_default_config() {
    if (ADVANCED_JS_DEFER_VERSION !== get_option('ADVANCED_JS_DEFER_VERSION')) {
        if (get_option('advanced_js_defer_config_delay') === false)
            update_option('advanced_js_defer_config_delay', 0);
        update_option('ADVANCED_JS_DEFER_VERSION', ADVANCED_JS_DEFER_VERSION);
    }
}
add_action('plugins_loaded', 'advanced_js_defer_set_default_config');

// Register settings menu
function advanced_js_defer_register_settings_menu()
{
    add_options_page('Advanced JS Defer', 'Advanced JS Defer', 'manage_options', 'advanced-js-defer', 'advanced_js_defer_settings_view');
}
add_action('admin_menu', 'advanced_js_defer_register_settings_menu');

// Settings page
function advanced_js_defer_settings_view()
{
    // Validate nonce
    if(isset($_POST['submit']) && !wp_verify_nonce($_POST['advanced-js-defer-settings-form'], 'advanced-js-defer')) {
        echo '<div class="notice notice-error"><p>Nonce verification failed</p></div>';
        exit;
    }

    // Update config in database after form submission
    if (isset($_POST['delay'])) {
        $delay = sanitize_text_field($_POST['delay']);
        $delay = is_numeric($delay) ? $delay : 0;
        update_option('advanced_js_defer_config_delay', $delay);
    }

    // Get config from db for displaying in the form
    $delay = get_option('advanced_js_defer_config_delay');
    
    // Settings form
    include 'settings-form.php';
}

// Add links in plugins list
function advanced_js_defer_add_action_links($links)
{
    $plugin_shortcuts = array(
        '<a href="'.admin_url('options-general.php?page=advanced-js-defer').'">Settings</a>',
        '<a href="https://www.buymeacoffee.com/gijovarghese" target="_blank" style="color:#3db634;">Buy developer a coffee</a>'
    );
    return array_merge($links, $plugin_shortcuts);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'advanced_js_defer_add_action_links');

// Lazy load scripts
function advanced_js_defer_callback($buffer) {
  $buffer = str_replace("<script>", '<script type="text/javascript">', $buffer);
  $buffer = str_replace('type="text/javascript"', 'type="lazy"', $buffer);
  $buffer = str_replace("type='text/javascript'", "type='lazy'", $buffer);
  $lazyloader = '
  <script type="text/javascript">
  const scripts = document.querySelectorAll(\'script[type="lazy"]\');
      window.requestIdleCallback(() => {
        setTimeout(()=>{
          scripts.forEach(s => {
            if (s.src) {
              (function(d, script) {
                script = d.createElement("script");
                script.type = "text/javascript";
                script.defer = true;
                script.src = s.src;
                d.getElementsByTagName("head")[0].appendChild(script);
              })(document);
            } else {
              console.log(s.innerHTML);
              var newScript = document.createElement("script");
              var inlineScript = document.createTextNode(s.innerHTML);
              newScript.appendChild(inlineScript);
              document.head.appendChild(newScript);
            }
          });
        },'.get_option('advanced_js_defer_config_delay').'*1000)
      });
	  </script>
	  </body>
	  ';
  $buffer = str_replace("</body>", $lazyloader, $buffer);
  return $buffer;
}

if(!is_admin()) ob_start("advanced_js_defer_callback");