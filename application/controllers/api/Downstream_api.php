<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Downstream_api extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->database();
       $this->load->model('Tpl_history_model', 'tpl_history');
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_get($tpl_history_id = 0)
	{
	    $histResult = $this->tpl_history->getSingleRecordById($tpl_history_id);
	    //echo count($histResult);
	    //echo "<pre>";
	    //print_r($histResult);
	    //die('here');
	    if(count($histResult)==1){
	        if($histResult->submit_by_qa==1){
        	    $dsData = array();
        	    $dsData['caseId'] = $id;
        	    $jsonData = json_encode($dsData);
        	    $curl = curl_init();
        	    curl_setopt_array($curl, array(
        	        CURLOPT_URL => "http://127.0.0.1:8092/bank_statements_integration/submit-spreading-details",
        	        CURLOPT_RETURNTRANSFER => true,
        	        CURLOPT_FAILONERROR=> true,
        	        CURLOPT_ENCODING => "",
        	        CURLOPT_MAXREDIRS => 10,
        	        CURLOPT_TIMEOUT => 0,
        	        CURLOPT_FOLLOWLOCATION => true,
        	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	        CURLOPT_CUSTOMREQUEST => "POST",
        	        CURLOPT_POSTFIELDS =>"caseId=".$id,
        	        CURLOPT_HTTPHEADER => array(
        	            "Content-Type: application/x-www-form-urlencoded",
        	        ),
        	    ));
                
        	    $response = curl_exec($curl);
        	    //echo"<pre>";
        	    //print_r($response);
        	    if ($response === false){
        	        $response = curl_error($curl);
        	    }
        	    $api_obj = json_decode($response, true);
        	    if($api_obj['code']){
        	        $this->response([
        	            'status' => TRUE,
        	            'message' => "Data sent to downstream successfully.",
        	            'data' =>$api_obj
        	        ], REST_Controller::HTTP_OK);
        	    }else{
        	        $this->response([
        	            'status' => FALSE,
        	            'message' => "Failed.",
        	            'data' =>$response
        	        ], REST_Controller::HTTP_OK);
        	    }
	        }else{
	            $this->response([
	                'status' => FALSE,
	                'message' => "Case is not sumbitted to qa."
	            ], REST_Controller::HTTP_OK);
	        }
	    }else{
	        $this->response([
	            'status' => FALSE,
	            'message' => "CaseId is invalid."
	        ], REST_Controller::HTTP_OK);
	    }
	}
      
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    /*public function index_post()
    {
        $input = $this->input->post();
        $this->db->insert('items',$input);
     
        $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);
    }*/
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    /*public function index_post()
    {
        return true;
    }*/
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    /*public function index_delete($id)
    {
        $this->db->delete('items', array('id'=>$id));
       
        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }*/
    	
}