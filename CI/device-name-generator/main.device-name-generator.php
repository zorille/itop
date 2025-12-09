<?php

class AddNetworkDeviceTab implements iApplicationUIExtension
{
    private $assetCode=array(
        "e"=>"Customer",
        "p"=>"Provider",
        "c"=>"Company",
    );
    private $deviceCodes=array(
        "Equipment"=>array(
            "FWS"=>"Firewalls",
            "LPT"=>"Laptops",
            "MCV"=>"MediaConverter : mux actif, tranpondeur optique (OEO), etc…",
            "MUX"=>"multiplexeur ou demultiplexeur optique passif",
            "NAS"=>"NAS",
            "PRT"=>"Printers",
            "RTS"=>"Routers",
            "SAN"=>"SAN",
            "SCRN"=>"Screen",
            "SRV"=>"Servers",
            "SWT"=>"Switchs",
            "WIFI"=>"Wifi",
            "WDM"=>"wdm mux-demux réseau",
            "MS-RDWAxx"=>"Microsoft RDS RDWA (DMZ)",
            "MS-RDSHxx"=>"Microsoft RDS RDSH (APP)",
            "MS-RDCBxx"=>"Microsoft RDS RDCB (LB)",
            "PC"=>"Desktop",
            "PDL"=>"Point Of Delivery (Network/fiber port)",
        ),
        "BMS Equipment"=>array(
            "BMS"=>"Every BMS equipment",
            "AKCP"=>"akcp",
            "DIRIS"=>"Diris (Socomec)",
            "PDPM"=>"Pdpm (schneider et Vertiv)",
            "WAGO"=>"Wago"
        ),
        "CCTV"=>array(
            "CAM"=>"CCTV",
            "RECTV"=>"CCTV recorder"
        ),
        "Electricite"=>array(
            "UPS"=>"UPS"
        ),
        "Telephone"=>array(
            "IPBX"=>"ipbx",
            "PHONE"=>"Interphone IP"
        )
    );
    private $csstyle = <<<EOT
        <style>
            label.device_label{
                display: block;
                font-weight:600;
                margin-top:10px;
            }
            select.device_select{
                width: 100%;
                max-width: 200px;
            }
            input.device_input_text{
                width: 100%;
                max-width: 200px;
            }
            button.device_generate_name{
                margin-top: 10px;
                padding: 10px;
                background-color: #19571d;
                color: white;
                border: none;
                cursor: pointer;
            }
            button.device_generate_name.wait:after {
                position: absolute;
                right: -25px;
                content: "";
                width: 20px;
                height: 20px;
                background-image: url(/images/indicator.gif);
                background-repeat: no-repeat;
            }
            button.device_generate_name.wait{background-color: #ffAF50;position: relative;}

            select optgroup{font-weight:600;}
        </style>    
        EOT;

    /**
     * This method is called by the framework to display custom properties.
     * It adds a new tab in the NetworkDevice view.
     *
     * @param DBObject $oObject The object currently being displayed
     * @param WebPage $oPage The web page to insert content into
     * @param boolean $bEditMode Whether the object is being edited
     */
    public function OnDisplayProperties($oObject, WebPage $oPage, $bEditMode = false)
    {

        // Check if the object is an instance of the class you want to extend (NetworkDevice in this case)
        if ($oObject instanceof NetworkDevice && $bEditMode)
        {
            //ajax request for generating device name
            if(isset($_POST['action']) && $_POST['action'] == 'generate_device_name'){
                echo json_encode($this->generate_device_name());
                exit;
            }

            // Add a custom tab
            $oPage->SetCurrentTab('Generate Device Name');
            
            // Add custom content to this tab
            $oPage->add($this->csstyle);    
            $oPage->p('Generate a device name based on the device type.<br/>');
            $oPage->Add($this->generateSelect($this->assetCode,"device_asset_code","Asset Type"));
            $oPage->Add($this->generateSelect($this->deviceCodes,"device_type","Device Type"));
            $year = date("y");
            $month = date("m");
            $oPage->Add($this->generateTextInput($year.$month,"device_yearmonth","Year and Month YYMM"));
            $oPage->Add("<button type='button' class='device_generate_name' id='device_generate_name'>Generate Device Name</button>");
            $oPage->Add($this->generateTextInput("","device_name","Device Name"));  
            //$oPage->add_ready_script($this->javascript); //javascript to generate device name on button click

            $oPage->add_linked_script('/extensions/device-name-generator/js/main.min.js');//load javascript file js/main.js
        }

    }
    
    //Recursive function to check if key exists in array and its children
    private function array_key_exists_recursive($key, $array) {
            if (array_key_exists($key, $array)) {
                return true;
            }
            foreach ($array as $value) {
                if (is_array($value) && $this->array_key_exists_recursive($key, $value)) {
                    return true;
                }
            }
            return false;
    }

    //Generate device name using POST data and search the database for similar names to calculate last ID used
    private function generate_device_name(){
        $response = array("status" => "error", "message" => "Something went wrong");

        // check device_asset_code set
        $device_asset_code = $_POST['device_asset_code'] ?? '';
        if (!$device_asset_code || $device_asset_code == "" || !$this->array_key_exists_recursive($device_asset_code,$this->assetCode)) {
            $response["message"] = "Incorrect Device Asset";
            return $response;
        }

        // check device_type set
        $device_type = $_POST['device_type'] ?? '';
        if (!$device_type || $device_type == "" || !$this->array_key_exists_recursive($device_type,$this->deviceCodes)) {
            $response["message"] = "Incorrect Device Type";
            return $response;
        }

        // Validate year and month format (YYMM)
        $yearMonth = $_POST['device_yearmonth'] ?? '';

        if (!preg_match('/^\d{4}$/', $yearMonth)) {
            $response['message'] = 'Invalid Year and Month';
            return $response;
        }

        $year = substr($yearMonth, 0, 2);
        $month = substr($yearMonth, 2, 2);

        if (!checkdate($month, 1, "20$year")) {
            $response['message'] = 'Invalid Year and Month';
            return $response;
        }

        // concatenate the device name to search the database with
        $deviceName = $device_asset_code . $device_type . $year.$month;
        $deviceName = strtoupper($deviceName);
        
        try {
            //Search - 'NetworkDevice' and use LIKE with parameter variable
            $oSearch = DBObjectSearch::FromOQL("SELECT NetworkDevice WHERE name LIKE :name ");
            $oSearch->AllowAllData();

            //Prepare a set of parameters for the query
            $oSet = new DBObjectSet($oSearch, array(), array('name' => $deviceName.'%'));
            //Fetch results
            $highestIDNumber = 0; //initialize the highest number
            while ($oNetworkDevice = $oSet->Fetch()) {
                // Process each network device
                $sDeviceName = $oNetworkDevice->Get('name');
                //take sDevicename, using string left to count the $deviceName length, then extract the following four digits.
                $currentNumber = substr($sDeviceName,strlen($deviceName),4);
                //get the highest number
                if(is_numeric($currentNumber)){
                    if($currentNumber > $highestIDNumber){
                        $highestIDNumber = $currentNumber;
                    }   
                }
            }
            $newNumber= $highestIDNumber + 1; //increment the highest number
            $newNumber = str_pad($newNumber, 4, "0", STR_PAD_LEFT);//pad with 0 to make 4 digit


            $response["status"] = "success";
            $response["message"] = $deviceName.$newNumber;
        } finally {
            $oSearch->AllowAllData(false);
        }

        return $response;
    }

    //generates HTML select drop down
    public function generateSelect($options=array(),$name="Default Name",$label="Default Label"){
        //device code is label with no spaces
        $inputName = str_replace(" ","_",$name);
        $inputName = strtolower($inputName);

        //generate the select box
        $tmpDeviceSelect = "<div>";
        $tmpDeviceSelect .= "<label class='device_label' for=".$inputName.">$label</label>";

        $tmpDeviceSelect .= "<select class='device_select' name='".$inputName."' id='".$inputName."'>";
        $tmpDeviceSelect .= "<option value='' selected></option>";
        foreach ($options as $key => $value) {
            if(is_array($value)){
                //create opt group using key as label
                $tmpDeviceSelect .= "<optgroup label='$key'>";
                foreach ($value as $key2 => $value2) {
                    $tmpDeviceSelect .= "<option value='$key2'>$value2</option>";

                }
                $tmpDeviceSelect .= "</optgroup>";

            }else{
                $tmpDeviceSelect .= "<option value='$key'>$value</option>";
            }
        }

        
        $tmpDeviceSelect .= "</select>";
        $tmpDeviceSelect .= "</div>";
        return $tmpDeviceSelect;
    }

    //Generates HTML text input
    public function generateTextInput($value="Default Value",$name="Default_Name",$label="Default Label"){
        //input name 
        $inputName = str_replace(" ","_",$name);
        $inputName = strtolower($inputName);

        //generate the text box
        $tmpDeviceSelect = "<div>";
        $tmpDeviceSelect .= "<label class='device_label' for=".$inputName.">$label</label>";

        $tmpDeviceSelect .= "<input class='device_input_text' type='text' name=".$inputName." id='".$inputName."' value='$value'>";
        $tmpDeviceSelect .= "</div>";
        return $tmpDeviceSelect;
    }

    // Required empty methods for the iApplicationUIExtension interface
    public function OnDisplayRelations($oObject, WebPage $oPage, $bEditMode = false) {}
    public function OnDisplayObjectHeader($oObject, WebPage $oPage) {}
    public function OnFormSubmit($oObject, $sFormPrefix = '') {}
    public function OnFormCancel($oObject, $sFormPrefix = '') {}
    public function EnumUsedAttributes($oObject) { return array(); }
    public function GetIcon($oObject) { return ''; }
    public function GetHilightClass($object) { return HILIGHT_CLASS_NONE; }
    public function EnumAllowedActions($oObject) { return array(); }
}
