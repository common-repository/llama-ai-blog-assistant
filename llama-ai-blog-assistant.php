<?php
/**
 * Plugin Name:       Llama: AI Blog Assistant
 * Description:       Boost your blog's revenue potential with Llama: AI Blog Assistant. Our ChatGPT-powered WordPress plugin optimizes content, increases engagement, and enhances your blog's SEO. Experience profitable blogging with Llama!
 * Requires at least: 6
 * Requires PHP:      7.0
 * php version        7.0
 * Version:           1.1.0
 * Author:            MarketingLlama.ai
 * Author URI:        https://marketingllama.ai/
 * Banner:            banner/banner.jpg
 * Banner             Width: 772
 * Banner Height:     250
 * License:           GPLv3-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       llama-ai-blog-assistant
 * Domain Path:       /languages
 *
 * @category Plugin
 *
 * @package LlamaAiBlogAssistant
 *
 * @author MarketingLlama.ai <support@higheredlab.com>
 *
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @link https://marketingllama.ai/
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'LLAMA_UPGRADE_URL', 'https://marketingllama.ai/pricing/' );
define( 'LLAMA_HOME_PAGE', 'https://marketingllama.ai/' );
define( 'LLAMA_HELP_PAGE', 'https://marketingllama.ai/guide' );
define( 'LLAMA_SUPPORT_PAGE', 'https://marketingllama.ai/contact/' );

/**
 * Creates a new block
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_init() {
	register_block_type( __DIR__ . '/build' );
}

/**
 * Add custom actions to the plugin row actions
 *
 * @param array  $actions     Actions array.
 * @param string $plugin_file Plugin file.
 *
 * @return array
 */
function Llama_AI_Blog_Assistant_upgrade( $actions, $plugin_file ) {
	// phpcs:disable
	$plugin_base = plugin_basename( __FILE__ );

	// Only modify the specific plugin's actions
	if ( $plugin_file === $plugin_base ) {

        $upgrade_link = '<a style="color: #1da867;" href="' . LLAMA_UPGRADE_URL . '" target="_blank">Upgrade to Pro</a>';

        $settings_link = '<a style="" href="/wp-admin/admin.php?page=llama-ai-blog-assistant&tab=settings">Settings</a>';

        // Insert the upgrade link as the first action
        $actions = array( 'upgrade' => $upgrade_link, 'settings' => $settings_link ) + $actions;
	}

	return $actions;
}
add_filter( 'plugin_action_links', 'Llama_AI_Blog_Assistant_upgrade', 10, 2 );

add_action( 'init', 'Llama_AI_Blog_Assistant_init' );

/**
 * Register rest api endpoint
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_create_Api_endpoint() {
	// route for getting settings
	register_rest_route(
		'llama-ai-blog-assistant/v1',
		'/get-settings/',
		array(
			'methods' => 'GET',
			'callback' => 'Llama_AI_Blog_Assistant_handle_Get_Settings_request',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'Llama_AI_Blog_Assistant_create_Api_endpoint' );

/**
 * Handle get settings request
 *
 * @param WP_REST_Request $request Request object
 *
 * @return WP_REST_Response $payload Response object
 */
function Llama_AI_Blog_Assistant_handle_Get_Settings_request( WP_REST_Request $request ) {
	$model = sanitize_text_field( get_option( 'llama_model_name' ) );
	$apiKey = sanitize_text_field( get_option( 'llama_api_secret' ) );
	$brandVoice = sanitize_text_field( get_option( 'llama_brand_voice' ) );
	// payload object
	$payload = array(
		"model" => $model,
		"apiKey" => $apiKey,
		"brandVoice" => $brandVoice
	);
	return rest_ensure_response( $payload );
}

/**
 * Register post meta
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_Register_Post_meta() {
	register_post_meta(
		'',
		'llama_token_used',
		array(
			'show_in_rest' => true,
			'single' => true,
			'type' => 'integer',
		)
	);

	register_post_meta(
		'',
		'llama_time_taken',
		array(
			'show_in_rest' => true,
			'single' => true,
			'type' => 'integer',
		)
	);
}
add_action( 'init', 'Llama_AI_Blog_Assistant_Register_Post_meta' );

/**
 * Enqueue block editor only JavaScript and CSS
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_Admin_styles() {
	echo '<style>
        #adminmenu .toplevel_page_llama-ai-blog-assistant div.wp-menu-image {
            background-size: 40% !important; /* adjust this value as needed */
            background-position: center !important; /* centers the icon */
        }
    </style>';
}
add_action( 'admin_head', 'Llama_AI_Blog_Assistant_Admin_styles' );

// Add the hook for the admin menu
add_action( 'admin_menu', 'Llama_AI_Blog_Assistant_Admin_menu' );

