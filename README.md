## Bean Counter App

A (hopefully) simple PHP invoicing/small buisness management tool for digital freelancers See the [project page](http://beancounterapp.com) for more details.  Released under the [MIT license](http://www.opensource.org/licenses/mit-license.php).


## Installation


### Step 1

Clone the repo. Create a MySQL database (preferrably with a UTF-8 encoding) like so:
	CREATE DATABASE `beancounter` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
	
### Step 2	
Copy the information from config.sample.php in the root folder to a new file called config.php remembering to change the database settings.
At this stage you can also edit a few options in the config.php file - if you so wish.

#### Step 3
Go to http://whatever-you-have-called-your-beancounter-installation.com/ and the install script should run automatically.


## Changelog

### v0.5b
* Code reworked to make it simpler and work better for other people