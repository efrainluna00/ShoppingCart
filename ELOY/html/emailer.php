<?php
// recipient configuration
	$tomail[0]="info@elmagodoneloy.com";
	$cc_tomail[0]="";
	$bcc_tomail[0]="";
	$tomail[1]="";
	$cc_tomail[1]="";
	$bcc_tomail[1]="";
	$tomail[2]="";
	$cc_tomail[2]="";
	$bcc_tomail[2]="";
// General Variables
	$check_referrer=0;
	$referring_domains="";
// Default Error and Success Page Variables
	$error_page_title="Error - Missed Fields";
	$error_page_text="Please use your browser's back button to return to the form and complete the required fields.";
	$thanks_page_title="Message Sent";
	$thanks_page_text="Thank you for your inquiry";

/////////////////////////////////////////////////////////////////////////
// Don't muck around past this line unless you know what you are doing //
/////////////////////////////////////////////////////////////////////////
ob_start();
$required_fields=$_POST["required_fields"];
$required_email_fields=$_POST["required_email_fields"];
$recipients=$_POST["recipient_group"];
$error_page=$_POST["error_page"];
$thanks_page=$_POST["thanks_page"];
$send_copy=$_POST["send_copy"];
$copy_subject=$_POST["copy_subject"];
$copy_tomail_field=$_POST["copy_tomail_field"];
$mail_type=$_POST["mail_type"];
$mail_priority=$_POST["mail_priority"];
$return_ip=$_POST["return_ip"];
if($_POST["Submit"]=="Enviar")
{
	if($check_referrer==1)
	{
		$ref_check=preg_split('/,/',$referring_domains);
		$ref_run=sizeof($ref_check);
		$referer=$_SERVER['HTTP_REFERER'];
		$domain_chk="no";
		for($i=0;$i<$ref_run;$i++)
		{
			$cur_domain=$ref_check[$i];
			if(stristr($referer,$cur_domain)){$domain_chk="yes";}
		}
	}
	else
	{
		$domain_chk="yes";
	}
	if($domain_chk=="yes")
	{
		$mail="yes";
		$req_check=preg_split('/,/',$required_fields);
		$req_run=sizeof($req_check);
		$error_message="";
		for($i=0;$i<$req_run;$i++)
		{
			$cur_field_name=$req_check[$i];
			$cur_field=$_POST[$cur_field_name];
			//if($cur_field=="")
			//{
			//	$error_message=$error_message."You are missing the ".$req_check[$i]." field<br />";
			//	$mail="no";
			//}
		}
		$email_check=preg_split('/,/',$required_email_fields);
		$email_run=sizeof($email_check);
		//for($i=0;$i<$email_run;$i++)
		//{
		//	$cur_email_name=$email_check[$i];
		//	$cur_email=$_POST[$cur_email_name];
		//	if($cur_email=="" || !eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$",$cur_email))
		//	{
		//		$error_message=$error_message."You are missing the ".$email_check[$i]." field or the email is not a valid email address.<br />";
		//		$mail="no";
		//	}
		//}
		if($mail=="yes")
		{
			if(getenv(HTTP_X_FORWARDED_FOR))
			{$user_ip=getenv("HTTP_X_FORWARDED_FOR");}
			else
			{$user_ip=getenv("REMOTE_ADDR");}
			if($mail_type=="vert_table")
			{
				$message="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">
							<html>
							<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"></head>
							<body>
							<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"600\">\n";
				foreach($_POST as $key=>$value)
				{
					$value=stripslashes($value);
					$value=preg_replace("/(http:\/\/+.[^\s]+)/i",'<a href="\\1">\\1</a>', $value);
					$value=nl2br($value);
					if($key != "Submit" && $key != "subject" && $key != "required_fields" && $key != "required_email_fields" && $key != "recipient_group" && $key != "error_page" && $key != "thanks_page" && $key != "send_copy" && $key != "copy_subject" && $key != "copy_tomail_field" && $key != "mail_type" && $key != "mail_priority" && $key != "return_ip")
					{
						$message=$message."<tr>\n<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\"><b>".$key."</b></td>\n<td align=\"left\" valign=\"top\" width=\"100%\">".$value."</td></tr>";
					}
				}
				if($return_ip==1)
				{
					$message=$message."<tr>\n<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\"><b>Sender IP</b></td>\n<td align=\"left\" valign=\"top\" width=\"100%\">".$user_ip."</td></tr>";
				}
				$message=$message."\n</table></body></html>";
			}
			else if($mail_type=="horz_table")
			{
				$message="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">
							<html>
							<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"></head>
							<body>
							<table cellpadding=\"2\" cellspacing=\"0\" border=\"1\">\n
							<tr>";
				foreach($_POST as $key=>$value)
				{
					if($key != "Submit" && $key != "subject" && $key != "required_fields" && $key != "required_email_fields" && $key != "recipient_group" && $key != "error_page" && $key != "thanks_page" && $key != "send_copy" && $key != "copy_subject" && $key != "copy_tomail_field" && $key != "mail_type" && $key != "mail_priority" && $key != "return_ip")
					{
						$message=$message."\n<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\"><b>".$key."</b></td>";
					}
				}
				if($return_ip==1)
				{
					$message=$message."<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\"><b>Sender IP</b></td>";
				}
				$message=$message."</tr>\n<tr>\n";
				foreach($_POST as $key=>$value)
				{
					$value=stripslashes($value);
					$value=preg_replace("/(http:\/\/+.[^\s]+)/i",'<a href="\\1">\\1</a>', $value);
					$value=nl2br($value);
					if($key != "Submit" && $key != "subject" && $key != "required_fields" && $key != "required_email_fields" && $key != "recipient_group" && $key != "error_page" && $key != "thanks_page" && $key != "send_copy" && $key != "copy_subject" && $key != "copy_tomail_field" && $key != "mail_type" && $key != "mail_priority" && $key != "return_ip")
					{
						$message=$message."\n<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\">".$value."</td>";
					}
				}
				if($return_ip==1)
				{
					$message=$message."<td align=\"left\" valign=\"top\" style=\"white-space:nowrap;\">".$user_ip."</td>";
				}
				$message=$message."\n</tr>\n</table></body></html>";
			}
			else
			{
				$message="Form Results";
				foreach($_POST as $key=>$value)
				{
					$value=stripslashes($value);
					$value=nl2br($value);
					if($key != "Submit" && $key != "subject" && $key != "required_fields" && $key != "required_email_fields" && $key != "recipient_group" && $key != "error_page" && $key != "thanks_page" && $key != "send_copy" && $key != "copy_subject" && $key != "copy_tomail_field" && $key != "mail_type" && $key != "mail_priority" && $key != "return_ip")
					{
						$message=$message."\n".$key.": ".$value;
					}
				}
				if($return_ip==1)
				{
					$message=$message."IP: ".$user_ip;
				}
			}
			$extra="From: ".$_POST[$reply_to_field]."\n";
			$extra.="X-Priority: $mail_priority\n";
			$cc_tomail=$cc_tomail[$recipients];
			$bcc_tomail=$bcc_tomail[$recipients];
			if($cc_tomail!="")
			{
				$extra.="Cc: $cc_tomail;\n";
			}
			if($bcc_tomail!="")
			{
				$extra.="Bcc: $bcc_tomail[$recipients]\n";
			} 
			if($mail_type=="horz_table" || $mail_type=="vert_table")
			{
				$extra.="MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\n";
			}
			$subject=$_POST["subject"];
			$tomail=$tomail[$recipients];
			mail ("$tomail", "$subject", "$message", "$extra");
			if($send_copy=="yes")
			{
				$copy_extra="From: $Name<$Email>\nX-Priority: $mail_priority\n";
				if($mail_type=="horz_table" || $mail_type=="vert_table")
				{
					$copy_extra.="MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\n";
				}
				$copy_address=$_POST[$copy_tomail_field];
				mail ("$copy_address", "$copy_subject", "$message", "$copy_extra");
			}
			if($thanks_page=="")
			{
				echo "<p>$thanks_page_title</p>";
				echo "<p>$thanks_page_text</p>";
				echo($thanks_page);
			}
			else
			{
				ob_end_clean();
				$redirect="Location: ".$thanks_page;
				header($redirect);
			}
		}
		else
		{
			if($error_page=="")
			{
				echo "<p>$error_page_title</p>";
				echo $error_message;
				echo "<p>$error_page_text</p>";
			}
			else
			{
				ob_end_clean();
				$redirect="Location: ".$error_page;
				header($redirect);
			}
		}
	}
	else
	{
		echo "<p>Sorry, mailing request came from an unauthorized domain.</p>";
	}
}
else
{
	echo "<p>Error</p>";
	echo "<p>No form data has been sent to the script</p>";
}
ob_end_flush();
?>