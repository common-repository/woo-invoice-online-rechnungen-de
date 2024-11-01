<?php

/*
 * Plugin Name:  WooCommerce Invoice Online-Rechnungen.de
 * Plugin URI:   https://www.online-rechnungen.de
 * Description:  Online-Rechnungen.de WooCommerce Plugin
 * Version:      20180716
 * Author:       online-rechnungen.de
 * Author URI:   https://www.online-rechnungen.de/
 * License:      GPL3
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 */
defined('ABSPATH') or die('No script kiddies please!');
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     *  Loading Text Domain
     */
    function iwvor_load_textdomain() {
        load_plugin_textdomain('woo-or', false, basename(dirname(__FILE__)) . '/languages/');
    }

    add_action('plugins_loaded', 'iwvor_load_textdomain');
    /**
     *  Loading settings
     */
    if (is_admin()) {
        /* Loading Classes */
        include_once 'orapi/IwvOrSender.php';
        include_once 'orapi/IwvOrRecipient.php';
        include_once 'orapi/IwvOrDocument.php';
        include_once 'classes/IwvOrSettings.php';
        $my_settings_page = new IwvOrSettings();



        /**
         * Adding Buttons to
         */
        add_filter('woocommerce_admin_order_actions', 'add_custom_order_status_actions_button', 100, 2);

        function add_custom_order_status_actions_button($actions, $order) {
            // Display the button for all orders that have a 'processing' status
            $pdf = get_post_meta($order->get_id(), 'iwvor_pdf', true);
            if ($pdf) {
                $actions['iwvor_download_invoice'] = array(
                    'url' => wp_nonce_url(admin_url('admin-post.php?action=iwvor_download_invoice&order_id=' . $order->get_id()), 'admin_post_iwvor_download_invoice'),
                    'name' => __('Download Invoice', 'woo-or'),
                    'action' => 'iwvor_download_invoice',
                );
                $actions['iwvor_delete_invoice'] = array(
                    'url' => wp_nonce_url(admin_url('admin-post.php?action=iwvor_delete_invoice&order_id=' . $order->get_id()), 'admin_post_iwvor_delete_invoice'),
                    'name' => __('Delete Invoice', 'woo-or'),
                    'action' => 'iwvor_delete_invoice',
                );
            } else {
                if ($order->has_status(array('completed', 'processing'))) {
                    // The key slug defined for your action button
                    $actions['iwvor_create_invoice'] = array(
                        'url' => wp_nonce_url(admin_url('admin-post.php?action=iwvor_create_invoice&order_id=' . $order->get_id()), 'admin_post_iwvor_create_invoice'),
                        'name' => __('Create Invoice', 'woo-or'),
                        'action' => 'iwvor_create_invoice',
                    );
                    $actions['iwvor_preview_invoice'] = array(
                        'url' => wp_nonce_url(admin_url('admin-post.php?action=iwvor_preview_invoice&order_id=' . $order->get_id()), 'admin_post_iwvor_preview_invoice'),
                        'name' => __('Preview Invoice', 'woo-or'),
                        'action' => 'iwvor_preview_invoice',
                    );
                }
            }
            return $actions;
        }

        /** Style Buttons */
        add_action('admin_head', 'add_custom_order_status_actions_button_css');

        function add_custom_order_status_actions_button_css() {
            echo '<style>' .
            '.wc-action-button-iwvor_create_invoice {  background-color: #5b841b!important;color: #fff!important;border-color: #5b841b!important;}' .
            '.wc-action-button-iwvor_create_invoice::after { font-family: woocommerce !important; content: "\e02b" !important;}' .
            '.wc-action-button-iwvor_preview_invoice {  background-color: #31708f!important;color: #fff!important;border-color: #31708f!important;}' .
            '.wc-action-button-iwvor_preview_invoice::after { font-family: woocommerce !important; content: "\e010" !important;}' .
            '.wc-action-button-iwvor_download_invoice {  background-color: #31708f!important;color: #fff!important;border-color: #31708f!important;}' .
            '.wc-action-button-iwvor_download_invoice::after { font-family: woocommerce !important; content: "\e02e" !important;}' .
            '.wc-action-button-iwvor_delete_invoice {  background-color: #a94442!important;color: #fff!important;border-color: #a94442!important;}' .
            '.wc-action-button-iwvor_delete_invoice::after { font-family: woocommerce !important; content: "\e602" !important;}' .
            '</style>' .
            '<script type="text/javascript">' .
            "jQuery(function($){ $(document).ready(function(){ $('.wc-action-button-iwvor_delete_invoice,.wc-action-button-iwvor_create_invoice').on('click',function(){ " .
            "var url=$(this).attr('href');$.ajax({type: 'POST',url: url,success: function(){ window.location=document.location.href; }});return false;" .
            "});  });});" .
            '//window.location=document.location.href;' .
            '</script>';
        }

    }

    /**
     * Download Function
     */
    add_action('admin_post_iwvor_download_invoice', 'iwvor_admin_download_invoice');

    function iwvor_admin_download_invoice() {
        $id = isset($_GET['order_id']) ? $_GET['order_id'] : -1;
        $pdfName = get_post_meta($id, 'iwvor_pdf', true);
        $filePath = plugin_dir_path(__FILE__) . '/invoices/' . $pdfName;
        if (!is_file($filePath)) {
            throw new \Exception(__('File not found', 'woo-or'));
        }
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $id . ".pdf");
        echo readfile($filePath);
    }

    /**
     *  Delete Function
     */
    add_action('admin_post_iwvor_delete_invoice', 'iwvor_admin_delete_invoice');

    function iwvor_admin_delete_invoice() {
        $id = isset($_GET['order_id']) ? $_GET['order_id'] : -1;
        $pdfName = get_post_meta($id, 'iwvor_pdf', true);
        $filePath = plugin_dir_path(__FILE__) . '/invoices/' . $pdfName;
        if (is_file($filePath)) {
            unlink($filePath);
        }
        delete_post_meta($id, 'iwvor_pdf');
    }

    /**
     * Create Document Function
     */
    add_action('admin_post_iwvor_create_invoice', 'iwvor_admin_create_invoice');

    function iwvor_admin_create_invoice() {
        createDocument(false, false);
    }

    /**
     * Preview Document Function
     */
    add_action('admin_post_iwvor_preview_invoice', 'iwvor_admin_preview_invoice');

    function iwvor_admin_preview_invoice() {
        createDocument(true, true);
    }

    function createDocument($preview = false, $startDownload = true) {
        $inv = new IwvOrDocument;
        $inv->sender->loadFromDatabase();
        $id = isset($_GET['order_id']) ? $_GET['order_id'] : -1;
        if (!$inv->loadOrder($id)) {
            throw new \Exception('Order not found');
        }
        $ret = $inv->createDocument();
        if (!$preview) {
            $options = (array) get_option('iwvor_system');
            $options['increment'] = (isset($options['increment']) ? intval($options['increment']) : 1) + 1;
            update_option('iwvor_system', $options, false);
            $fileName = $inv->docName . '_invoice.pdf';
            $pdfName = (new \DateTime)->format('U') . '.pdf';
            update_post_meta($id, 'iwvor_pdf', $pdfName);
            file_put_contents(plugin_dir_path(__FILE__) . '/invoices/' . $pdfName, $ret);
        } else {
            $fileName = $inv->docName . '_preview.pdf';
        }
        if ($startDownload) {
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $fileName);
            echo $ret;
        }
        exit();
    }

}