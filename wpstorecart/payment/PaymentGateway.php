<?php

/**
 * Payment Gateway
 *
 * This library provides generic payment gateway handling functionlity
 * to the other payment gateway classes in an uniform way. Please have
 * a look on them for the implementation details.
 *
 * @package     Payment Gateway
 * @category    Library
 * @author      Md Emran Hasan <phpfour@gmail.com>
 * @link        http://www.phpfour.com
 */

if(!class_exists('PaymentGateway')) {
    abstract class PaymentGateway {
        /**
        * Holds the last error encountered
        *
        * @var string
        */
        public $lastError;

        /**
        * Do we need to log IPN results ?
        *
        * @var boolean
        */
        public $logIpn;

        /**
        * File to log IPN results
        *
        * @var string
        */
        public $ipnLogFile;

        /**
        * Payment gateway IPN response
        *
        * @var string
        */
        public $ipnResponse;

        /**
        * Are we in test mode ?
        *
        * @var boolean
        */
        public $testMode;

        /**
        * Field array to submit to gateway
        *
        * @var array
        */
        public $fields = array();

        /**
        * IPN post values as array
        *
        * @var array
        */
        public $ipnData = array();

        /**
        * Payment gateway URL
        *
        * @var string
        */
        public $gatewayUrl;

        /**
        * Initialization constructor
        *
        * @param none
        * @return void
        */
        public function __construct()
        {
            // Some default values of the class
            $this->lastError = '';
            $this->logIpn = TRUE;
            $this->ipnResponse = '';
            $this->testMode = FALSE;
        }

        /**
        * Adds a key=>value pair to the fields array
        *
        * @param string key of field
        * @param string value of field
        * @return
        */
        public function addField($field, $value)
        {
            $this->fields["$field"] = $value;
        }

        /**
        * Submit Payment Request
        *
        * Generates a form with hidden elements from the fields array
        * and submits it to the payment gateway URL. The user is presented
        * a redirecting message along with a button to click.
        *
        * @param none
        * @return void
        */
        public function submitPayment()
        {

            $this->prepareSubmit();

            echo "<html>\n";
            echo "<head><title>".__('Processing Payment...','wpstorecart')."</title></head>\n";
            echo "<body onload=\"document.forms['gateway_form'].submit();\" style=\"background-color:#eeeeee;\">\n";
            
            

            echo '<center><img src="'.plugins_url().'/wpstorecart/images/loader2.gif" alt="redirecting" />';
            echo "<p style=\"text-align:center;\"><h2>".__('Please wait, your order is being processed and you will be redirected to the payment website.','wpstorecart');
            
            echo "</h2></p>\n";
            

            
            echo "<form method=\"POST\" id=\"gateway_form\" name=\"gateway_form\" ";
            echo "action=\"" . $this->gatewayUrl . "\">\n";

            foreach ($this->fields as $name => $value)
            {
                echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
            }


            echo "<p style=\"text-align:center;\"><br/><br/>".__('If you are not automatically redirected to the payment website within 5 seconds...', 'wpstorecart');
            
            

            echo "<input type=\"submit\" value=\"".__('Click Here','wpstorecart')."\" /></p>\n";
                       
            

            echo "</form>\n";
            echo "</center></body></html>\n";
        }

        /**
        * Echoes the current values of all the fields.  Useful for debugging, added in wpStoreCart 2.2.9
        */
        public function echoFields() {
            echo '<table><thead><tr><th>'.__('Field','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>';
            foreach ($this->fields as $name => $value) {
                echo "<tr><td>$name</td><td>$value</td></tr>";
            }
            echo '</tbody></table>';
        }


        /**
        * Perform any pre-posting actions
        *
        * @param none
        * @return none
        */
        protected function prepareSubmit()
        {
            // Fill if needed
        }

        /**
        * Enables the test mode
        *
        * @param none
        * @return none
        */
        abstract protected function enableTestMode();

        /**
        * Validate the IPN notification
        *
        * @param none
        * @return boolean
        */
        abstract protected function validateIpn();

        /**
        * Logs the IPN results
        *
        * @param boolean IPN result
        * @return void
        */
        public function logResults($success)
        {

            if (!$this->logIpn) return;

            // Timestamp
            $text = '[' . date('m/d/Y g:i A').'] - ';

            // Success or failure being logged?
            $text .= ($success) ? __('SUCCESS!','wpstorecart')."\n" : __('FAIL: ','wpstorecart') . $this->lastError . "\n";

            // Log the POST variables
            $text .= __('IPN POST Vars from gateway:','wpstorecart')."\n";
            foreach ($this->ipnData as $key=>$value)
            {
                $text .= "$key=$value, ";
            }

            // Log the response from the paypal server
            $text .= "\n".__('IPN Response from gateway Server:','wpstorecart')."\n " . $this->ipnResponse;

            // Write to log
            $fp = fopen($this->ipnLogFile,'a');
            fwrite($fp, $text . "\n\n");
            fclose($fp);
        }
    }
}