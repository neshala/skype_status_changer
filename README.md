# Script to automatically set Skype status

OS X Shell script to automatically change Skype status.
Using Apple Script this bash script will send commands to Skype application if it's running to change user status.
It can be used via cron job (built in into OS X), for example to set user status at specific time to online, and for example at the end of work to offline or invisible.

Tested on OS X El Capitan.
This script will also display system notification and play sound after changing status.

## Install
chmod +x status_changer.sh

While Skype is opened, open terminal and type
./status_changer.sh "online"

Skype will ask you for permissions, please grant those permissions so it can run always.

Than you can set cron job to change status at specific time.
In OS X terminal type

<code>
crontab -e
</code>

For example, here is how I want to change status to online between Monday and Thursday at 3 PM, and on Friday at 8 AM

<code>
0 15 * * 1-4 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "online"
0 08 * * 5 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "online"
</code>

And than I want to set it to offline between Monday and Thursday at 11:10 PM, and on Friday at 4:10 AM

<code>
10 23 * * 1-4 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "offline"
10 16 * * 1-4 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "offline"
</code>
