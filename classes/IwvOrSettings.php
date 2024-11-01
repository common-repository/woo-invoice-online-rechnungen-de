<?php

class IwvOrSettings {

    protected $pluginSlug = 'online-rechungen-free';

    /**
     * Start up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        add_submenu_page(
                'woocommerce', __('Online-Rechnungen', 'woo-or'), __('Online-Rechnungen', 'woo-or'), 'manage_woocommerce', $this->pluginSlug, array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
// Set class property
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'iwvor_sender';
        $this->options = get_option($active_tab);
        ?>
        <div class="wrap">
            <h1>Online-Rechnungen.de</h1>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo admin_url('admin.php?page=' . $this->pluginSlug . '&tab=iwvor_sender'); ?>" class="nav-tab <?php echo $active_tab == 'iwvor_sender' ? 'nav-tab-active' : ''; ?>"><?php _e('Company Settings', 'woo-or') ?></a>
                <a href="<?php echo admin_url('admin.php?page=' . $this->pluginSlug . '&tab=iwvor_bank'); ?>" class="nav-tab <?php echo $active_tab == 'iwvor_bank' ? 'nav-tab-active' : ''; ?>"><?php _e('Bank Settings', 'woo-or') ?></a>
                <a href="<?php echo admin_url('admin.php?page=' . $this->pluginSlug . '&tab=iwvor_system'); ?>" class="nav-tab <?php echo $active_tab == 'iwvor_system' ? 'nav-tab-active' : ''; ?>"><?php _e('System Settings', 'woo-or') ?></a>
            </h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields($active_tab . '_group');
                do_settings_sections($active_tab . '_tab');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        /* Company Details */
        register_setting(
                'iwvor_sender_group', // Option group
                'iwvor_sender' // Option name
        );
        add_settings_section(
                'iwvor_sender_section', // ID
                '', // Title
                array($this, 'print_section_info'), // Callback
                'iwvor_sender_tab' // Page
        );
        add_settings_field(
                'company', // ID
                __('Company', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                ['id' => 'company', 'form' => 'iwvor_sender'] // Arguments
        );
        add_settings_field(
                'firstName', // ID
                __('Firstname', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                ['id' => 'firstName', 'form' => 'iwvor_sender'] // Arguments
        );

        add_settings_field(
                'lastname', // ID
                __('Lastname', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'lastname', 'form' => 'iwvor_sender') // Args
        );
        add_settings_field(
                'street', // ID
                __('Street', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'street', 'form' => 'iwvor_sender') // Args
        );
        add_settings_field(
                'streetNumber', // ID
                __('Street Number', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'streetNumber', 'form' => 'iwvor_sender') // Args
        );
        add_settings_field(
                'postal', // ID
                __('Postal', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'postal', 'form' => 'iwvor_sender') // Args
        );
        add_settings_field(
                'city', // ID
                __('City', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'city', 'form' => 'iwvor_sender') // Args
        );
        add_settings_field(
                'country', // ID
                __('Country', 'woo-or'), // Title
                array($this, 'input_render'), // Callback
                'iwvor_sender_tab', // Page
                'iwvor_sender_section', // Section
                array('id' => 'country', 'form' => 'iwvor_sender') // Args
        );
        /* Bank Details */
        register_setting(
                'iwvor_bank_group', // Option group
                'iwvor_bank' // Option name
        );
        add_settings_section(
                'iwvor_bank_section', // ID
                '', // Title
                array($this, 'print_section_info'), // Callback
                'iwvor_bank_tab' // Page
        );
        add_settings_field(
                'bank', // ID
                __('Bank', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_bank_tab', // Page
                'iwvor_bank_section', // Section
                ['id' => 'bank', 'form' => 'iwvor_bank'] // Arguments
        );
        add_settings_field(
                'iban', // ID
                __('IBAN', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_bank_tab', // Page
                'iwvor_bank_section', // Section
                ['id' => 'iban', 'form' => 'iwvor_bank'] // Arguments
        );
        add_settings_field(
                'bic', // ID
                __('BIC/SWIFT', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_bank_tab', // Page
                'iwvor_bank_section', // Section
                ['id' => 'bic', 'form' => 'iwvor_bank'] // Arguments
        );
        /* Online-Rechnungen System */
        register_setting(
                'iwvor_system_group', // Option group
                'iwvor_system' // Option name
        );
        add_settings_section(
                'iwvor_system_section', // ID
                '', // Title
                array($this, 'print_section_info'), // Callback
                'iwvor_system_tab' // Page
        );
        add_settings_field(
                'prefix', // ID
                __('Invoice Prefix', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_system_tab', // Page
                'iwvor_system_section', // Section
                ['id' => 'prefix', 'form' => 'iwvor_system'] // Arguments
        );
        add_settings_field(
                'suffix', // ID
                __('Invoice Suffix', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_system_tab', // Page
                'iwvor_system_section', // Section
                ['id' => 'suffix', 'form' => 'iwvor_system'] // Arguments
        );
        add_settings_field(
                'increment', // ID
                __('Invoice Increment Number', 'woo-or'), // Label
                array($this, 'input_render'), // Renderer
                'iwvor_system_tab', // Page
                'iwvor_system_section', // Section
                ['id' => 'increment', 'form' => 'iwvor_system'] // Arguments
        );
        add_settings_field(
                'textBefore', // ID
                __('Text Before', 'woo-or'), // Label
                array($this, 'textarea_render'), // Renderer
                'iwvor_system_tab', // Page
                'iwvor_system_section', // Section
                ['id' => 'textBefore', 'form' => 'iwvor_system'] // Arguments
        );
        add_settings_field(
                'textAfter', // ID
                __('Text After', 'woo-or'), // Label
                array($this, 'textarea_render'), // Renderer
                'iwvor_system_tab', // Page
                'iwvor_system_section', // Section
                ['id' => 'textAfter', 'form' => 'iwvor_system', 'extraHtml' => '<br/><small>{orderDate}: ' . __('Order Date', 'woo-or') . '<br/>{orderNumber}: ' . __('Order Number', 'woo-or') . '</small>'] // Arguments
        );
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print '<br/>';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function input_render($args) {
        echo '<input type="text" id="' . $args['id'] . '" name="' . $args['form'] . '[' . $args['id'] . ']" value="' . (isset($this->options[$args['id']]) ? esc_attr($this->options[$args['id']]) : '') . '" />';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function textarea_render($args) {
        echo '<textarea rows="4" id="' . $args['id'] . '" name="' . $args['form'] . '[' . $args['id'] . ']">' . (isset($this->options[$args['id']]) ? esc_attr($this->options[$args['id']]) : '') . '</textarea>' . (isset($args['extraHtml']) ? $args['extraHtml'] : '');
    }

}
