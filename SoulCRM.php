<?php
 
/**
 * SoulCRM API v2 wrapper
 * 
 * Requires curl
 * 
 * @author Shubham Arora <arorashubham@outlook.com>
 * @version 1.0
 *
 * TO-DO 
 * 1. Create only one curl session
 * 2. curl error handling
 * 3. Exception handling
 */
 
class SoulCRMWrapper {
         
        private $sessionToken;
        public $rawDataArgs, $leadDataArgs;
         
        public function collectData($args1, $args2) 
        {   
            $this->rawDataArgs = $args1;
            $this->leadDataArgs = $args2;
            return $this->authenticate();
        }
         
        private function authenticate() 
        {
     
            // Setting request Headers
            $headers = array(
                'AppSecretKey: SECRET_KEY_HERE', 
                'AppAccessKey: ACCESS_KEY_HERE'
            );
 
            // Setting curl Options
            $cOptions = array(
                CURLOPT_URL => 'AUTH_API_URL',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPGET => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10
            );
 
            // Initialize and execute curl session
            $cSession = curl_init();
            curl_setopt_array($cSession, $cOptions);
            $authResult = curl_exec($cSession);
            curl_close($cSession);
 
            // Handling Authentication API Response
            $authResultJsonObj = json_decode($authResult, true);
            if($authResultJsonObj['Status']=="success") {
                $this->sessionToken = $authResultJsonObj['Data']['SessionToken'];
                return $this->sendRawData();
            }
            else {
                return "Failed to Authenticate";
            }   
        }   
 
        private function sendRawData() 
        {
            
            $rawData = array(
                    'Title' => "1",
                    'Designation' => "1",
                    'TypeOfContact' => "1",
                    'KeyAccountManager' => "1",
                    'Timezone' => "5.5",
                    'Currency' => "1"
                );
                 
            $params = array_merge($rawData,$this->rawDataArgs);
             
            // Setting request Headers
            $headers = array(
                'AppSecretKey: SECRET_KEY_HERE', 
                'AppAccessKey: ACCESS_KEY_HERE',
                'SessionToken: '. $this->sessionToken,
				'Content-Type: application/json' 
            );
 
            // Setting curl Options
            $cOptions = array(
                CURLOPT_URL => 'RAW_CONTACT_API_URL',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10
            );
             
            // Initialize and execute curl session
            $cSession = curl_init();
            curl_setopt_array($cSession, $cOptions);
            $rawDataAPIResult = curl_exec($cSession);
            curl_close($cSession);
             
            // Handling RawContact API Response
            $rawDataAPIResultJsonObj = json_decode($rawDataAPIResult, true);
            if($rawDataAPIResultJsonObj['Status']=="success") {
                $contactId = $rawDataAPIResultJsonObj['Data']['ContactId'];
                return $this->createLead($contactId);
            }
            else {
                return "Failed to create Raw Contact";
            }
        }
 
        private function createLead($contactId) 
        {
            
            $leadData = array(
                    'ContactType' => "1",
                    'EmployeeId' => "1",
                    'Status' => "1",
                    'CreatedBy' => "1",
                    'AssignTo' => "1",
                    'ContactId' => $contactId,
                );
             
            $params = array_merge($leadData,$this->leadDataArgs);
             
            // Setting request Headers
            $headers = array(
                'AppSecretKey: SECRET_KEY_HERE', 
                'AppAccessKey: ACCESS_KEY_HERE',
                'SessionToken: '. $this->sessionToken,
				'Content-Type: application/json' 
            );
 
            // Setting curl Options
            $cOptions = array(
                CURLOPT_URL => 'LEAD_API_URL',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10
            );
             
            // Initialize and execute curl session
            $cSession = curl_init();
            curl_setopt_array($cSession, $cOptions);
            $createLeadAPIResult = curl_exec($cSession);
            curl_close($cSession);
             
            // Handling Lead API Response
            $createLeadResultJsonObj = json_decode($createLeadAPIResult, true);
            if($createLeadResultJsonObj['Status']=="success") {
                return "success";
            }
            else {
                return "Failed to create Lead";
            }
        }
}       
?>