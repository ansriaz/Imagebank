# ImageBank

An open source social platform based on Laravel and MySQL for researcher community to upload and download already available* datasets and share between their internal (organization / university) community with a pre-integrated MATLAB interface to facilitate researcher community for their simple Machine Learning algorithm and do much more. 

* flicker and google images currently can be used to downloaded the images and youtube for the videos.

# Pre-requisites 

## PHP (Laravel 5.4)
php.ini file
1. max_execution_time = 0
2. memory_limit = 4096M
3. post_max_size = 32M
4. upload_max_filesize = 32M
5. zip extension needs to be enable

Note: php.ini file i used for mac is also available in root folder just to match the enabled extensions.

## MySQL
1. User: “root” == Password: “root”
2. backup in dump folder
3. Currently using socket on mac and TCP/IP on windows
4. No reference key 
5. “php artisan migrate” to migrate data on new installation and then import data

## MATLAB
1. Install MATLAB in hosting machine.

## Others
1. admin=> email: “admin@imagebank.com” & Password: “123456”
2. admin email password (.env)
3. modifications in config/constants.php
4. change check in app/Controllers/Auth/AuthController.php

## How to deploy the system (Installation)
1. Install composer and php
2. Install apache and mysql (or for simplicity install LAMP/WAMP/MAMP)
3. Enable extensions mentioned before. 
4. run “composer install“ command in the root folder of project to download the required files and libraries for the project.
5. If you are using LAMP/MAMP/WAMP setup the project in it and run from there. 
If you are not using any of this tool. use “php artisan server —host=hostname —port=port”
example: “php artisan serve --host=192.163.168.20 --port=80”
6. run job command mentioned below.
7. run flickr crawler as mentioned below. 

## Job
1. Run job by “php artisan queue:listen —timeout=0”

## Flickr Crawler
1. “php artisan crawlflickr”
2. “php artisan crawl”
3. 100 api calls per class label (Paid account can be used for google images and youtube)


## API Keys
### Flickr
Create an application on Flickr dev and then use API key and secret in /app/Logic/Services/FlickrService/FlickrCrawler

### Google
1. Create an application on Google developer console
2. Enable services for the required API (Youtube, Google+, Custom Search Engine)
3. Copy and paste API key and secret in /app/Logic/Services/GoogleServices/* (in respective file)
4. For Google images, create a new search engine at “https://cse.google.com/cse/all”. Enable image search under “Details” in Basic Tab. And change “Sites to Search” dropdown to “Search the entire web”

## Official Documentation Laravel

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering this awesome platform and would love to have contributions. For more details, kindly contect me @ http://www.linkedin.com/in/ansriaz or tweet me https://twitter.com/_ansriaz. 

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Thanks

This project was developed as my thesis work in University of Trento. Special thanks to my supervisor and co-supervisor.
