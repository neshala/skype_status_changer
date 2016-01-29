<?php
/**
 * This script will do the same as status_changer.sh
 * Only additional thing it can do is to use predefined time frames when to change skype status
 * Bash script language have limits to multidimensional arrays so i picked PHP as language to do that.
 */

$new_status = isset($argv[1]) ? $argv[1] : '';
$allowed_statuses = ['online', 'away', 'dnd', 'invisible', 'offline'];

// check is argument valid
if (!in_array($new_status, $allowed_statuses)) {
    die("Invalid new status !!!");
}

// get current Skype status
$online_status = exec('osascript -e "tell application \"Skype\" to send command \"GET USERSTATUS\" script name \"my script\""');
$online_status = str_replace("USERSTATUS ", "", $online_status);
$online_status = strtolower($online_status);

if ($new_status == $online_status) {
    die("Nothing to do, Skype status is already {$online_status}.");
}

// change Skype status
$command = exec('osascript -e \'
tell application "System Events" to (name of processes) contains "Skype"
if the result is true then
	tell application "Skype"
		send command "SET USERSTATUS '.$new_status.'" script name "Skype Changer"
		display notification "Status changed to '.$new_status.'" with title "Skype Changer"
		say "Skype Status changed to '.$new_status.' "
	end tell
end if
\'');

echo "Done !!!";