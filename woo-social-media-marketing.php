<?php
/**
 * Plugin Name: Woo Social Media Marketing
 * Plugin URI: https://around.io
 * Description: Around.io helps you to put your WooCommerce store's social media marketing on Automation.Plan and promote your product listings and many more features.
 * Version: 2.0
 * Author: Natwar Maheshwari
 * License: GPLv2
 */
add_action( 'rest_api_init', 'add_custom_endpoints' );
add_action('admin_menu','aroundio_actions');



function add_endpoint($request) {
    $filters = $request->get_body_params();
    foreach($filters as $key=>$value) {
        $params = json_decode($key);
    }
    $args = array(
        'post_type' => 'product',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => $params->limit,
        'offset' => $params->offset,
        'paged' => get_query_var( 'paged' ),
    );
    $products = get_posts($args);
    $productArray = array();
    foreach($products as $p) {
        $product = new WC_product($p);
        $attachment_ids = $product->get_gallery_image_ids();
        $allimages = array();
        foreach($attachment_ids as $ids){
            $allimages[] = wp_get_attachment_url( $ids );
        }
        $productArray[] = array(
            "product_id" => $p->ID,
            "product_title" => $product->get_name(),
            "product_url" => $product->get_permalink(),
            "product_image" => wp_get_attachment_image_src( get_post_thumbnail_id( $p->ID ), 'single-post-thumbnail' )[0],
            "product_all_images" => $allimages,
            "product_short_description" => $product->get_short_description(),
            "product_description" => $product->get_description(),
            "product_create_date" => $p->post_date,
            "product_update_date" => $p->post_modified,
        );
    }
    return rest_ensure_response( $productArray );
}

function get_version() {
    $version = array(
        "wc_version" => WC()->version
    );
    return rest_ensure_response( $version );
}

function add_custom_endpoints() {
    register_rest_route( 'aroundio/v1', '/products', array(
        'methods'  => 'POST',
        'callback' => 'add_endpoint'
    ) );
    register_rest_route( 'aroundio/v1', '/wc_version', array(
        'methods'  => 'GET',
        'callback' => 'get_version'
    ) );
}



function aroundio_actions(){

    wp_enqueue_script( 'woo_plugin_script', plugins_url('/woo-social-media-marketing.js', __FILE__), array('jquery'), '1.0', true );
    add_menu_page('Around Woocommerce Plugin','Around.io','manage_options','woocommercesocialhome','woocommerceplugin_admin',plugins_url('images/logo_mini.png',__FILE__),2);
}

