<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct() {
        parent::__construct();
        $this->load->model('menu');
        $this->load->model('order');
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index() {
        // Build a list of orders
        $this->load->helper('directory');
        $candidates = directory_map(DATAPATH);
        sort($candidates);
        foreach ($candidates as $file) {
            if (substr_compare($file, XMLSUFFIX, strlen($file) - strlen(XMLSUFFIX), strlen(XMLSUFFIX)) === 0)
            // exclude our menu
                if ($file != 'menu.xml')
                // trim the suffix
                    $orders[] = array('filename' => substr($file, 0, -4));
        }
        $this->data['orders'] = $orders;

        // Present the list to choose from
        $this->data['pagebody'] = 'homepage';
        $this->render();
    }

    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($filename) {
        // Build a receipt for the chosen order
        $order = new Order($filename);

        $this->data['filename'] = $filename;
        $this->data['customer'] = $order->getCustomer();
        $this->data['ordertype'] = $order->getType();

        // handle the burgers in an order
        $count = 1;
        $this->bigbucks = 0.0;

        $details = '';
        foreach ($order->getBurgers() as $burger)
            $details .= $this->burp($burger, $count++);

        // Present this burger
        $this->data['details'] = $details;
        $delivery = $order->getDelivery();
        $this->data['delivery'] = (isset($delivery)) ? 'Delivery: ' . $delivery : '';
        $special = $order->getSpecial();
        $this->data['special'] = (isset($special)) ? 'Special instructions: ' . $special() : '';
        $this->data['bigbucks'] = '$' . number_format($this->bigbucks, 2);

        $this->data['pagebody'] = 'justone';
        $this->render();
    }

    // present a receipt for a single burger
    function burp($burger, $count) {
        $bucks = 0.0; // price for this burger

        $parms['count'] = $count;
        $parms['name'] = (isset($burger->name)) ? $burger->name : '';
        $parms['instructions'] = (isset($burger->instructions)) ? '** Instructions ** ' . $burger->instructions : '';

        $patty = $this->menu->getPatty($burger->patty);
        $parms['patty'] = $patty->name;
        $bucks += $patty->price;

        // cheese?
        $cheesy = '';
        if (($burger->top == null) && ($burger->bottom == null))
            $cheesy = "None";
        if ($burger->top != null) {
            $slice = $this->menu->getCheese($burger->top);
            $cheesy = $slice->name . ' (top)';
            $bucks += $slice->price;
        }
        if ($burger->bottom != null) {
            if ($burger->top != null)
                $cheesy .= ' &amp; ';
            $slice = $this->menu->getCheese($burger->bottom);
            $cheesy .= $slice->name . ' (bottom)';
            $bucks == $slice->price;
        }
        $parms['cheesy'] = $cheesy;

        // toppings?
        $topper = '';
        if (count($burger->toppings) == 0)
            $topper = 'Plain as a doorknob';
        else
            foreach ($burger->toppings as $topping) {
                $stuff = $this->menu->getTopping($topping);
                if ($topper != '')
                    $topper .= ', ';
                $topper .= $stuff->name;
                $bucks += $stuff->price;
            }
        $parms['topper'] = $topper;

        // sauces?
        $squirt = '';
        if (count($burger->sauces) == 0)
            $squirt = 'Naked as the day the cow was born';
        else
            foreach ($burger->sauces as $sauce) {
                $blurp = $this->menu->getSauce($sauce);
                if ($squirt != '')
                    $squirt .= ', ';
                $squirt .= $blurp->name;
            }
        $parms['squirt'] = $squirt;

        // now for the burger total
        $parms['bucks'] = '$' . number_format($bucks, 2);
        $this->bigbucks += $bucks;

        return $this->parser->parse('aburger', $parms, true);
    }

}
