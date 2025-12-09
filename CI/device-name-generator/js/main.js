$(document).ready(function(){
    //add a change event to the organisation and device select elements
    $('#2_networkdevicetype_id,#2_org_id').on('change', function() {auto_complete_device_settings();generate_device_name(0);});
    
    //if any of these input ID's (device_asset_code,device_type,device_yearmonth) change their value clear the device_name text to empty
    $('#device_asset_code,#device_type,#device_yearmonth').on('change', function() {
        $('#device_name').val('');
        //if each item $('#device_asset_code,#device_type,#device_yearmonth') has a selected value or text value, then run the auto_complete_device_settings function
        if($('#device_asset_code').val() != "" && $('#device_type').val() != "" && $('#device_yearmonth').val() != ""){
            generate_device_name(0);
            return;
        }
    });
    
    //generate button clicked
    $('#device_generate_name').click(function(){generate_device_name(1);});

    function generate_device_name(showwarn=1){
        $('#device_name').val('');
        if($('#device_asset_code').val() == "" || $('#device_type').val() == "" || $('#device_yearmonth').val() == ""){
            if(showwarn){
                alert("Please fill out all fields before generating a device name.");
            }            
            return;
        }

        $('#device_generate_name').addClass('wait');
            var asset = $('#device_asset_code').val();
            var type = $('#device_type').val();
            var yearmonth = $('#device_yearmonth').val();
            //post asset,type,yearmonth, and set action to "generate_device_name"
            $.post(window.location.href, {device_asset_code: asset, device_type: type, device_yearmonth: yearmonth, action: "generate_device_name"}, function(data){
            $('#device_generate_name').removeClass('wait');                            
                    var response = JSON.parse(data);
                if(response.status == "success"){
                    $('#device_name').val(response.message);
                }else{
                    alert(response.message);
                }
            });
    }
    function auto_complete_device_settings(waittime=0){
        //add wait period for page to load
        if(waittime>0){setTimeout(function(){auto_complete_device_settings();},waittime);return;}

        let organisation_name = $('#2_org_id').find('option:selected').text();

        //check if organisation name is empty
        if(organisation_name != ""){
            //split the name by space
            let org_Text = organisation_name.split(" ");
            //check if first part of organisation name is a number and between these values
            //     Org ID between 10000 and 79999 = e //customers
            //     Org ID between 80000 and 89999 = p //providers
            //     Org ID between 90000 and 99999 = c //company
            let asset_code = $('#device_asset_code');
            if(org_Text[0] >= 10000 && org_Text[0] <= 79999){
                $(asset_code).val('e'); //enterprise
            }
            if(org_Text[0] >= 80000 && org_Text[0] <= 89999){
                $(asset_code).val('p'); //customer
            }
            if(org_Text[0] >= 90000 && org_Text[0] <= 99999){
                $(asset_code).val('c'); //provider
            }
        }



        //check if network device type is selected in properties
        let el_nt=$('#2_networkdevicetype_id');
        var networkTypeId = $(el_nt).val(); // Gets the selected value (e.g., "47")
        var networkTypeText = $(el_nt).find('option:selected').text(); // Gets the selected text (e.g., "Switch")

        //get select element for device_type
        let deviceType = $('#device_type');

        //loop through options and select the one that matches the networkTypeText
        $(deviceType).find('option').each(function(){
            if($(this).text() !="" && $(this).text() != "Select Device Type" && networkTypeId != "" && networkTypeText!=""){
                if($(this).text() == networkTypeText){$(deviceType).val($(this).val());return;}
                if($(this).text().includes(networkTypeText)){$(deviceType).val($(this).val());return;}
                if(networkTypeText.includes($(this).text())){$(deviceType).val($(this).val());return;}
            }
        });
    }

    auto_complete_device_settings(1000);//add delay to let page load
});