/**
 * Add the admin menu
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_Admin_menu()
// phpcs:disable
// ignoring this because it is impossible to make a single line with only 80 characters
{

	$icon_svg = 'data:image/svg+xml;base64,' . base64_encode( '
    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="40%" height="40%" viewBox="10.021989822387695 2.9099979400634766 43.958038330078125 58.1820068359375"><g stroke="#a7aaad" stroke-width="4.395803833007812px"><g fill="#a7aaad"><path d="M52.38 28.858a8.098 8.098 0 0 0-3.358-6.596 8.133 8.133 0 0 0-1.236-4.225c.018-.074.045-.145.045-.225v-9.55c0-2.951-2.291-5.352-5.106-5.352s-5.105 2.4-5.105 5.352v6.23a8.161 8.161 0 0 0-11.238 0v-6.23c0-2.951-2.291-5.352-5.106-5.352s-5.106 2.4-5.106 5.352v9.552c0 .08.028.15.045.226a8.111 8.111 0 0 0-1.236 4.223 8.093 8.093 0 0 0-3.36 6.597c0 .911.155 1.795.445 2.638a19.74 19.74 0 0 0-2.042 8.764c0 11.485 9.859 20.83 21.979 20.83s21.979-9.345 21.979-20.83a19.69 19.69 0 0 0-2.042-8.766 8.123 8.123 0 0 0 .442-2.638zM42.724 4.91c1.713 0 3.106 1.504 3.106 3.352v7.611a8.134 8.134 0 0 0-4.988-1.711 8.26 8.26 0 0 0-1.224.098V8.262c0-1.848 1.394-3.352 3.106-3.352zm-21.448 0c1.713 0 3.106 1.504 3.106 3.352v5.998a8.247 8.247 0 0 0-1.223-.098 8.135 8.135 0 0 0-4.989 1.711V8.262c0-1.848 1.393-3.352 3.106-3.352zM16.53 23.629a1 1 0 0 0 .46-1.014c-.015-.095-.011-.181-.011-.273a6.187 6.187 0 0 1 6.181-6.18c.987 0 1.921.223 2.777.662a1 1 0 0 0 1.234-.261 6.192 6.192 0 0 1 4.83-2.313c1.88 0 3.64.843 4.829 2.313a1 1 0 0 0 1.234.261 6.023 6.023 0 0 1 2.778-.662 6.187 6.187 0 0 1 6.18 6.18c0 .092.004.177-.012.271-.067.4.114.802.46 1.017a6.113 6.113 0 0 1 2.91 5.229c0 .811-.151 1.59-.449 2.318l-.001.002c-.959 2.353-3.208 3.873-5.729 3.873a6.087 6.087 0 0 1-5.24-2.929.997.997 0 0 0-.849-.479c-.354-.031-.671.177-.854.472-1.145 1.839-3.109 2.937-5.258 2.937a6.119 6.119 0 0 1-5.242-2.933.999.999 0 0 0-1.702 0 6.137 6.137 0 0 1-5.256 2.933 6.173 6.173 0 0 1-5.729-3.873l-.001-.002a6.08 6.08 0 0 1-.45-2.318 6.11 6.11 0 0 1 2.91-5.231zm24.238 12.957a12.73 12.73 0 0 1 .956 4.893c0 6.235-4.362 11.309-9.724 11.309s-9.724-5.073-9.724-11.309c0-1.811.358-3.542 1.064-5.147.014-.031.011-.064.021-.097a8.08 8.08 0 0 0 2.545-1.918A8.093 8.093 0 0 0 32 37.052a8.131 8.131 0 0 0 6.101-2.735 8.027 8.027 0 0 0 2.605 1.954.974.974 0 0 0 .062.315zM32 59.09c-11.016 0-19.979-8.447-19.979-18.83 0-2.249.41-4.429 1.222-6.503a8.144 8.144 0 0 0 6.556 3.295c.396 0 .784-.038 1.168-.093a14.848 14.848 0 0 0-.692 4.52c0 7.339 5.259 13.309 11.724 13.309s11.724-5.97 11.724-13.309a14.83 14.83 0 0 0-.703-4.521c.388.056.779.094 1.18.094a8.143 8.143 0 0 0 6.558-3.298 17.705 17.705 0 0 1 1.221 6.506c0 10.383-8.962 18.83-19.979 18.83zm2.63-17.394c.187.691.03 2.106-1.619 3.009v3.475a1 1 0 1 1-2 0v-3.465c-1.665-.901-1.828-2.322-1.643-3.016a1 1 0 0 1 .966-.743h3.33c.452 0 .848.304.966.74zm13.847-2.413c0 1.038-.709 1.877-1.582 1.877-.874 0-1.582-.839-1.582-1.877 0-1.04.708-1.878 1.582-1.878.873 0 1.582.838 1.582 1.878zm-29.789 0c0 1.038-.708 1.877-1.583 1.877-.873 0-1.581-.839-1.581-1.877 0-1.04.708-1.878 1.581-1.878.874 0 1.583.838 1.583 1.878z"></path></g></g></svg>
    ' );

	// Add the menu page for the plugin
	//add_menu_page('Llama: AI Blog Assistant', 'Llama', 'manage_options', 'llama-ai-blog-assistant', 'Llama_AI_Blog_Assistant_admin_page', 'dashicons-superhero', 6);
	add_menu_page( 'Llama: AI Blog Assistant', 'Llama', 'manage_options', 'llama-ai-blog-assistant', 'Llama_AI_Blog_Assistant_admin_page', $icon_svg, 6 );
}

add_action( 'admin_init', 'Llama_AI_Blog_Assistant_Register_Plugin_settings' );

/**
 * Register the plugin settings
 *
 * @return void
 */
