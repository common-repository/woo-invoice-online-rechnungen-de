<?php

/**
 * Online-Rechnungen Object Class Loader
 */
class IwvOrDocument {

    public $type = 'doc_invoice';
    public $date; // d.m.Y
    public $netto = 1;
    public $docName;
    public $textBefore;
    public $textAfter;

    /** @var IwvOrRecipient */
    public $recipient;

    /** @var IwvOrSender */
    public $sender;
    public $positions = [];

    public function __construct() {
        $this->sender = new IwvOrSender;
        $this->recipient = new IwvOrRecipient;
        # Extra
        $systemDb = (array) get_option('or_system');

        $this->textAfter = isset($systemDb['textAfter']) ? $systemDb['textAfter'] : '';
        $this->textBefore = isset($systemDb['textBefore']) ? $systemDb['textBefore'] : '';
        $prefix = isset($systemDb['prefix']) ? $systemDb['prefix'] : '';
        $suffix = isset($systemDb['suffix']) ? $systemDb['suffix'] : '';
        $number = isset($systemDb['increment']) ? $systemDb['increment'] : 1;
        $this->docName = $prefix . $number . $suffix;
    }

    public function loadOrder($orderId) {
        $orderDb = wc_get_order($orderId);
        if (!$orderDb) {
            return false;
        }
        $order = $orderDb->get_data();
        # Recipient
        $this->recipient->customerId = '';
        $this->recipient->firm = $order['billing']['company'];
        $this->recipient->firstName = $order['billing']['first_name'];
        $this->recipient->lastName = $order['billing']['last_name'];
        $this->recipient->address = $order['billing']['address_1'];
        $this->recipient->addressExtra = $order['billing']['address_2'];
        $this->recipient->postal = $order['billing']['postcode'];
        $this->recipient->phone = $order['billing']['phone'];
        $this->recipient->email = $order['billing']['email'];
        $this->recipient->city = $order['billing']['city'];
        $this->recipient->countryIso = $order['billing']['country'];
        # Positions
        foreach ($order['line_items'] as $amPos) {
            $pos = new stdClass();
            $pos->unit = '';
            $pos->description = $amPos['name'];
            $pos->quantity = $amPos['quantity'];
            $pos->tax = abs(intval(round((100 * $amPos['subtotal_tax']) / $amPos['subtotal'])));
            $price = round(round($amPos['subtotal'], 2) / $pos->quantity, 2, PHP_ROUND_HALF_DOWN);
            $pos->price = $price;
            $pos->price = round($price, 2);
            $this->positions[] = $pos;
        }
        # Shipping
        if (floatval($order['shipping_total']) > 0) {
            $pos = new stdClass();
            $pos->description = __('Shipping Costs', 'woo-or');
            $pos->quantity = 1;
            $pos->tax = abs(intval(round($order['shipping_tax'] * 100 / $order['shipping_total'])));
            $pos->price = round($order['shipping_total'], 2);
            $this->positions[] = $pos;
        }
        # Discount
        if (floatval($order['discount_total']) > 0) {
            $pos = new stdClass();
            $pos->description = __('Discount', 'woo-or');
            $pos->quantity = 1;
            $pos->tax = abs(intval(round($order['discount_tax'] * 100 / $order['discount_total'])));
            $pos->price = -(round($order['discount_total'], 2) );
            $this->positions[] = $pos;
        }
        # Extras
        $search = [
            '{orderDate}' => $order['date_created']->date_i18n(),
            '{orderNumber}' => $order['id'],
        ];
        $this->textAfter = str_replace(array_keys($search), $search, $this->textAfter);
        return true;
    }

    public function createDocument() {

        return $this->_makePost('https://www.online-rechnungen.de/controller.php?cname=MyDocuments&do=getActiveDocumentPreview&tkn=47783', [
                    'sessionDocument' => $this
        ]);
    }

    /** Make POST request * */
    protected function _makePost($url, $data = []) {
        $content = http_build_query($data, '', '&');
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($content)
        );
        $options = array(
            'http' => array(
                'method' => 'POST',
                'content' => $content,
                'header' => implode("\r\n", $header)
            )
        );
        return file_get_contents($url, false, stream_context_create($options));
    }

    protected function _addTax($price, $tax) {
        return floatval($price) * ((intval($tax) / 100) + 1);
    }

}
