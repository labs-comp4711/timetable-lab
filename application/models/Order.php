<?php

/**
 * This is a model for a single order, stored in an XML document.
 *
 * @author jim
 */
class Order extends CI_Model {

    protected $xml = null;
    protected $customer = '';
    protected $delivery = null; // optional
    protected $special = null;  // optional
    protected $ordertype = '';
    protected $burgers = array();

    // Constructor
    public function __construct($filename = null) {
        parent::__construct();
        if ($filename == null)
            return;

        $this->xml = simplexml_load_file(DATAPATH . $filename . XMLSUFFIX);

        // extract basics
        $this->customer = (string) $this->xml->customer;
        $this->delivery = (isset($this->xml->delivery)) ? (string) $this->xml->delivery : null;
        $this->special = (isset($this->xml->special)) ? (string) $this->xml->special : null;
        $this->ordertype = (string) $this->xml['type'];

        foreach ($this->xml->burger as $one) {
            $this->burgers[] = $this->cookem($one);
        }
    }

    // build a burger object from the simpleXML
    // use the DTD as a guide ... (patty, cheeses?, topping*, sauce*, instructions?, name?)
    function cookem($element) {
        $record = new stdClass();
        $record->patty = (string) $element->patty['type'];
        $record->top = (isset($element->cheeses)) ? (string) $element->cheeses['top'] : null;
        $record->bottom = (isset($element->cheeses)) ? (string) $element->cheeses['bottom'] : null;
        $record->instructions = (isset($element->instructions)) ? (string) $element->instructions : null;
        $record->name = (isset($element->name)) ? (string) $element->name : null;

        // build our toppings etc
        $record->toppings = array();
        foreach ($element->topping as $one)
            $record->toppings[] = (string) $one['type'];
        $record->sauces = array();
        foreach ($element->sauce as $one)
            $record->sauces[] = (string) $one['type'];

        return $record;
    }

    // return the customer name
    function getCustomer() {
        return $this->customer;
    }

    // return delivery instructions
    function getDelivery() {
        return $this->delivery;
    }

    // return any special notes
    function getSpecial() {
        return $this->special;
    }

    // return the order type
    function getType() {
        return $this->ordertype;
    }

    // return the array of burgers in this order
    function getBurgers() {
        return $this->burgers;
    }

}
