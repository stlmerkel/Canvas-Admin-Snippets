<?php
/* ----------------------------------------------------------------------------------------------------------
	COPY CONTENT SCRIPT
	From https://github.com/stlmerkel/Canvas-Admin-Snippets
	
	WARNING: ALWAYS TEST IN THE BETA ENVIRONMENT FIRST!

	You need a token generated from an account with full admin rights and copy it into the token variable inside the function below.

	Copy the addresses of your production site (and beta if you want to test) into the target arguments inside the function below.

	This function takes 3 arguments
	1. destination_course = the course_id of the empty shell or wherever the content is going to
	2. source_course = the course_id of the course where the content resides
	3. target = either 'production' or 'beta' depending on which system you're targeting

	NOTE: The function will take the Canvas ID of the courses as is, if you are using the sis_course_id for the target and/or source, you must prepend them with 'sis_course_id:'. See code example below.

	The code outside of the function is for reference/exmple of how the function may be called.
---------------------------------------------------------------------------------------------------------- */

$destination_course="course_id";
$source_course="course_id";
$target="beta or production";

/// USE only 1 of the next two lines.
$copy_results=apply_content("sis_course_id:".$destination_course,"sis_course_id:".$source_course,$target); /// for sis_course_ids
$copy_results=apply_content($destination_course,$source_course,$target); /// for Canvas course ids

echo copy_results; // This will print the results of the function call.

//

function copy_content($destination_course,$source_course,$target) {

	$token="#### Admin token generated from an account that has full admin rights ####";

	if ($target=="production") { $url="https://wherever.instructure.com";	}
	if ($target=="beta") { $url="https://wherever.beta.instructure.com";	}


	$html_post="settings[source_course_id]=".$source_course."&migration_type=course_copy_importer";

	$ch = curl_init();
		$canvas_write_results="";

		curl_setopt($ch, CURLOPT_URL, $url."/api/v1/courses/".$destination_course."/content_migrations");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$token));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $html_post);
		curl_setopt($ch, CURLOPT_POST, 1);

		$data = curl_exec($ch);
		if ($data === FALSE) {
			die(curl_error($ch));
		} else {
	 		$canvas_write_results.= $data;
		}
	curl_close($ch);

	//// now the JSON response from this can be parsed to get the actual canvas_id of the course. 

	$canvas_info=json_decode ($canvas_write_results ,true);

	return $canvas_info;
} // copy content function

?>