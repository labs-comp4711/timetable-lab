<?php

/**
 * This is a model for the control data in our burger ordering
 *
 * @author jim
 */
class TimeSchedule extends CI_Model {

    protected $xml = null;
    protected $daysofweek = array();
    protected $timeslots = array();
    protected $cheeses = array();
    protected $toppings = array();
    protected $sauces = array();

    // Constructor
    public function __construct() {
        parent::__construct();
        $this->xml = simplexml_load_file(DATAPATH . 'timetable.xml');

/*
        // build the list of patties - approach 1
        foreach ($this->xml->patties->patty as $patty) {
            $this->patty_names[(string) $patty['code']] = (string) $patty;
        }
*/

        foreach ($this->xml->days->day as $day) {
            foreach ($day->dayEntry as $Entry) {
                $element = array();
                $element['day'] = (string) $day['name'];
                //$element['time'] = (string) $Entry['time'];
                $element['bookingroom'] = (string) $Entry->bookingroom;
                $element['start']=(string) $Entry->time['start'];
                $element['code']=(string) $Entry->course['code'];
                $element['instructor'] = (string) $Entry->instructor;
                $element['type'] = (string) $Entry->type;

                $this->daysofweek[] = new Booking($element);
            }
        }

        // building as timeslots
        foreach ($this->xml->timeslots->slots as $slot){
            foreach ($slot->timeEntry as $Entry){
                $element = array();
                $element['start'] = (string) $slot['start'];
                $element['bookingroom'] = (string) $Entry->bookingroom;
                $element['day'] = (string) $Entry->day['name'];
                $element['code'] = (string) $Entry->course['code'];
                $element['instructor'] = (string) $Entry->instructor;
                $element['type'] = (string) $Entry->type;

                $this->timeslots[] = new Booking($element);
            }
        }

    }


    // retrieve a list of days, to populate a dropdown, for instance
    function getDays() {
        return $this->daysofweek;
    }

    // retrieve list of timeslots
    function getTimeslots(){
        return $this->timeslots;
    }
    
}
class Booking extends CI_Model {
    public $day;
    public $start;
    public $coursename;
    public $instructor;
    public $location;
    public $classtype;

    //Constructor for booking
    public function __construct($booking) {
        $this->day = (string) $booking['day'];
        $this->start = (string) $booking['start'];
        $this->coursename = (string) $booking['code'];
        $this->instructor = (string) $booking['instructor'];
        $this->location = (string) $booking['bookingroom'];
        $this->classtype = (string) $booking['type'];
    }
}