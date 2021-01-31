<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/pve2-api-php-client/pve2_api.class.php';
require_once __DIR__ . '/lib/bbProx-Func.php';

 

function bbproxmox_MetaData()
{
    return array(
        'DisplayName' => 'Proxmox Provisioning Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '8006', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '8006', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

function bbproxmox_ConfigOptions()
{
    return array(
        // a text field type allows for single line text input
        'SYS IMG Name' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter in name of system image to install',
        ),
        'HDD Space' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter in megabytes',
        ),
		'RAM SIZE' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter in megabytes',
        ),
		'SWAP SIZE' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter in megabytes',
        ),
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_CreateAccount(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
        // )
        // ```
		print_r($params);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return $params;
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_SuspendAccount(array $params)
{
    try {
        // Call the service's suspend function, using the values provided by
        // WHMCS in `$params`.
	bbproxmox_Stop($params);
	//TODO add code here for users account...
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'error';
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_UnsuspendAccount(array $params)
{
    try {
        // Call the service's unsuspend function, using the values provided by
        // WHMCS in `$params`.

	//TODO add code here for users account...

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'error';
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_TerminateAccount(array $params)
{
    try {
        // Call the service's terminate function, using the values provided by
        // WHMCS in `$params`.

	 bbproxmox_Stop($params); //i personly dont trust the server to remove things so lets just make sure to  stop the vps for now.
        //TODO add code here for users account...

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'error';
}

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_ChangePassword(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'error';
}

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function bbproxmox_ChangePackage(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
	 logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
	$null
);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'error';
}

// Done
function bbproxmox_TestConnection(array $params)
{
	$serverip = $params['serverip'];
	$serverusername = $params['serverusername'];
	$serverpassword = $params['serverpassword'];
	try {
        	# You can try/catch exception handle the constructor here if you want.
		$pve2 = new PVE2_API($serverip, $serverusername, "pam", $serverpassword);
		# realm above can be pve, pam or any other realm available.
		if ($pve2->login()) {
			$success = true;
			$errorMsg = '';
		} else {
			$success = false;
			$errorMsg = 'Login to Proxmox Host failed. (Bad host/user/pass?)';
		}
	} catch (Exception $e) {
        	logModuleCall(
            	'bbproxmoxmodule',
            	__FUNCTION__,
            	$params,
            	$e->getMessage(),
            	$e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
    	}
    	return array(
        	'success' => $success,
        	'error' => $errorMsg,
    	);
}

//Done
function bbproxmox_AdminCustomButtonArray()
{
    return array(
	 "Start Server" => "Start",
	 "Stop Server" => "Stop",
	 "Shutdown Server" => "Shutdown",
	 "Suspend Server" => "Suspend",
	 "Resume Server" => "Resume",
	 "Restart Server" => "Restart",
	 "Get VPS Settings" => "GetSettings",
	 "Send VPS Settings" => "SendSettings",
    );
}

//Done
function bbproxmox_Start(array $params)
{
	$customfields = $params["customfields"];
	$DocmdR = array();
	$DocmdR["serverip"] = $params["serverip"];
	$DocmdR["realm"] = "pam";
	$DocmdR["serverusername"] = $params["serverusername"];
	$DocmdR["serverpassword"] = $params["serverpassword"];
	$DocmdR['vmid'] = $customfields['vmid'];
	$DocmdR['vmtype'] = $customfields['vmtype'];
	$DocmdR['state'] = "start";
	$docmd = bbproxmox_ChState($DocmdR);
	return $docmd;
	return "ERROR end of line?";
};

//Done
function bbproxmox_Stop(array $params)
{
        $customfields = $params["customfields"];
        $DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "stop";
        $docmd = bbproxmox_ChState($DocmdR);
        return $docmd;
        return "ERROR end of line?";
};

//Done
function bbproxmox_Shutdown(array $params)
{
        $customfields = $params["customfields"];
        $DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "shutdown";
        $docmd = bbproxmox_ChState($DocmdR);
        return $docmd;
        return "ERROR end of line?";
};

//Done
function bbproxmox_Suspend(array $params)
{
        $customfields = $params["customfields"];
        $DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "suspend";
        $docmd = bbproxmox_ChState($DocmdR);
        return $docmd;
        return "ERROR end of line?";
};

//Done
function bbproxmox_Resume(array $params)
{
        $customfields = $params["customfields"];
	$DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "resume";
        $docmd = bbproxmox_ChState($DocmdR);
        return $docmd;
        return "ERROR end of line?";
};

//Done
function bbproxmox_Restart(array $params)
{
        $customfields = $params["customfields"];
	$DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "stop";
        $docmd = bbproxmox_ChState($DocmdR);
	sleep(5);
        $DocmdR['state'] = "start";
        $docmd = bbproxmox_ChState($DocmdR);
        return $docmd;
        return "ERROR end of line?";
};










/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function bbproxmox_ClientAreaCustomButtonArray()
{
    return array(
         "Start Server" => "Start",
         "Stop Server" => "Stop",
         "Shutdown Server" => "Shutdown",
//         "Suspend Server" => "Suspend",
//         "Resume Server" => "Resume",
         "Restart Server" => "Restart",
    );
}

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see provisioningmodule_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function bbproxmox_AdminServicesTabFields(array $params)
{

	//gee so much to do here......... HELP!
    try {
        $customfields = $params["customfields"];
        $DocmdR = array();
        $DocmdR["serverip"] = $params["serverip"];
        $DocmdR["realm"] = "pam";
        $DocmdR["serverusername"] = $params["serverusername"];
        $DocmdR["serverpassword"] = $params["serverpassword"];
        $DocmdR['vmid'] = $customfields['vmid'];
        $DocmdR['vmtype'] = $customfields['vmtype'];
        $DocmdR['state'] = "current";
        $docmd = bbproxmox_ReadState($DocmdR);
	$arraydata = implode(',',$docmd);
	$VMname = $docmd['name'];
	error_log("----------------------------!!!!! $arraydata !!!!!!!!");
	 error_log("---------------------!---!-!- $VMname ");
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();

        // Return an array based on the function's response.
        return array(
            'Number of Apples' => $docmd['name'],
            'Number of Oranges' => (int) $response['numOranges'],
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'Something Editable' => '<input type="hidden" name="bbproxmoxmodule_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="bbproxmoxmodule_uniquefieldname"'
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see provisioningmodule_AdminServicesTabFields()
 */
function bbproxmox_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['bbproxmoxmodule_original_uniquefieldname'])
        ? $_REQUEST['bbproxmoxmodule_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['bbproxmoxmodule_uniquefieldname'])
        ? $_REQUEST['bbproxmoxmodule_uniquefieldname']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'bbproxmoxmodule',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function bbproxmox_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function bbproxmox_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function bbproxmox_ClientArea(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        $extraVariable1 = 'abc';
        $extraVariable2 = '123';

        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'extraVariable1' => $extraVariable1,
                'extraVariable2' => $extraVariable2,
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'bbproxmoxmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}
?>
