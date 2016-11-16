<?php
/**
 * This script will do the same as status_changer.sh
 * Main difference is that it can do is to use predefined time frames when to change skype status and we can set mood
 * text message optional.
 * Bash script language have limits to multidimensional arrays so i picked PHP as language to do that.
 *
 * unofficial and deprecated Skype Docs is available here http://caxapa.ru/thumbs/460039/SkypeSDK_deprecated.pdf
 */

### time frame when to change status from default ###
date_default_timezone_set('Europe/Belgrade');
$schedule = [
    'monday' => [
        ['time' => '15:00-23:10', 'status' => 'online', 'mood' => '']
    ],
    'tuesday' => [
        ['time' => '15:10-21:10', 'status' => 'online', 'mood' => ''],
        ['time' => '21:10-23:00', 'status' => 'dnd', 'mood' => 'I am currently playing soccer match for the company. Please leave a message and I will reply later.'],
    ],
    'wednesday' => [
        ['time' => '15:00-23:10', 'status' => 'online', 'mood' => '']
    ],
    'thursday' => [
        ['time' => '08:00-16:10', 'status' => 'online', 'mood' => '']
    ],
    'friday' => [
        ['time' => '08:00-16:10', 'status' => 'online', 'mood' => '']
    ]
];

// try to figure out status to be used
$schedule_status = getScheduledStatus($schedule, 'invisible');

// if we pass argument in terminal, that will be used instead of scheduled status
$new_status = isset($argv[1]) ? $argv[1] : $schedule_status['status'];

$new_mood_text = $schedule_status['mood'];

// check is status valid
$allowed_statuses = ['online', 'away', 'dnd', 'invisible', 'offline', 'na'];
if (!in_array($new_status, $allowed_statuses)) {
    die("Invalid new status !!!");
}

// check is Skype running
$is_running = exec('osascript -e \'
tell application "System Events"
    count (every process whose name is "Skype")
end tell
\'');

if (empty($is_running)) {
    die("Skype is not running. Please open Skype and try again.");
}

// check internet connection
$ping = exec('osascript -e \'
tell application "System Events" to (name of processes) contains "Skype"
if the result is true then
	tell application "Skype"
		send command "GET CONNSTATUS" script name "Skype Changer"
	end tell
end if
\'');

if (!strstr($ping, "ONLINE")) {
    die("Check your internet connection, Skype is not able to connect.");
}

// get current Skype status
$current_status = exec('osascript -e "tell application \"Skype\" to send command \"GET USERSTATUS\" script name \"Skype Changer\""');
$current_status = str_replace("USERSTATUS ", "", $current_status);
$current_status = strtolower($current_status);

// get current mood
$current_mood = exec('osascript -e "tell application \"Skype\" to send command \"GET PROFILE MOOD_TEXT\" script name \"Skype Changer\""');
$current_mood = trim(str_replace("PROFILE MOOD_TEXT", "", $current_mood));

$mood_update_needed = true;
if ($new_mood_text == $current_mood) {
    // no need to update
    $new_mood_text = '';
    $mood_update_needed = false;
}

if ($new_status == $current_status && !$mood_update_needed) {
    die("Nothing to do, Skype status is already {$current_status}.");
}

$mood_message = '';
if ($mood_update_needed) {
    $mood_message = 'send command "SET PROFILE MOOD_TEXT ' . $new_mood_text . '" script name "Skype Changer"';
}


$status_message = "Skype Status changed to {$new_status}. ";
if ($mood_update_needed) {
    $mood_say = !empty($new_mood_text) ? $new_mood_text : 'Empty';
    $status_message .= "Mood updated to {$mood_say}";
}

//send command

// change Skype status
$command = exec('osascript -e \'
tell application "System Events" to (name of processes) contains "Skype"
if the result is true then
	tell application "Skype"
		send command "SET USERSTATUS ' . $new_status . '" script name "Skype Changer"
		' . $mood_message . '
		display notification "' . $status_message . '" with title "Skype Changer"
		say " ' . $status_message . ' "
	end tell
end if
\'');

echo "Done !!!";


// todo move this function getScheduledStatus to some class file, right now all is in 1 file to make it easier for cron
/**
 * determinate status based on provided time frames
 * @param array $schedule
 * @param string $default will be used if no time frame is used
 * @return string status
 */
function getScheduledStatus($schedule = [], $default = '')
{
    $status = $default;
    $mood = '';

    $curr_ts = strtotime("now");
    $current_day_of_week = strtolower(date("l"));
    $use_default = true;
    foreach ($schedule as $day_of_week => $config) {

        if (strtolower($day_of_week) != $current_day_of_week) {
            // not today
            continue;
        }

        if (!is_array($config) || empty($config)) {
            continue;
        }

        foreach ($config as $item) {
            $times = $item['time'];
            $config_status = isset($item['status']) ? $item['status'] : $default;
            $config_mood = isset($item['mood']) ? $item['mood'] : '';

            $times = explode("-", $times);
            if (!isset($times[0]) || !isset($times[1])) {
                continue;
            }
            $start_time = strtotime($times[0]);
            $end_time = strtotime($times[1]);

            if ($curr_ts >= $start_time && $curr_ts <= $end_time) {
                $status = $config_status;
                $mood = $config_mood;
                $use_default = false;
                break 1;
            }
        }
    }

    if ($use_default) {
        $status = $default;
    }

    return ['status' => $status, 'mood' => $mood];
}
