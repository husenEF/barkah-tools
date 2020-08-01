<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function bt_add_button_plugin()
{
    global $product;

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url_path = parse_url($url, PHP_URL_PATH);
    $slug = pathinfo($url_path, PATHINFO_BASENAME);

    if ($slug == '') {
        $pageId = $_GET['page_id'];
        $currentPage = get_post($pageId);
        $slug = $currentPage->post_name;
    }
    $product_type = $product->get_type();
    $id = $product->get_id();
    $title = $product->get_name();
    $currency = get_woocommerce_currency_symbol();
    $price = wc_get_price_including_tax($product);
    $product_url = $product->get_permalink();
    $product_type = $product->get_type();
    $class = sprintf('button add-to-cart barkah-tools product_type_%s', $product->get_type());

    $btToolSetting = get_option('woocommerce_barkah-tools_settings', []);
    $bt_wa_no = $btToolSetting['bt_wa_no'];
    $bt_wa_msg = $btToolSetting['bt_wa_msg'];
    $bt_wa_btntext = $btToolSetting['bt_wa_btntext'];
    // $bt_wa_l_qty = $btToolSetting['bt_wa_l_qty'];
    $bt_wa_l_price = $btToolSetting['bt_wa_l_price'];
    $bt_wa_l_url = $btToolSetting['bt_wa_l_url'];
    // $bt_wa_l_total = $btToolSetting['bt_wa_l_total'];
    // $bt_wa_l_payment = $btToolSetting['bt_wa_l_payment'];
    $bt_wa_l_thx = $btToolSetting['bt_wa_l_thx'];
    // debug
    // $bt_testing_active = $btToolSetting['bt_testing_active'];
    $bt_testing_page = $btToolSetting['bt_testing_page'];

    //pixel
    $bt_active = $btToolSetting['bt_active'];
    $bt_pixelid = $btToolSetting['bt_pixelid'];

    $dataAttr = " data-curency='$currency'";
    $dataAttr .= " data-product-name='$title'";
    $dataAttr .= " data-prod-id='$id'";
    $dataAttr .= " data-type='$product_type'";
    // sanitize_key()

    if ($bt_wa_no != '') {
        if ($bt_wa_msg == '') $message_price = "Hello, I want to buy:";
        else $message_price = "$bt_wa_msg";

        $encode_custom_message_price = urlencode($message_price);
        $encode_title = urlencode($title);
        $encode_price_label = urlencode($bt_wa_l_price);
        $encode_price = urlencode($price);
        $encode_url_label = urlencode($bt_wa_l_url);
        $encode_product_url = urlencode($product_url);
        $encode_thanks = urlencode($bt_wa_l_thx);
        $phone = sanitize_text_field($bt_wa_no);
        // print_r($bt_wa_no);
        $final_message = "$encode_custom_message_price%0D%0A%0D%0A*$encode_title*%0D%0A*$encode_price_label*%20$currency$encode_price%0D%0Avar:*$encode_url_label:*%20$encode_product_url%0D%0A%0D%0A";
        $final_message .= "%0D%0A%0D%0A$encode_thanks";
        $button_url = "https://wa.me/$phone?text=$final_message";

        if ($bt_active == 'yes' && $bt_pixelid !== '') {
            $dataAttr .= " data-pixelid='$bt_pixelid'";
        }

        $isDebug = ($bt_testing_page != '0') ? true : false;
        if (!$isDebug) {
?>
            <a href="<?php echo $button_url ?>" class="<?php echo $class ?>" target="blank" <?php echo $dataAttr ?> data-pixel="BuyViaWhatsApp">
                <button type="button" id="sendbtn bt-tools-button-click" class="btn btn-add-to-cart single_add_to_cart_button button alt">
                    <?php _e($bt_wa_btntext) ?>
                </button>
            </a>
            <?php
        } else {
            if ($slug == $bt_testing_page) {
            ?>
                <a href="<?php echo $button_url ?>" class="<?php echo $class ?>" target="blank" <?php echo $dataAttr ?> data-pixel="BuyViaWhatsAppTest">
                    <button type="button" id="sendbtn bt-tools-button-click" class="btn btn-add-to-cart single_add_to_cart_button button alt">
                        <?php _e($bt_wa_btntext) ?>
                    </button>
                </a>
<?php
            }
        }
    }
}

add_action('woocommerce_after_add_to_cart_button', 'bt_add_button_plugin', 5);


// Start calling main css
function wcbarkahtoolsjscss()
{
    wp_register_style('barkah-tools-css-front', BT_PLUGIN_DIR_URI . 'assets/css/front.css');
    wp_enqueue_style('barkah-tools-css-front');

    wp_enqueue_script('barkah-tools-js-front', BT_PLUGIN_DIR_URI . 'assets/js/front.js', '', '', true);
    wp_localize_script('barkah-tools-js-front', 'bt_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'wcbarkahtoolsjscss');


add_action('wp_ajax_bt_order_wa', 'handling_bt_changevariant', 0);
add_action('wp_ajax_nopriv_bt_order_wa', 'handling_bt_changevariant');

function handling_bt_changevariant()
{
    if ($_POST) {
        $post = $_POST;

        $product = wc_get_product($post['prod_id']);

        $content_type = ($product->get_type() == 'variable-subscription' || $product->get_type() == 'variable') ? 'product_group' : '';
        $response = [
            'version' => WC()->version,
            "content_name" => $product->get_name(),
            'content_ids'  => wp_json_encode(["wc_post_id_" . $product->get_id()]),
            'content_type' => $content_type,
            // 'value'        => $order->get_total(),
            'price' => $product->get_price(),
            'product_type' => $product->get_type(),
            'currency'     => get_woocommerce_currency(),
        ];
        wp_send_json($response);
    } else {
        wp_send_json([], 400);
    }
    // echo json_encode($_POST);
}
