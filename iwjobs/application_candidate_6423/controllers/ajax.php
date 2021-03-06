<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    /*
     * this function is used to fetch messages for client side/ajax validation.
     */

    public function fetch_message() {
        if ($this->input->is_ajax_request() == TRUE) {

        //--- start loading model ---//
        //$CI =& get_instance();
            $this->load->model('All_functiondb','',TRUE);
            require_once(APPPATH.'php_include/common_header.php');

            //--- end loading model ---//
            // Collect @parameter_string...
            $parameter_string = $this->input->post('parameter_string');


            // Separating each message index...
            $message_index_arr = preg_split("/[~]+/", $parameter_string);



            // Counting number of index...
            $last_index = count($message_index_arr);


            // If number of index==1...
            if ($last_index == 0) {
                $message_index_arr[0] = $parameter_string;
            }
            // else when number of index>1...
            else {
            // Initializing the loop counter...
                $counter = 1;

                // Initializing the variable which will return to ajax...
                $message_value = "";


                // Within the loop, we collect message corresponding to each index...
                foreach ($message_index_arr as $message_index) {
                // This is the actual message index as in DB...
                    $message_index_in_db = "msg_" . str_replace("-", "_", $message_index);


                    /* --- start fetching message from DB corresponding to actual index --- */
                    $result_array = $this->All_functiondb->fetch_message($message_index_in_db)->result_array();

                    $message_value .= $result_array[0]['Content'];

                    /* --- end fetching message from DB corresponding to actual index --- */


                    // Separating each message by a separator...
                    $message_value .= $counter != $last_index ? "##@@##" : "";


                    // The counter increases by 1...
                    $counter++;
                }
            }

            echo $message_value;
            exit();
        } else {
            redirect(base_url());
        }
    }

    /*
     * this function is used to check signup availability by username / email.
     */

    public function check_signup_availability() {
        if ($this->input->is_ajax_request() == TRUE) {

        //--- start loading model ---//
        //$CI =& get_instance();
            $this->load->model('Userdb', '', TRUE);

            //--- end loading model ---//
            // Collect @parameter_string...
            $to_check = $this->input->post('to_check');
            $val = $this->input->post('val');

            // If var @to_check == 'username', check for username existence...
            if ($to_check == 'username') {

                $rec_num = $this->Userdb->username_exist($val);
            }
            // If var @to_check == 'email', check for email existence...
            else {
                $rec_num = $this->Userdb->email_exist($val);
            }

            echo $rec_num;
            exit();
        } else {
            redirect(base_url());
        }
    }

    public function get_municipality()
    {
        if ($this->input->is_ajax_request() == TRUE) {

            $province_id = $this->input->post('province_id');

            $this->load->model('Locationdb','',TRUE);
            $municipality_arr=$this->Locationdb->get_municipality($province_id);

            $val='<select class="contrydropdown" name="municapility" id="municapility" onchange="get_postalcode(this.value);">
                    <option value="">'.$this->all_function->get_label('l_slmunicipality').'</option>';
            foreach($municipality_arr as $v):
             $val.='<option value="'.$v['MunicipalityId'].'">'.$v['MunicipalityName'].'</option>';
            endforeach;

            $val.='</select>';

            echo $val;
            exit;
        }
        else {
            redirect(base_url());
        }
    }

    public function get_postalcode()
    {
        if ($this->input->is_ajax_request() == TRUE) {

                $municipality_id = $this->input->post('municipality_id');

            $this->load->model('Locationdb','',TRUE);
            $postalcode_arr=$this->Locationdb->get_postalcode($municipality_id);

            $val='<select class="contrydropdown" name="postalcode" id="postalcode" >
                    <option value="">'.$this->all_function->get_label('l_slpostalcode').'</option>';
            foreach($postalcode_arr as $v):
             $val.='<option value="'.$v['PostalId'].'">'.$v['PostalCode'].'</option>';
            endforeach;

            $val.='</select>';

            echo $val;
            exit;
        }
        else {
            redirect(base_url());
        }
    }
    public function get_key_skill()
    {
        if ($this->input->is_ajax_request() == TRUE) {
            
            // Collect @parameter_string...
            $param = trim(strip_tags($this->input->get("term")));
            if($param!='')
            {
                $skill_arr=$this->all_function->get_key_skills($param);
                $count=count($skill_arr);
                $result_arr=array();
                $i=0;
                while($i<$count)
                {
                    $row_array['Id']=$skill_arr[$i]['SkillId'];
                    $row_array['Name']=$skill_arr[$i]['SkillName'];
                    array_push($result_arr, $row_array);
                    $i++;
                }
            }
            else
            {
                $result_arr=array();
            }
            //echo JSON to page
	$response = $this->input->get("callback") . "(" . json_encode($result_arr) . ")";
	echo $response;
        }
        else {
            redirect(base_url());
        }
    }

}