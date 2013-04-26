## Bean Counter App

A (hopefully) simple PHP invoicing/small buisness management tool for digital freelancers See the [project page](http://beancounterapp.com) for more details.  


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






## Licensing


Released under the [Creative Commons Attribution-NonCommercial 3.0 License](http://creativecommons.org/licenses/by-nc/3.0/).

The following also applies:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.