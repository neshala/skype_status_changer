# Skype Status Changer

OS X Shell script to automatically change Skype status.
Using Apple Script this bash script will send command to Skype application if it's running to change user status.
It can be used via cron job (builtin into OS X), for example to set user status at specific time to online, and for example at the end of work to offline or invisible.
Also this script can be used to force specific status, by running cron on every 5 minutes between specific hours. This can be useful because sometimes Skype change online status on his own (when you login from other device).

Tested on OS X El Capitan.
This script will also display system notification and play sound after changing status.

## Install

Run this command from terminal
<pre>chmod +x status_changer.sh</pre>

While Skype is opened, open terminal and type

<pre>./status_changer.sh "online"</pre>

Skype will ask you for permissions, please grant those permissions so it can run always.

Than you can set cron job to change status at specific time.
In OS X terminal type

<pre>
crontab -e
</pre>

For example, here is how I change status to online between Monday and Thursday at 3 PM, and on Friday at 8 AM

<pre>
0 15 * * 1-4 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "online"
0 08 * * 5 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "online"
</pre>

And than I want to set it to offline between Monday and Thursday at 11:10 PM, and on Friday at 4:10 AM

<pre>
10 23 * * 1-4 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "offline"
10 16 * * 5 /Users/nenadmilosavljevic/Sites/skype_status_changer/status_changer.sh "offline"
</pre>
