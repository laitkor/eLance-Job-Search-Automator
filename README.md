eLance Job Search Automator
===========================

Shortlists specific jobs from all jobs available on eLance based on prespecified criteria. 

Open **config.php** and enter your Elance and SMTP details. Additionally you can modify the available search criteria. If you don't have an access-token already, generate one, by following the below steps -

1) Open **request.php** and replace *ENTER_YOUR_API_KEY* by your Elance API Key (Elance Client ID) there. Also replace - [http://localhost:8080/callback/index.php](#)  by URL for index.php as your callback URL.

2) Open **index.php** and replace *ENTER_YOUR_API_KEY* by your Elance API Key (first parameter) and *ENTER_YOUR_SECRET_KEY* by Customer Secret code (second parameter).

3) Now make a request to your request.php, from browser and follow the steps, to get you access token.

Now copy the Access Token displayed on your screen and enter in you **config.php**, by replacing - *ENTER_YOUR_ACCESS_TOKEN_HERE*. All your configurations are now done, just schedule a Cron job to execute **elance.php** through URL daily or weekly (or as you wish).
