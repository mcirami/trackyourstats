Unfortunately, there's currently no web interface to make doing this easier. 

How to setup a trackyourstats install:

TrackYourStats is setup to be hosted on one website, with multiple databases, and one master database.
It changes databases solely through the URL. Either through the subdomain of the website, login_url/landing_page, and offer urls.

Companies information is stored in the 'companies' table of the master database, which contain their 'subdomain', 'colors', and varies other fields.

'login_url' and 'landing_page' are fields on the 'company' table in the master database, (trackyourstats)
These are meant for the landing page and login pages for companies.
e.g.
cashboom.com -> is the 'landing_page'
stats.cashboom.com -> is the 'login_url'


Finding the database through the subdomain is dynamic, nothing needs to be set in the master database.
xyz.trackyourstats.com, will always try to connect to the xyz database.

Offer urls are _only_ meant for offers, and will not work for normal logins or site usage.
e.g.
hotdatestonight.com -> will 404 if no offer information is set, login is not possible 
You don't have to worry about offer urls.


Some examples:
trackyourstats.com -> goes to master database, trackyourstats
powercastcash.trackyourstats.com -> goes to powercastcash database, based on the subdomain
cashboom.com -> goes to the cashboom database, based on the set landing_page, allows landing page resource loading
stats.powercastcash.com -> goes to the powercastcash database.

Let's look at an example ticket.
_____________________________
Hey bruh need this new install:

launionchatters.com
stats.launionchatters.com
_____________________________

1. First, we need to get the company's subdomain. Based on the information provided, 
we can see it's obviously 'launionchatters'

2. Next, lets add this new company to the 'company' table in the master database.
Open plesk, go to trackyourstats.com domain, and open the trackyourstats database.

Let's fill out the required fields:

shortHand: La Union (this is shown as the title in the webbrowser, for e.g.)
subDomain: launionchatters (VERY IMPORTANT, this has to be the name of the database!)
uid: lAuN (four letter unique identifier for the company, can come up with anything you want)
db_version: 1.37 (This was legacy updating informatino, not needed anymore, just put a number in there)
login_url: stats.launionchatters.com (the page thats specific for login, usually stats.xyz.com)
landing_page: launionchatters.com (The page that loads the lander site, usually the base domain)

Rest of the fields don't matter.

3. Next, let's create the new database. Create a database under 'trackyourstats.com' in plesk, and name it the subDomain, in our case, 'lanunionchatters'.

4. Once the database is created, and while we're still in plesk, lets add the new subdomain to our dns records.
Todo so, go to trackyourstats.com domain, Click "DNS Settings", copy trackyourstats.com static ip (	208.94.65.205).
Click "Add Record"
Record type: A
Domain name: launionchatters (.trackyourstats.com, its forced to be a subdomain)
IP Address: 208.94.65.205

Click ok, and it will return you to the DNS Settings page, it'll say changes won't be applied, until you hit "Apply".
Click "Apply" and you should be good to go.

4. Lastly, we have to import the base_install for TYS, and run the latest migrations for the new database. 
To do so, we'll need to ssh into our server. Our current setup is really ghetto, so prepare yourself.

ssh into the trafficmasters-cs server. You can find the login in our shared Sync folder. 
SERVERS/trafficmasters_ssh.txt

We need to use the latest php version, and its hacky setup right now, to do so, run:
``` source ~/.bashrc ```

then check the php version
``` php -v ```
Make sure we're on at least 7.1 or 7.0 i dont remember, as long as its 7.

Now, we need to cd into the vhosts directory.

``` cd /var/www/vhosts/trackyourstats.com/httpdocs-laravel ```

6. Import the base_install.sql dump to the new install's database.
Use the artisan command "$ php artisan migrate:legacy nameofthedatabase"
in our example, "php artisan migrate:legacy launionchatters"
If it says, "Success!", you're good.

Now, we have to run our migrations, to do so, i have a custom command setup to run migrations against all current installs:

``` php artisan migrate:all ``` 

Let it run, if you've only added one install, only one should update.

If you have any offer urls to add, you can add them through the website.

Default god password:
god
god123

If you have any questions, ask me on slack! :)
