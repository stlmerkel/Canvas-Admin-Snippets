<?php
############################################################################
#	UPDATE COURSE SCRIPT
#	You need a token generated from an account with full admin rights and copy it into the token variable inside the function below.
#
#	Copy the addresses of your production site (and beta if you want to test) into the target arguments inside the function below.
#
#	Grab a list of courses you need to publish. The easiest way is probably to just pull a provisioning report 
#	(http://guides.instructure.com/m/4214/l/80121-what-reports-are-available-at-the-account-level), 
#	Report #4 in that example. 
#
#	Once you have those, single out the column called sis_course_id
#
#	Put those into an array called $sis_course_ids. The limit per API call is 500 so you need to limit the size of your array accordingly.
#	
#	Call the function and pass the array, the event, and either the word "production" or "beta" depending on where you want to publish.
#	 
#		Events can be (per the API documentation)
#			'offer' makes a course visible to students. This action is also called "publish" on the web site.
#
#			'conclude' prevents future enrollments and makes a course read-only for all participants. The course still appears in prior-enrollment lists.
#
#			'delete' completely removes the course from the web site (including course menus and prior-enrollment lists). All enrollments are deleted. Course 
#			content may be physically deleted at a future date.
#
#			'undelete' attempts to recover a course that has been deleted. (Recovery is not guaranteed; please conclude rather than delete a course if there is 
#			any ossibility the course will be used again.) The recovered course will be unpublished. Deleted enrollments will not be recovered.
#
#	The code outside of the function is for reference.
############################################################################


$sis_course_ids=Array("course_1","course_2","course_500"); /// up to 500 sis_course_ids

$update_results=update_course($sis_course_ids,"publish","beta");

echo update_results; // This will print the results of the function call.


function update_course($sis_course_ids,$event,$target) {

	$token="#### Admin token generated from an account that has full admin rights ####";

	if ($target=="production") { $url="https://wherever.instructure.com";	}
	if ($target=="beta") { $url="https://wherever.beta.instructure.com";	}

	//Expecting $sis_course_ids to be an array, even if there is only one course

	$bulk_list="";
	foreach($sis_course_ids as $scid) {
		$bulk_list.="&course_ids[]=sis_course_id:".$scid;
	}

	$html_post="event=".$event.$bulk_list;


	$ch = curl_init();
		$canvas_write_results="";

		curl_setopt($ch, CURLOPT_URL, $url."/api/v1/accounts/1/courses");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$token));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $html_post);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

		$data = curl_exec($ch);
		if ($data === FALSE) {
			die(curl_error($ch));
		} else {
	 		$canvas_write_results.= $data;
		}
	curl_close($ch);

	$canvas_info=json_decode ($canvas_write_results ,true);

	return $canvas_info;
} // end course



?>