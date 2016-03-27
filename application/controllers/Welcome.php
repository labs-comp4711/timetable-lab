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
        $this->load->model('TimeSchedule');
        //$this->load->model('order');
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index() {
        // Build a list of orders

        $this->data["daysofweek"] = $this->TimeSchedule->getDays();
//        $temp=$this->TimeSchedule->getDays();
//        var_dump($temp);

        $this->data['timeslots'] = $this->TimeSchedule->getTimeslots();

        $this->data['courses'] = $this->TimeSchedule->getCourses();

        $this->data['pagebody']='homepage';
        $this->render();
    }

    function search(){
        $this->data['pagebody']='searchresults';
        $timeslotSelected = $this->input->post('timeslots');
        $daySelected = $this->input->post('days');

        $timeslotArray = $this->TimeSchedule->getTimeslotForDropdown();
        $timeslotSelected = $timeslotArray[$timeslotSelected];

        $dayArray = $this->TimeSchedule->getDayForDropdown();
        $daySelected = $dayArray[$daySelected];

        $dayResults = $this->TimeSchedule->searchDays($timeslotSelected, $daySelected);
        $timeslotResults = $this->TimeSchedule->searchTimeslots($timeslotSelected, $daySelected);
        $courseResults = $this->TimeSchedule->searchCourses($timeslotSelected, $daySelected);


        if(count($dayResults) == 1 && count($timeslotResults) == 1 && count($courseResults) == 1){    
            if($dayResults==$timeslotResults && $dayResults==$courseResults && $courseResults==$timeslotResults){
                $this->data['dayinfo']=$dayResults;
                $this->data['timeinfo']=$timeslotResults;
                $this->data['courseinfo']=$courseResults;
                $this->render();
            } 
        } else //Bad Search result, load error page
        {
            $this->data['dayinfo']=$dayResults;
            $this->data['timeinfo']=$timeslotResults;
            $this->data['courseinfo']=$courseResults;
            $this->data['pagebody']='errorsearch';  
            $this->render();
        }
    }
}