function Llama_AI_Blog_Assistant_Register_Plugin_settings() {
	// Register the settings
	register_setting( 'llama-plugin-settings', 'llama_model_name' );
	register_setting( 'llama-plugin-settings', 'llama_api_secret' );
}

function Llama_AI_Blog_Assistant_admin_page() {
	$allowed_tabs = array('usage', 'settings', 'helpAndSupport');
	// Get the active tab
	$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], $allowed_tabs) ? sanitize_text_field($_GET['tab']) : 'usage';

	// Start the buffer
	ob_start();
	?>
	<style>
		#llama-header {
			padding: 10px 20px;
			margin-left: -20px;
			text-align: right;
			background: #fff;
			/* height: 36px; */
			margin-bottom: 40px;
			box-shadow: 0 1px 1px #c8d7e1;
			box-sizing: content-box;
			display: flex;
			justify-content: space-between;
		}

		.llama-button-secondary {
			display: inline-block;
			background: #fff;
			border-color: #c8d7e1;
			border-style: solid;
			border-width: 1px 1px 2px;
			color: #2e4453;
			cursor: pointer;
			margin: 0;
			outline: 0;
			overflow: hidden;
			font-size: 13px;
			font-weight: 500;
			text-overflow: ellipsis;
			text-decoration: none;
			vertical-align: middle;
			box-sizing: border-box;
			line-height: 21px;
			border-radius: 4px;
			padding: 5px 11px;
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			box-shadow: none !important;
		}

		#llama-header .llama-button-secondary,
		#llama-header .llama-button-upgrade {
			padding: 6px 12px;
			margin-left: 5px;
		}

		.llama-button-primary .dashicons,
		.llama-button-secondary .dashicons,
		.llama-button-upgrade .dashicons {
			font-size: 16px;
			height: 16px;
			width: 16px;
			margin: 2px 5px 0 -5px;
		}

		.llama-button-upgrade {
			display: inline-block;
			background: #f16232;
			border-color: #c0392b;
			border-style: solid;
			border-width: 1px 1px 2px;
			color: #fff;
			cursor: pointer;
			margin: 0;
			outline: 0;
			overflow: hidden;
			font-size: 13px;
			font-weight: 500;
			text-overflow: ellipsis;
			text-decoration: none;
			vertical-align: middle;
			box-sizing: border-box;
			line-height: 21px;
			border-radius: 4px;
			padding: 5px 11px;
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			box-shadow: none !important;
		}

		.llama-menu-btn-wrapper {
			display: flex;
			flex-direction: column;
			justify-content: center;
		}
	</style>
	<div id="llama-header">
		<a href="https://marketingllama.ai/" target="_blank">
		<img src="<?php echo esc_url( plugins_url( 'public/images/llama-5.png', __FILE__ ) ); ?>" width="140">
		</a>
		<div class='llama-menu-btn-wrapper'>
			<div>

				<a href="<?php echo esc_url(LLAMA_SUPPORT_PAGE); ?>" target="_blank" rel="noopener noreferrer" class="llama-button-secondary"><span
						class="dashicons dashicons-email-alt"></span>Support</a>

				<a href="<?php echo esc_url(LLAMA_HELP_PAGE); ?>" target="_blank" rel="noopener noreferrer" class="llama-button-secondary"><span
						class="dashicons dashicons-book"></span>Documentation</a>

				<a href="<?php echo esc_url(LLAMA_HOME_PAGE); ?>" target="_blank" rel="noopener noreferrer" class="llama-button-upgrade"><span
						class="dashicons dashicons-upload"></span>Upgrade to
					PRO</a>
			</div>
		</div>
	</div>
	<h2 class="nav-tab-wrapper">
		<a href="?page=llama-ai-blog-assistant&tab=usage"
			class="nav-tab <?php echo esc_attr($active_tab == 'usage' ? 'nav-tab-active' : ''); ?>">Usage</a>
		<a href="?page=llama-ai-blog-assistant&tab=settings"
			class="nav-tab <?php echo esc_attr($active_tab == 'settings' ? 'nav-tab-active' : ''); ?>">Settings</a>
		<a href="?page=llama-ai-blog-assistant&tab=helpAndSupport"
			class="nav-tab <?php echo esc_attr($active_tab == 'helpAndSupport' ? 'nav-tab-active' : ''); ?>">FAQs</a>
	</h2>

	<?php
	// Check if the settings updated GET parameter is set
	if ( isset( $_GET['settings-updated'] ) ) {
        // Display the message
        ?>
		<div id="message" class="updated notice is-dismissible">
			<p><strong>
        <?php  esc_html_e( 'Settings saved.' ) ?>
				</strong></p>
		</div>
        <?php
	}

	if ( $active_tab == 'usage' ) {
        ?>
		<h3>Usage</h3>
		<p>Your usage of Llama, ChatGPT-powered Blog Assistant, is as shown below.</p>

        <?php
        // Get the current page
        $paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;

        // Fetch the posts
        $query = new WP_Query(
         array(
          'post_type' => 'post',
          'posts_per_page' => 20,
          'paged' => $paged,
         )
           );

           if ( $query->have_posts() ) {
            // Start the table
            ?>
			<div class="wrap">
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<th width="5%" scope="col" class="manage-column">#</th>
							<th scope="col" class="manage-column">Post Title</th>
							<th width="15%" scope="col" class="manage-column">Tokens Used</th>
							<th width="15%" scope="col" class="manage-column">Time Taken</th>
						</tr>
					</thead>
					<tbody>
			<?php
			// Loop through the posts
			$serial_number = 1 + ( ( $paged - 1 ) * 20 );
			while ( $query->have_posts() ) {
                               $query->the_post();

                               // Fetch the metadata
                               $tokens_used = get_post_meta( get_the_ID(), 'llama_token_used', true );
                               $time_taken = get_post_meta( get_the_ID(), 'llama_time_taken', true );
                               ?>
							<tr>
								<td>
                              <?php echo esc_html($serial_number); ?>
								</td>
								<td><a class="row-title" href="<?php echo esc_url(get_edit_post_link()); ?>" target="_blank">
                                 <?php echo esc_html(substr( get_the_title(), 0, 150 )); ?>
									</a></td>
								<td>
                                 <?php echo esc_html($tokens_used); ?>
								</td>
								<td>
                                 <?php echo esc_html($time_taken); ?>
								</td>
							</tr>
                                 <?php
                                              $serial_number++;
               }
			// End the table
			?>
					</tbody>
				</table>
			</div>
            <?php
            // Display the pagination
            $total_pages = $query->max_num_pages;
            if ( $total_pages > 1 ) {
                    $current_page = max( 1, $paged );
                    $base_url = esc_url_raw( remove_query_arg( 'paged' ) );
					echo '<div class="wrap"><div class="tablenav bottom"><div class="tablenav-pages"><span class="displaying-num">' . esc_html( $query->found_posts ) . ' items</span>';
					echo '<span class="pagination-links">';				
                    if ( $paged > 1 ) {
				  echo '<a class="prev-page button" href="' . esc_url(add_query_arg( 'paged', $paged - 1, $base_url )) . '"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>';
                        }
                    echo '<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="' . esc_attr($paged) . '" size="1" aria-describedby="table-paging"> of <span class="total-pages">' . esc_html($total_pages) . '</span></span>';
                    if ( $paged < $total_pages ) {
				  echo '<a class="next-page button" href="' . esc_url(add_query_arg( 'paged', $paged + 1, $base_url )) . '"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
                        }
                    echo '</span></div></div></div>';
            }
                 } else {
            // No posts found
            echo '<p><a href="/wp-admin/post-new.php">Create your 1st post with Llama</a></p>';
                 }

           // Reset the post data
           wp_reset_postdata();


	} else if ( $active_tab == 'settings' ) {
        // Display settings content here
        ?>
			<h3>Settings</h3>
			<form method="post" action="options.php">
         <?php settings_fields( 'llama-plugin-settings' ); ?>
         <?php do_settings_sections( 'llama-plugin-settings' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">OpenAI Model Name</th>
						<td> <select name="llama_model_name">
								<option value="gpt-3.5-turbo" <?php selected( get_option( 'llama_model_name' ), 'gpt-3.5-turbo' ); ?>>
									gpt-3.5-turbo</option>
								<option value="gpt-4" <?php selected( get_option( 'llama_model_name' ), 'gpt-4' ); ?>>gpt-4</option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">OpenAI API Secret</th>
						<td><input type="password" name="llama_api_secret" style="width: 400px;"
								value="<?php echo esc_attr( get_option( 'llama_api_secret' ) ); ?>" /></td>
					</tr>
				</table>
         <?php submit_button(); ?>
			</form>
        <?php
	} else {
        ?>
			<style>
				.accordion {
					margin-bottom: 10px;
				}

				.accordion-header {
					background-color: #f1f1f1;
					padding: 10px;
					font-size: 1rem;
					cursor: pointer;
					position: relative;
					border-bottom: 1px solid #ddd;
					padding-bottom: 20px;
					padding-top: 40px;
				}



				.accordion-content * {
					font-size: 0.9rem;
				}

				.accordion-arrow {
					position: absolute;
					top: 50%;
					right: 10px;
					transform: translateY(-50%);
					transition: transform 0.3s ease-in-out;
				}

				.accordion-content {
					display: none;
					padding: 10px;
					background-color: #fff;
				}

				.accordion-arrow {
					flex-shrink: 0;
					width: 1.25rem;
					height: 1.25rem;
					margin-left: auto;
					content: "";
					background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
					background-repeat: no-repeat;
					background-size: 1rem;
					transition: transform .2s ease-in-out;
				}
			</style>
			<h3>Questions? We have answers</h3>
			<div>
				<div class="accordion">
					<div class="accordion-header">How easy is it to install and set up Marketing Llama plugin?<span
							class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<ol>
							<li>
								First, navigate to 'Plugins > Add New' on your WordPress dashboard.
							</li>
							<li>Use the search bar to search for 'Marketing Llama'.</li>
							<li>Once the plugin appears in the search results, install it.</li>
							<li>After installation, activate 'Marketing Llama' from your Plugins page.</li>
							<li>To configure the plugin, go to the 'Llama' menu on the admin side bar and select the 'Settings' tab.
							</li>
							<li>Here, you'll need to enter your OpenAI API key. If you don't already have one, you can register for
								an
								API key by logging into OpenAI’s page and creating a new API token.</li>
							<li>To create a new post, go to 'Posts > Add New'.</li>
							<li>Make sure to use the default Block WordPress editor.</li>
							<li>Inside the editor, click on the '+' icon to bring up a search bar, then search for 'Llama'.</li>
							<li>Once the 'Llama' icon appears, click on it to add the Llama block to your post.</li>
							<li>Finally, write a prompt in the Llama block. This will be sent to Llama, and it will provide you with
								a
								response.</li>
						</ol>
						<p>With these steps, you will have successfully installed, activated, and configured your Marketing Llama
							plugin, and you'll be ready to start creating content with it.</p>
					</div>
				</div>

				<div class="accordion">
					<div class="accordion-header">Will this plugin affect the speed or performance of my WordPress site?<span
							class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin has been meticulously designed and optimized to ensure that it does not impact
							the performance or speed of your WordPress website. The development team behind Marketing Llama has
							prioritized efficient, lightweight coding practices to ensure that the plugin runs seamlessly and
							effectively without contributing to site lag or load times. Therefore, you can confidently use the
							Marketing Llama plugin on your website without worrying about it slowing down your website's
							performance.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">Can I use the Marketing Llama plugin on multiple WordPress websites or do I need
						to purchase a separate premium subscription for each site?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin requires a separate license for each domain on which it's installed.
							Therefore, if you have multiple WordPress websites, you'll need to purchase a separate premium
							subscription for each individual site. This is to ensure that each domain gets the dedicated support,
							updates, and premium features it requires for optimal operation and performance.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">Are there any limitations on the number of times I can use the Llama block or ask
						ChatGPT for suggestions?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>There are no limitations on the number of times you can use the Llama block or ask ChatGPT for
							suggestions. You can use it as much as you want.

							However, the plugin does keep track of token usage. Tokens are units of text that language models read.
							In English, a token can be as short as one character or as long as one word (e.g., "a" or "apple").

							The plugin provides a feature that shows you how many tokens you have used. This can help you keep track
							of your usage and manage it effectively.

							While there is no limitation on the plugin's usage, please be aware that OpenAI may charge you for token
							usage as per their pricing policy. Hence, while the plugin allows unlimited requests, the cost
							associated with these requests will depend on your usage and OpenAI's token-based pricing.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How does the plugin understand the context of my blog?<span
							class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin understands the context of your blog by creating an intelligent and
							comprehensive prompt using both your direct input (user prompt) and the existing content (context) of
							your blog post.

							When you insert the Llama block and input a query, the plugin doesn't just pass on your query in
							isolation to ChatGPT. Instead, it combines your prompt with the existing blog content to create a
							holistic understanding of what the blog is about. This combined prompt provides a rich context that
							helps ChatGPT generate relevant, context-specific suggestions.

							For example, if you're halfway through a blog about vegan baking and you ask ChatGPT for suggestions for
							the next section, the plugin will include the existing content of your blog when making this request. As
							a result, ChatGPT is aware that you're asking for suggestions within the context of vegan baking, and
							its suggestions will be tailored accordingly.

							The premium version of the plugin enhances this feature even further. It allows you to specify your
							brand's unique voice and provide additional data, such as specific facts, quotes, or research reports,
							that you want to be considered in the content generation. This extra information is used to enrich the
							context for ChatGPT, enabling it to generate content that not only fits with the specific topic of the
							blog post but also aligns with your brand's tone and style and includes the specific details you want to
							highlight.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How can I specify my brand's specific language, tone, and style using the plugin?
						Can it support multiple brand styles if I manage different types of content?<span
							class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>In the premium version of the Marketing Llama plugin, you have the ability to customize your brand's
							specific language, tone, and style for the generated content.

							This is done through the settings of the Llama block, where you can define and input your specific
							preferences. For instance, you might specify a more formal tone and complex language for a corporate
							blog, or a conversational tone and simple language for a lifestyle blog.

							This is particularly useful if you manage different types of content as you can set distinct language,
							tone, and style preferences for each one. Each Llama block you insert in your content can be tailored
							individually, offering you great flexibility and control over your content creation process.

							Remember, the objective here is to align the output from ChatGPT with your brand's unique voice to
							provide a coherent and consistent brand experience for your readers.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">What happens if I provide incorrect or irrelevant data? How does the plugin ensure
						the accuracy and relevance of the generated content?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin uses advanced AI technology, specifically ChatGPT, to process and interpret
							the information you provide. If you provide incorrect or irrelevant data, the generated content may not
							meet your expectations in terms of accuracy and relevance. However, the AI is trained to be robust and
							can often provide useful output even with less-than-perfect input.

							As a user, it's your responsibility to provide accurate data to the plugin to get the best possible
							results. The plugin will use the data, facts, quotes, or research reports you provide to enrich the
							generated content, making it more insightful and unique to your blog.

							If you notice any inaccuracies in the generated content, you can tweak the input or guide the AI in a
							different direction to improve the output. The more accurate and relevant your input, the better the
							output will be.

							Please note, however, that while AI can provide very good suggestions and content generation, it does
							not replace a human editor's role in validating and verifying the information's accuracy and relevance.
							It's still crucial to review and edit the generated content before publishing to ensure it meets your
							blog's standards and goals.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How can I provide the specific data, facts, quotes, or research reports to the
						plugin? Is there a specific format I need to follow?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>In the premium version of the Marketing Llama plugin, you indeed have the capability to provide specific
							data, facts, quotes, or research reports. This information can be entered directly into the Llama block.
							Each Llama block you add to your content editor will have an input box where you can enter this
							information in plain text format.

							Moreover, you have the flexibility to enter different pieces of information in multiple Llama blocks.
							This allows you to incorporate diverse facts or data points throughout your content.

							Once this information is entered, the plugin intelligently integrates it into the content suggestions
							generated by ChatGPT. For instance, if you provide a specific fact or statistic, the AI could utilize
							this data to enrich your blog post content, making it more accurate and relevant.

							Despite this capability, it's important to remember that while AI like ChatGPT can greatly enhance your
							content, it's crucial to review and verify the generated content to ensure its accuracy and alignment
							with your objectives. While the plugin will incorporate your provided information to the best of its
							abilities, it's ultimately up to the human user to ensure the content meets the desired standard and
							fits within the context correctly. AI is indeed a powerful tool, but still requires human oversight to
							ensure optimal results.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">If there are issues or bugs with the plugin, what kind of support can I
						expect?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>If you encounter any issues or bugs with the Marketing Llama plugin, you can reach out to our dedicated
							support team at support@marketingllama.ai. We strive to respond to all inquiries within 24-48 hours,
							ensuring that your concerns are addressed promptly.

							In more complex cases, where a bug requires a fix from our development team, we commit to keeping you
							informed about the progress. We understand that such issues can impact your website operations, and our
							aim is to make the process as smooth and as transparent as possible.

							We value our customers greatly and your experience with our product is our top priority. Therefore,
							regardless of the nature of the issue you're facing, you can rest assured that we will do our best to
							assist you and resolve it in a timely manner.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">What is the cancellation policy for the premium subscription? Can I get a refund
						if I'm not satisfied with the plugin?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>At Marketing Llama, we're committed to ensuring that our customers are fully satisfied with our products.
							That's why we have a generous 30-day no-questions-asked refund policy. If for any reason you're not
							satisfied with our premium plugin, all you need to do is send us an email at support@marketingllama.ai
							within 30 days of your purchase.

							We will then proceed to refund your entire purchase, no questions asked. We understand that not every
							product is a perfect fit for everyone, and this is our way of ensuring that you can try our plugin
							risk-free. We aim for your absolute satisfaction and comfort in using our plugin, and we believe that
							our refund policy reflects this commitment.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How will the plugin be updated and will I get access to the latest features as a
						premium subscriber?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>As the developer of the Marketing Llama plugin, we continuously strive to improve its features and fix
							any issues. Premium subscribers automatically have access to all updates at no extra cost. Updates are
							seamlessly incorporated into the plugin, ensuring that your experience is always up-to-date.

							We periodically send out communication to all our premium users with information about new features and
							improvements. Additionally, when an update is available, you'll be notified within your WordPress
							dashboard. You can then choose to update the plugin at your convenience.

							Remember that keeping the plugin updated is not only important for accessing new features, but also for
							ensuring optimal performance and security. If you need help at any point, our customer support team is
							always ready to assist.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">
						Does the plugin have a learning curve? Will I need to be technically savvy to use it to its full
						potential?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin is designed with user-friendliness in mind and doesn't require any extensive
							technical knowledge. It integrates smoothly with the WordPress block editor, which is a familiar
							interface for most WordPress users.

							To use the plugin, you simply insert the Llama block and input your request to ChatGPT, such as asking
							for blog title suggestions or a content outline. It's akin to typing in a text editor, so it doesn't
							require specialized skills.

							In the premium version, you can further customize the content by specifying your brand's specific
							language, tone, and style. This is typically a matter of defining your preferences rather than needing
							technical expertise.

							Similarly, providing specific data, facts, quotes, or research reports for content generation is as
							simple as inputting the information into the plugin.

							However, to get the most out of the plugin, a basic understanding of your content strategy, including
							your target keywords and desired content structure, can be beneficial. If you face any issues or need
							assistance, the plugin's support team should be readily available to help.

							Remember, like any tool, getting used to it might take a little bit of time, but soon it should become a
							natural part of your content creation workflow.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">Can the plugin be used in conjunction with other WordPress plugins or does it have
						any known compatibility issues?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin is designed to be compatible with the majority of other WordPress plugins.
							We've thoroughly tested it to ensure that it does not conflict with popular WordPress plugins. However,
							like with any software, conflicts could potentially occur due to the wide range of plugins available.

							If you experience any issues related to compatibility with other plugins, please let our support team
							know. We are committed to continuously improving the plugin's compatibility and will work on fixing any
							reported issues.

							Before installing the plugin, it's recommended to ensure your other plugins are updated to their latest
							versions to minimize potential compatibility issues. If possible, try the plugin on a staging site first
							to verify its compatibility with your existing setup.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How will my data be stored and protected? What are the plugin's privacy
						policies?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin respects user privacy and has been designed with stringent data handling and
							protection mechanisms. Here are some key aspects to consider:</p>
						<ol>
							<li>Interaction with ChatGPT: AI models like ChatGPT, which is utilized by our plugin, do not retain or
								store user input data beyond the duration of the interaction. The model does not use this data to
								improve itself or for any other purpose. This means that once the interaction is over, the
								information
								you provided is no longer stored or accessible within the AI system.</li>
							<li>Anonymizing Data: In the process of using our plugin, you may need to input data to guide the AI in
								generating relevant content. We highly recommend anonymizing this data to protect any potentially
								sensitive information. Specifically, we advise removing any personally identifiable information
								before
								submission to the plugin.</li>
							<li>No Personal Data Storage: The plugin doesn't store personal data. All information entered into the
								Llama block is used solely for the purpose of generating content during that specific session and is
								not
								stored or used afterward.</li>
						</ol>
						<p>In conclusion, you can rest assured knowing that the Marketing Llama plugin prioritizes your data privacy
							and security. Always remember to anonymize any potentially sensitive data before using the plugin to
							generate content.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">Are there any discounts for longer-term commitments, such as annual
						subscriptions?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>Yes, Marketing Llama does offer a discount for users who choose longer-term commitments. Specifically, if
							you opt for an annual subscription, you receive a 20% discount compared to the monthly rate. This
							discount is a great way to save money if you're planning to use the plugin consistently over the course
							of the year.

							For more information on this and other pricing details, please visit the Marketing Llama pricing page
							at:<a href=" https://marketingllama.ai/pricing/" _target="blank">
								https://marketingllama.ai/pricing/</a>. This page will
							provide you with comprehensive information about
							all the pricing options available, including the cost of monthly versus annual subscriptions.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">How does the plugin integrate with SEO and other marketing efforts on my
						website?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>The Marketing Llama plugin is designed with a focus on enhancing the content creation process and SEO
							optimization of your blog posts. The following are some ways in which you can use the plugin to improve
							the SEO of your website:</p>
						<ol>
							<li>Keyword Analysis: The plugin, through the integration with ChatGPT, can analyze the keyword usage
								and density in your blog post. For example, you can ask, "Analyze the keyword usage and density in
								this blog post. Are the keywords properly used? Are they overused or underused?"</li>
							<li>Meta Description and Title Tag: The plugin can help identify and optimize the meta description and
								title tag of your blog post. A prompt could be, "Identify the blog's meta description and title tag.
								Are they optimized for the main keywords and meet the required length?"</li>
							<li>
								Content Structure: The plugin allows for analysis of content structure with regards to SEO
								principles. A possible prompt can be, "Review the blog's content structure. How well does it follow
								SEO principles such as headers (H1, H2, H3) usage, bullet points, and paragraph length?"
							</li>
							<li>
								Readability and User Experience: It helps in reviewing the readability and user experience of your
								blog post. For instance, you can ask, "Analyze the readability and user experience of this blog
								post. Does it match SEO guidelines for easy readability?"
							</li>
							<li>URL Suggestion: You can ask the plugin to suggest an SEO-friendly URL for your blog post. A sample
								prompt could be, "Suggest a URL for this blog post that is SEO-friendly, i.e., short, descriptive,
								and includes keywords?"</li>
							<li>SEO Title and Meta Description: The plugin can also be used to generate an SEO title and meta
								description for your blog post. You could ask, "Suggest an SEO title and SEO meta description for
								this blog post."</li>
							<li>Content Analysis: The plugin aids in the analysis of the content, ensuring that it provides unique
								and valuable information and effectively answers the user's potential queries. A potential prompt
								is, "Analyze the content of the blog post. Does it provide unique and valuable information, and how
								well does it answer the user's potential queries?"
							</li>

						</ol>
						<p>By utilizing these features, you can ensure your content is better optimized for search engines, thus
							improving your website's visibility and ranking.</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">Is the plugin multilingual? Can it generate content in other languages or adhere
						to specific cultural nuances?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>Yes, the Marketing Llama plugin is designed to support multi-language content generation, including
							languages such as Spanish, French, and German, among others.
							<p />
						<p>Here's how you can use it: </p>
						<p>For Spanish, you might use a prompt like:</p>
						<p>"Sugiere tres títulos de blog sobre el cambio climático" <br>(English translation: "Suggest three
							blog post titles about climate change")
						</p>
						<p>For French, your prompt could be:</p>

						<p>"Propose une ébauche de contenu pour un article sur la cuisine française"
							<br>
							(English translation: "Propose a content outline for a post about French
							cuisine")
						</p>
						<p> For German, you could ask:</p>

						<p>"Schlage drei neue Titel für einen Blogbeitrag über erneuerbare Energien vor" <br>

							(English translation: "Suggest three new titles for a blog post about renewable
							energy")
						</p>

						<P>Just type these prompts in the Llama block in your respective language, and the
							plugin will generate content in the language you've used for your prompt. This makes it easier for you
							to create content in different languages and cater to a more diverse audience.
						</p>
					</div>
				</div>
				<div class="accordion">
					<div class="accordion-header">If I need a feature that is currently not provided by the plugin, can I request
						it?<span class="accordion-arrow"></span></div>
					<div class="accordion-content">
						<p>Yes, as a user of Marketing Llama, you can request new features. If there's a specific capability or
							functionality you'd like to see, you're encouraged to send an email to support@marketingllama.ai
							detailing your request.</p>

						<p>Our development team values user feedback and takes into account the needs of our customers in planning
							future updates. The feasibility of each requested feature is evaluated based on several factors. These
							include its complexity, the general demand among users, and how well it aligns with our overall product
							roadmap.
							<p />

						<p>After receiving a feature request, our team considers its technical feasibility and the potential
							benefits to the overall user base. If the feature aligns with the product's goals and enough users
							express interest in it, it may very well be included in a future update.</p>

						<p>However, it's important to note that the time frame for such developments can vary widely. We appreciate
							your patience and assure you that your request will be given serious consideration.</p>

					</div>
				</div>

			</div>

			<script>     // Get all accordion headers
				var accordionHeaders = document.querySelectorAll('.accordion-header');
				// Add click event listener to each accordion header
				accordionHeaders.forEach(function (header) {
					header.addEventListener('click', function () {
						// Toggle the display of the associated accordion content
						var content = this.nextElementSibling;
						if (content.style.display === 'block') {
							content.style.display = 'none';
							this.style["background-color"] = '#f1f1f1';
							this.querySelector('.accordion-arrow').style.transform = 'rotate(0deg)';
							this.style["border-bottom"] = '1px solid #ddd';
						}
						else {
							content.style.display = 'block';
							this.querySelector('.accordion-arrow').style.transform = 'rotate(180deg)';
							this.style["border-bottom"] = 'none';
							this.style["background-color"] = '#fff';
							content.style["border-bottom"] = '1px solid #ddd';
						}
					});
				});

			</script>
        <?php
	}
	// End the buffer and echo
	echo ob_get_clean();
}
