#!/bin/sh
# filename: Skype Changer
# usage from Terminal prompt: sh status_changer.sh "Online"

# check is argument passed
if [ "$#" -ne 1 ]
then
  echo "Please provide status as argument."
  exit 1
fi

# check is status valid
status=$(echo "$1" | tr '[:upper:]' '[:lower:]')

if [ "$status" != 'online' ] && [ "$status" != 'away' ] && [ "$status" != 'dnd' ] && [ "$status" != 'invisible' ]  && [ "$status" != 'offline' ]; then
    echo 'Invalid Skype status !!!'
    exit 1
fi

# get current status, and check is change needed
online_status=`osascript -e "tell application \"Skype\" to send command \"GET USERSTATUS\" script name \"my script\"" | tr '[:upper:]' '[:lower:]'`

if [ "$online_status" = "userstatus $status" ]; then
    echo "Nothing to do, skype status is already $status ."
    exit 1
fi

osascript -e '
tell application "System Events" to (name of processes) contains "Skype"
if the result is true then
	tell application "Skype"
		send command "SET USERSTATUS '$status'" script name "Skype Changer"
		display notification "Status changed to '$status'" with title "Skype Changer"
		say "Skype Status changed to '$status' "
	end tell
end if
'