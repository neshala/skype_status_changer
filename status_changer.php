<?php
/**
 * This script will do the same as status_changer.sh
 * Only additional thing it can do is to use predefined time frames when to change skype status
 * Bash script language have limits to multidimensional arrays so i picked PHP as language to do that.
 */

### time frame when to change status from default ###
date_default_timezone_set('Europe/Belgrade');
$schedule = [
    '1' => '15:00-23:10',
    '2' => '15:00-23:10',
    '3' => '15:00-23:10',
    '4' => '15:00-23:10',
    '5' => '08:00-16:10'
];

// try to figure out status to be used
$schedule_status = getScheduledStatus($schedule, 'online', 'invisible');

// if we pass argument in terminal, that will be used instead of scheduled status
$new_status = isset($argv[1]) ? $argv[1] : $schedule_status;

// check is argument valid
$allowed_statuses = ['online', 'away', 'dnd', 'invisible', 'offline'];
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


// todo move this function getScheduledStatus to some class file, right now all is in 1 file to make it easier for cron
/**
 * determinate status based on provided time frames
 * @param array $schedule
 * @param string $new_status will be used if time frame is current
 * @param string $default will be used if no time frame is used
 * @return string status
 */
function getScheduledStatus($schedule = [], $new_status = '', $default = '')
{
    $status = $default;

    $curr_ts = strtotime("now");
    $current_day_of_week = date("w");
    $use_default = true;
    foreach ($schedule as $day_of_week => $times) {
        if ($day_of_week != $current_day_of_week) {
            continue;
        }

        $times = explode("-", $times);
        if (!isset($times[0]) || !isset($times[1])) {
            continue;
        }
        $start_time = strtotime($times[0]);
        $end_time = strtotime($times[1]);

        if ($curr_ts >= $start_time && $curr_ts <= $end_time) {
            $status = $new_status;
            $use_default = false;
            break;
        }
    }

    if ($use_default) {
        $status = $default;
    }

    return $status;
}