<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
// func to create vm openvz/qemu/lxc etc
function bbproxmox_CreateVM(array $params)
{
	// well seems theres some diffrent types to deal with so lets start with that eh
	$serverip = $params["serverip"];
	$realm = $params["realm"];
    $serverusername = $params["serverusername"];
    $serverpassword = $params["serverpassword"];
    $vmid = $params['vmid'];
	$vmtype = $params['vmtype'];
	$state = $params['state'];
    if (!$serverip) { return "Error No server ip given to connect to server to issuse command!..."; }
	if (!$serverusername) { return "Error No server username given to connect to server to issuse command!..."; }
	if (!$serverpassword) { return "Error No server password given to connect to server to issuse command!..."; }

    if (!$vmtype) { return "Error no vmtype given!..."; }
	if (!$state) { return "Error state command not given"; }

	
		try {
			$pve2 = new PVE2_API($serverip, $serverusername, $realm, $serverpassword);
			if ($pve2->login()) {
				if (!$vmid) { 
				//return "Error No customer vps VM number given!...";
				//hmm if the vm id is blank lets just find out what the next free id from the cluster mon 
				$vmid = $pve2->get_next_vmid;
				print($vmid);
				}
				# Get servers node name.
				$this_nodes1 = $pve2->get("/nodes");
				$this_nodes2 = $this_nodes1['0'];
				$this_node = $this_nodes2['node'];
			if ($vmtype == "openvz") {
				# Create a VZ container on the server node in the cluster.
				// todo add input processing here
				$new_container_settings = array();
				$new_container_settings['ostemplate'] = "local:vztmpl/debian-6.0-standard_6.0-4_amd64.tar.gz";
				$new_container_settings['vmid'] = $vmid;
				$new_container_settings['cpus'] = "2";
				$new_container_settings['description'] = "Test VM using Proxmox 2.0 API";
				$new_container_settings['disk'] = "8";
				$new_container_settings['hostname'] = "testapi.domain.tld";
				$new_container_settings['memory'] = "1024";
				$new_container_settings['nameserver'] = "4.2.2.1";
	
		
				print_r($pve2->post("/nodes/".$this_node."/openvz", $new_container_settings));
			}
	
			if ($vmtype == "lxc") {

					# Create a VZ container on the server node in the cluster.
				// todo add input processing here
				$new_container_settings = array();
				$new_container_settings['ostemplate'] = "local:vztmpl/debian-6.0-standard_6.0-4_amd64.tar.gz";
				$new_container_settings['vmid'] = $vmid;
				//$new_container_settings['cpulimit'] = "2";
				$new_container_settings['description'] = "Test VM using Proxmox 2.0 API";
				$new_container_settings['rootfs'] = "8";
				$new_container_settings['onboot'] = "1";
				$new_container_settings['hostname'] = "testapi.domain.tld";
				$new_container_settings['memory'] = "1024";
				$new_container_settings['swap'] = "1024";
				//$new_container_settings['nameserver'] = "4.2.2.1";
	
		
				print_r($pve2->post("/nodes/".$this_node."/lxc", $new_container_settings));
			};
			if ($vmtype == "qemu") {};
			} else {
				print("Login to Proxmox Host failed.\n");
				exit;
			}	
		} catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
		return $errorMsg;
    }
	return "ERROR end of line?";

}
// func to change state of a vps eg start stop etc
function bbproxmox_ChState(array $params)
{
//	$arraydata = implode(',',$params);
//	error_log("----------------------------!!!!! $arraydata !!!!!!!!");
	$serverip = $params["serverip"];
	$realm = $params["realm"];
	$serverusername = $params["serverusername"];
	$serverpassword = $params["serverpassword"];
	$vmid = $params['vmid'];
	$vmtype = $params['vmtype'];
	$state = $params['state'];
    	if (!$serverip) { return "Error No server ip given to connect to server to issuse command!..."; }
	if (!$serverusername) { return "Error No server username given to connect to server to issuse command!..."; }
	if (!$serverpassword) { return "Error No server password given to connect to server to issuse command!..."; }
  	if (!$vmid) { return "Error No customer vps VM number given!..."; }
   	if (!$vmtype) { return "Error no vmtype given!..."; }
	if (!$state) { return "Error state command not given"; }
    	try {
        	$pve2 = new PVE2_API($serverip, $serverusername, $realm, $serverpassword);
		if ($pve2->login()) {
			$this_nodes1 = $pve2->get("/nodes");
            		$this_nodes2 = $this_nodes1['0'];
         		$this_node = $this_nodes2['node'];

			$responce = $pve2->post("/nodes/".$this_node."/".$vmtype."/".$vmid."/status/".$state,$null);

			//  UPID:server5:00000B99:04D3599B:56F880D5:vzstart:1001:root@pam:
			if ($responce) { return 'success'; }
			else { return "ERROR blank response bad vmid or vm allready is"; }
		} else { return "Login to Proxmox Host failed. (Bad host/user/pass?)\n"; }
	} catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
		return $errorMsg;
    }
	return "ERROR end of line?";
};
?>
