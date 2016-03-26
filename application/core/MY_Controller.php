<?php

/**
 * core/MY_Controller.php
 *
 * Default application controller
 *
 * @author		JLP
 * @copyright           2010-2013, James L. Parry
 * ------------------------------------------------------------------------
 */
class Application extends CI_Controller {

    protected $data = array();      // parameters for view components
    protected $id;                  // identifier for our content

    /**
     * Constructor.
     * Establish view parameters & load common helpers
     */

    function __construct() {
        parent::__construct();
        $this->data = array();
        $this->data['title'] = "Schedule Lab";    // our default title
        $this->errors = array();
        $this->data['pageTitle'] = 'welcome';   // our default page
        $this->data['search'] = $this->initDropdown();
    }

    /**
     * Render this page
     */
    function render() {
        $this->data['content'] = $this->parser->parse($this->data['pagebody'], $this->data, true);

        // finally, build the browser page!
        $this->parser->parse('_template', $this->data);
    }

    //create dropdown
    function initDropdown(){
        $data = array();
        $data['timeslotsDropdown'] = form_dropdown('timeslots', $this->timeschedule->getTimeslotForDropdown());
        $data['daysDropdown'] = form_dropdown('days', $this->timeschedule->getDayForDropdown());

//        $temp = $this->timeschedule->getTimeslotForDropdown();
//        var_dump($temp);

        return $this->parser->parse('search', $data, true);

    }

}

/* End of file MY_Controller.php */
/* Location: application/core/MY_Controller.php */