# hr-idea-integration
This project will provide an integration of HackerRank portal problems/submisions with Intellij IDEA.

## How to use it
Make sure you have PHP installed on your machine (I used PHP 7.2 for development purposes)

### Prerequisites
You would be needing the following dependencies to run/debug this script. (Feel free to log an issue if you see any missing dependencies)

|  Extension Name   |   Type        |   Purpose         |
|-------------------|:-------------:|------------------:|
|   eio             |  PECL         |enable logging     |
|   xdebug          |  PECL         |PHP Debugging      |
|    wkhtmltopdf    |  installer    | PDF Generation    |


### Command

#### Get a Basic Authentication Token

1. Go to https://www.base64encode.org/
2. Enter your `username` and `password` in the format `<username>:<password>` in the text box.
3. Click `Encode` button.
4. Copy the generated token.

NOTE: Do not worry. This website is secure. For more details about generation of basic authentication token refer to https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Authorization.

#### Normal Run
<code>/usr/bin/php GetCodes.php `<encrypted_password>`</code>
See below 

#### Debug
<code>/usr/bin/php -dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=127.0.0.1 GetCodes.php `<encrypted_password>`</code>

## How it works

This script will scrape HackerRank website and gets all problems for different domains mentioned in the domains array in `Constants` class in `GetCodes.php` and saves it in a HTML file in a folder of the format `$HOME/content/<domain_name>/<slug>/<slug>.html` in your home directory.
It also generates a PDF for the problem using `wkhtmltopdf` and saved it as `$HOME/content/<domain_name>/<slug>/<slug>.pdf`.
It also gets the stub-code (if any) and saves it to `Solution.java` file in the same directory.

It will create a `debug.log` in the same directory from where the script is executed. Once you run the script if you see any issues, feel free to report an issue on GitHub.

It will generate a `no-solution.out` file which contains the [slugs][1] for problem statements 

It took 38 m 50 s to scrape all content for domains `['algorithms', 'data-structures', 'tutorials/30-days-of-code']` from the website.

For already scraped data (by me) you can refer to `content` directory in this repository.

## Limitations

The design is not very efficient. I tried my best to make things generic. But, I don't want to do over-engineering. PHP files are anyways interpreted.
That is why I didn't even use a configuration file to put `domains`. If you want to scrape more data, add more values to domains array in constants file and run the file.
Alternately, you can delete existing ones if they are already scraped in a previous run, so as to save run time.


[1]: A slug is a unique string identifier for a problem statement on HackerRank. Eg: For a problem with URL https://www.hackerrank.com/challenges/sherlock-and-valid-string/problem , the slug is `sherlock-and-valid-string`.
