<?php

/**
 * Integration Demo.
 *
 * @package   Woocommerce My plugin Integration
 * @category Integration
 * @author   Addweb Solution Pvt. Ltd.
 */
if (!class_exists('WC_Barkah_Tools_Integration')) :
    class WC_Barkah_Tools_Integration extends WC_Integration
    {
        /**
         * Init and hook in the integration.
         */
        public function __construct()
        {
            global $woocommerce;
            $this->id                 = 'barkah-tools';
            $this->method_title       = __('Barkah Tools');
            $this->method_description = __('Barkah Tools action to wa & fb pixel.');
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();
            // Define user set variables.
            $this->defineVars();
            // Actions.
            add_action('woocommerce_update_options_integration_' .  $this->id, array($this, 'process_admin_options'));
        }
        /**
         * Initialize integration settings form fields.
         */
        public function init_form_fields()
        {

            $this->form_fields = $this->defineFields();
        }

        function defineVars()
        {
            $fields = $this->defineFields();
            // $this->bt_wa_no = $this->get_option('bt_wa_no');
            foreach ($fields as $fk => $fv) {
                $this->{$fk} = $this->get_option($fk);
            }
        }

        function defineFields()
        {
            $postPage = get_posts(['post_type' => ['post', 'page'], 'numberposts' => -1]);
            $postPageArray = [];
            $postPageArray = [0 => __("All Page")];

            foreach ($postPage as $p) {
                $postPageArray[$p->post_name] = $p->post_title;
            };
            $fields = array(
                'bt_wa_no' => [
                    'title'             => __('Wa Number'),
                    'type'              => 'number',
                    'description'       => __('Enter Wa Number with internatinal format'),
                    'desc_tip'          => true,
                    'default'           => '',
                    'css'      => 'width:170px;',
                ],
                'bt_wa_msg' => [
                    'title'             => __('Custom Message'),
                    'type'              => 'textarea',
                    'description'       => __('Enter Wa Message'),
                    'desc_tip'          => false,
                    'default'           => '',
                    'css'      => 'max-width:300px;',
                ],
                'bt_wa_btntext' => [
                    'title'             => __('Button Text'),
                    'type'              => 'text',
                    'description'       => __('Button Text, Ex: Chat Now'),
                    'desc_tip'          => false,
                    'default'           => '',
                    'css'      => 'max-width:300px;',
                ],
                // 'bt_wa_l_qty' => [
                //     'title'             => __('Label Qty'),
                //     'type'              => 'text',
                //     'description'       => __('Quantity:'),
                //     'desc_tip'          => false,
                //     'default'           => '',
                //     'css'      => 'max-width:300px;',
                // ],
                'bt_wa_l_price' => [
                    'title'             => __('Label Price'),
                    'type'              => 'text',
                    'description'       => __('Price:'),
                    'desc_tip'          => false,
                    'default'           => '',
                    'css'      => 'max-width:300px;',
                ],
                'bt_wa_l_url' => [
                    'title'             => __('Label URL'),
                    'type'              => 'text',
                    'description'       => __('Url:'),
                    'desc_tip'          => false,
                    'default'           => '',
                    'css'      => 'max-width:300px;',
                ],
                // 'bt_wa_l_total' => [
                //     'title'             => __('Label Total'),
                //     'type'              => 'text',
                //     'description'       => __('Total:'),
                //     'desc_tip'          => false,
                //     'default'           => '',
                //     'css'      => 'max-width:300px;',
                // ],
                // 'bt_wa_l_payment' => [
                //     'title'             => __('Label Payment'),
                //     'type'              => 'text',
                //     'description'       => __('Payment:'),
                //     'desc_tip'          => false,
                //     'default'           => '',
                //     'css'      => 'max-width:300px;',
                // ],
                'bt_wa_l_thx' => [
                    'title'             => __('Label Thank You'),
                    'type'              => 'text',
                    'description'       => __('Thank You'),
                    'desc_tip'          => false,
                    'default'           => '',
                    'css'      => 'max-width:300px;',
                ],
                'bt_pixel_hd' => [
                    'title' => "Facebook Pixel settings",
                    'type' => 'title'
                ],
                'bt_active' => [
                    'title' => __("Active Pixel?"),
                    'type' => 'checkbox',
                    'default' => 'no'
                ],
                'bt_pixelid' => [
                    'title' => __("Pixel ID"),
                    'type' => 'text',
                    'desc_tip' => true,
                    'description' => 'Insert FB Pixel ID '
                ],
                'bt_testing' => [
                    'title' => "Testing",
                    'type' => 'title'
                ],
                // 'bt_testing_active' => [
                //     'title' => __("Active Debug?"),
                //     'type' => 'checkbox',
                //     'default' => 'no'
                // ],
                'bt_testing_page' => [
                    'title' => __("Select Page to Test"),
                    'type' => 'select',
                    'default' => 0,
                    'description' => __("shown same page"),
                    'options' => $postPageArray
                ],
            );
            return $fields;
        }
    }
endif;