function woocommerceplugin_admin()
{
    $site_url = get_site_url();

    ?>
    <div class="wrap">
        <style>
            #pluginBody {
                margin: 24px auto;
                height: 100%;
                font: 10px/16px sans-serif;
                background-color: rgba(0,0,0,0.05);
            }
            h2 {
                color: #1abc9c;
            }
            .unit p, ol, ul {
                line-height: 24px;
                font-size: 16px;
            }
            a {
                color: #1abc9c;
            }
            .unit {
                margin-bottom: 24px;
                font-size: 1.2em;
            }
            .unit h2 {
                font-size: 1.6em;
                line-height: 24px;
            }
            .unit h3 {
                font-size: 1.6em;
            }
            .unit ol li {
                padding-bottom: 16px;
            }
            .unit h3.key span {
                padding: 8px;
                background-color: #fff;
                border: 1px solid #1abc9c;
                border-radius: 4px;
            }
            .unit.marginBottom {
                margin-bottom: 48px;
            }
            .alnCenter {
                text-align: center;
            }

            .logo img {
                max-width: 100px;
            }

        </style>

        <div class="unit marginBottom alnCenter">
            <div class="logo">
                <img src="<?php echo plugins_url('images/aroundio-logo.png',__FILE__); ?>">
            </div>
        </div>

        <div class="unit marginBottom alnCenter">
            <h2>Congrats! You just installed Around.io Social Media (Woo) Plugin successfully!</h2>
        </div>

        <!--<div class="unit marginBottom">
            <h3 class="key">
                Your Around.io key is: <span>nuyBcCUGp0CnxNdnEWxq</span>
            </h3>
        </div>-->

        <div class="unit marginBottom" id="generate-key-box">
            <h3 class="key">
                <input type="hidden" id="site-url" value="<?php echo $site_url; ?>">
                <input type="hidden" id="ajax-nonce" value="<?php echo wp_create_nonce( "generate-woo-key" ); ?>">
                Generate Around Key: <input type="button" id="generate-woo-key" value="Click to generate" style="cursor: pointer; font-weight: normal; font-size: 0.9em; padding: 8px; color: #fff; background: #1abc9c; border: 1px solid #1abc9c; border-radius: 4px;">
            </h3>
        </div>

        <div class="unit marginBottom" id="key-box" style="display: none;">
            <h3 class="key">
                Your Around.io key is: <span id="aroundKey"><?php //echo $resp_store->aroundKey; ?></span>
            </h3>
        </div>

        <div class="unit">
            <p>
                Here's what you need to do next:
            </p>
        </div>

        <div class="unit">
            <ol>
                <li>
                    Go to your WooCommerce plugin settings and <a href="#howto">create a new set of consumer key and secret</a>. Copy this somewhere.
                </li>
                <li>
                    Sign up at <a href="https://app.around.io/#register" target="_blank">Around.io</a> (skip to the next step if you've already done this).
                </li>
                <li>
                    Paste your Around.io key, your WooCommerce API and secret in the respective fields when you sign up at Around.io.
                </li>
            </ol>
        </div>
        <div class="unit">
            <p>
                That's it. You will see all your products in your Around.io dashboard.
            </p>
        </div>
        <div class="unit marginBottom">
            <p>
                Need any help? Just send an email to <a href="mailto:hi@around.io">hi@around.io</a> and we'll help you get started right away.
            </p>
        </div>

        <div id="howto" class="unit">
            <h3>
                How to create new consumer key and secret in WooCommerce?
            </h3>
        </div>

        <div class="unit">
            <ol>
                <li>
                    Go to the WooCommerce plugin "Settings"
                </li>
                <li>
                    Click on the "API" tab
                </li>
                <li>
                    Under "General Options", make sure "Enable REST API" is checked
                </li>
                <li>
                    Then click on "Keys/Apps"
                </li>
                <li>
                    Click on "Add Key"
                </li>
                <li>
                    Type any name in the "Description" field
                </li>
                <li>
                    Change "Permissions" to "Read/Write" (select from the dropdown)
                </li>
                <li>
                    Click on "Generate API Key"
                </li>
            </ol>

            <p>
                This will generate your new consumer key and secret which you can paste in Around.io.
            </p>
        </div>
    </div>
    <?php
}
function wooplugin_generate_key()
{
    if (!defined('ABSPATH'))
    {
        echo 'error';
        exit();
    }

    add_action( 'wp_ajax_gen_key', 'my_action_function' );
    function my_action_function() {
        check_ajax_referer( 'generate-woo-key', 'ajaxNonce' );
        wp_die();
    }


    if ($_POST['market'] != 'woocommerce' || filter_var($_POST['siteurl'], FILTER_VALIDATE_URL) === false)
    {
        echo 'error';
        exit();
    }

    $request['request'] = array(
        'market' => $_POST['market'],
        'storeDomain' => $_POST['siteurl']
    );

    $appUrl  = "https://app.around.io/api/v2/store/generateAroundKey";

    $ch = curl_init();
    $header[] = 'Content-Type:application/json';
    curl_setopt($ch, CURLOPT_URL, $appUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    $resp_store = json_decode(curl_exec($ch));

    echo esc_attr($resp_store->aroundKey);
    die;
}

add_action('wp_ajax_gen_woo_key','wooplugin_generate_key');
?>