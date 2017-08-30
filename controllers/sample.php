<?php

  class Sample extends CI_Controller{

    function __construct(){
  		parent::__construct();
  		$this->load->helper('form');
  		$this->load->model('MerchDash_model');
  	}

    //Function to send JSON data to the transaction chart
    //from the database
    //N.B. - This method has been developed using the data format
    //required by Google on their usage page. All attempts have been taken to
    //make this method of sending data from the database as dynamic as possible
    public function liveTrxChart(){

      //$data is populated with an array containing the "amount" and timestamp for every transaction
      //The timestamp data is sent to this controller, with the hours, minutes and seconds in separate
      //variables
  		$data = $this->MerchDash_model->liveTrx();

  		// echo $data['paid'][0];
      //Basic outline of required data format defined here
      $dataTable = array(
        'cols' => array(
          array(
            "id" => 'A',
            "label" => 'Time',
            "type" => 'timeofday'
          ),
          array(
            "id" => 'B',
            "label" => 'Transaction value',
            "type" => 'number'
          )
        ),
        'rows' => array()
      );

      $i = 0;
      //Data from the model (i.e. the amount paid and timestamp) are later pushed to
      //the array
      foreach($data['paid'] as $item){

        $dataTable['rows'][$i] = array(
          "c" => array(
            array(
              "v" => array($data['hour'][$i], $data['min'][$i], $data['sec'][$i]),
              "f" => null
            ),
            array(
              "v" => $item,
              "f" => null
            )
          )
        );
        $i++;
      }

      //Array is encoded in JSON and echoed to the ajax call
      echo json_encode($dataTable);

  	}

    //Function send the start time to the tick generator
    //N.B. The time sent here is an hour prior to the current time, done
    //intentionally to be able to show all transactions within the past hour 
    public function systemStartTime(){

      date_default_timezone_set('Asia/Dhaka');
      $tStamp = strtotime("-1 hour");

      $hours = date("H", $tStamp);
      $hours = intval($hours);
      $minutes = date("i", $tStamp);

      $rounded = $minutes - ($minutes % 10);

      echo json_encode(array(
        'minutes' => $rounded,
        'hours' => $hours
      ));
    }

  }

 ?>
