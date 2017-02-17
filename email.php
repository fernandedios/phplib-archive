<?php

/*======================================================================*\
    FD Email Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 10/04/2012
    Version: 1.02
\*======================================================================*/

include_once("string.php");

class FDEmail extends FDUtilString {

	/*======================================================================*\
    	Function: processEmail
    	Purpose: send a HTML Email
    	Notes:  $template = location + filename of the mail template
    			$mail = array of values{}
	    			$mail['email-to'] = receiver email address;
					$mail['email-subject'] = subject
					$mail['email-from'] = sender email address
					$mail['email-name_first'] = sender first name
					$mail['email-name_last'] = sender last name

					$mail['{key}'] = "value";
						- Array for message, see template_sample_email.php

				$admin = email address to send copy of the email msg
				$debug = 1 on 0 off, if on email will not be sent, 
						 just returned as a string

				returns 1 if sent, email details if debug
	\*======================================================================*/
	public function processEmail($template, $mail, $admin = '', $debug = 0) {

        $data = '';

		if(file_exists($template)) {
			$data = file_get_contents($template);
			if(is_array($mail)) {
				foreach($mail as $key => $value) {
					$data = str_replace($key, $value, $data);
				}
			}		
		}
		else {
			$this->showError("Can't find template file: " . $template, "die", __FILE__, __LINE__);
		}

		$headers = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$headers .= 'From: '. $mail['email-name_first'] . ' ' . $mail['email-name_last'] . '<' . $mail['email-from'] . '>';

		if($debug == 0){
			mail($mail['email-to'], $mail['email-subject'], $data, $headers);

			if($admin != ''){
				mail($admin, $mail['email-subject'], $data, $headers);
			}
			return 1;
		}
		else if($debug == 1){
			return $this->showMailDetails($headers, $data);
		}
	}

	/*======================================================================*\
    	Function: showMailDetails
    	Purpose:  returns email details as string
	\*======================================================================*/
	private function showMailDetails($headers, $data){
		return $headers . '<br /><br />' . $data;
	}

}


?>