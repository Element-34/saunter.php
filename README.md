(Selenium) Page Objects in PHP
==============================

Page Objects 101
----------------

'Page Objects' is a pattern for creating Selenium scripts that makes heavy use of OO principles to enable code reuse and improve maintenance. Rather than having test methods that are a series of Se commands that are sent to the server, your scripts become a series of interactions with objects that represent a page (or part of one) -- thus the name.  

Without Page Objects
```php    
/**
* @test
*/    
public function example()
{
    $this->selenium->open('/');
    $this->selenium->click('css=div.account_mast a:first');
    $this->selenium->waitForPageToLoad("30000");
    $this->selenium->type('username', 'monkey');
    $this->selenium->type('password', 'buttress');  
    $this->selenium->click('submit');
    $this->selenium->waitForPageToLoad("30000");
    $this->assertEquals($this->selenium->getText("css=div.error > p"), "Incorrect username or password.");  
}
```
With Page Objects
```php
/**
* @test
*/
public function example()
{
    $landing = new LandingPage();
    $landing->open_default_base_url();
    $form = $landing->open_sign_in_form();
    $form->username = "monkey";
    $form->password = "buttress";
    $form->login();
    $this->assertEquals($form->error_message, "Incorrect username or password.");
}
```
As you can see, not only is the script that uses POs [slightly] more human readable, but it is much more maintainable since it really does separate the page interface from the implementation so that _when_ something on the page changes only the POs themselves need to change and not ten billion scripts.  

Anatomy of a PHP Page Object
----------------------------

Page Objects have two parts
* Elements
* Actions

_Elements_

Some languages (like Python) let you have class attributes that are other object instances, but PHP restricts you to scalar values. Because of this, _Elements_ are implemented by overriding the __get() and __set() on the containing PO.  

For putting information into a form, in this case one with a username and password text field, you would override the __set() as such.
```php
function __set($property, $value) 
{
    switch($property) {
        // cases can be stacked so all the 'text' ones here
        case "username":
        case "password":
            $this->selenium->type($this->locators[$property], $value);
            break;
        // if there were other types of elements like checks and selects
        // there would be another stack of cases here
        default:
            $this->$property = $value;
    }
}
```
Setting an element then becomes
```php
$form->username = "monkey";
```
Similarly, if you wanted to retrieve a text value from the page you would override the __get().
```php
function __get($property) 
{
    switch($property) {
        case "error_message":
            return $this->selenium->getText($this->locators[$property]);
        default:
            return $this->$property;
    }
}
```
This means that when trying to check that something on the page is what you expected you would do
```php
$this->assertEquals($form->error_message, "Incorrect username or password.");
```    
Notice that in both situations, the 'case' value for the switch is the element name used in the script

_Actions_

Actions are the part of the page that does something, like submitting a form, or clicking a link. These are implemented as methods on the PO, for example, submitting the login form is implemented as such.
```php
function login() 
{
    $this->selenium->click($this->locators['submit_button']);
    $this->selenium->waitForPageToLoad(parent::$string_timeout);
}
```
so you can call it as thus.
```php
$form->login();
```
Locators
--------

One of things POs help you with is isolating your locators since they are tucked away in a class rather than spread throughout your scripts. I _highly_ suggest that you go all the way and move your locators from in the actual Se calls to a class property.
```php
private $locators = array(
    "username" => "username",
    "password" => "password",
    "submit_button" => "submit",
    "error_message" => "css=div.error > p"
);
```
Now your locators truly are _change in one spot and fix all the broken-ness_. DRY code is good code.


Sharing the server connection
-----------------------------

It has been pointed out to me that what I have done to share the established connection/session to the Se server is borderline evil, but I understand it which trumps evil in my books. In order to make sure we can send / receive from the Se server, I make the connection to it a Singleton which gets set as a in the base PO constructor.
```python
def __init__(self):
    self.se = wrapper().connection
```
Apparently what I wanted was to use Dependency Injection but I only really understood it last weekend so this works -- if slightly evil.

Intermediary Parent
-------------------

If you look at the the actual script you'll notice that it extends _CustomTestCase_ and not _PHPUnit_Framework_TestCase_ as you might expect. This little layer of redirection lets us add custom asserts and/or exceptions for readability in our scripts.

Custom synchronization would go in the _BasePage_ class as our scripts will no longer need to worry about it -- that a responsibility of the PO.

One thing that drove me bonkers for a month or so was the test discovery of PHPUnit failing my runs because CustomTestCase didn't have any tests. Philosophy aside whether or not failing the run is the right thing to do, the way to make it go away is to make the class _abstract_.
```php
abstract class CustomTestCase extends PHPUnit_Framework_TestCase {
```
With the class marked as abstract, PHP won't try to create an instance of it which has a nice side effect of meaning discovery skips over it.

Config Files
------------

In the _conf_ directory there is a saunter.inc.default file. Taking a page from the RoR playbook, this file should be copied and renamed to just saunter.inc. The stacktrace you will get if you forget should remind you to do this. The reason saunter.inc is not checked in is so you can have different configs across different locations (like individual jobs in the CI server) or same location (one per environment and managed via symlink).

All system/framework-wide configurations should go in this file. Rather than _completely_ pollute the global namespace I put everything into a settings array.
```php
$GLOBALS['settings']
```
You're stuff should do the same.

Sauce Labs OnDemand
-------------------

Running your scripts locally or in the OnDemand cloud is simply a matter of setting 
```php
$GLOBALS['settings']['sauce.ondemand']
```
to _true_ and adjusting for which OS and browser combination you desire. Unlike other PHPUnit integrations with Selenium, I don't suggest that you figure out how to iterate over browser strings for runner-base parallelization. Instead, create a job per OS/browser in your CI server and use its local saunter.inc to configure things. This way when one of those jobs fail (and eventually one will) you don't need to change any code to troubleshoot it -- you just need to run that job.

Notice as well that in the intermediary class, the teardown method will set the OnDemand job name and status as well.

Groups
------

My current favourite way of managing scripts is not through the use of hierarchical  directories organized by functionality or persona but by 'tag'. Or since this is a framework expected to be run by PHPUnit, the use of [Groups](http://www.phpunit.de/manual/current/en/appendixes.annotations.html#appendixes.annotations.group).

Tagging your scripts nicely deals with the whole venn diagram problem of where in their hierarchy to stuff things. Now it doesn't matter.

The @group names are now used with Sauce OnDemand as job tags.

Suites
------

Some more OnDemand magic is that you can filter your job list by 'tag'. Since we're using test discovery based on tags there is not an easy way to look at 'all the jobs in a certain run' but there is a clever hack around that by creating a run specific tag. In this example is it uniqid with a prefix of 'run-'. Again, its a Singleton, but we really want it to be the same across all instances of our scripts (which is what Singletons are for). Now we can login to Sauce Labs and filter on the appropriate 'run' tag to see all our scripts.

Logging
-------

Logging is done through the standard [Log PEAR package](http://pear.php.net/package/Log). Use logging intelligently in your scripts. As in, use it _very_ sparingly. I coach people to basically only use it to log things that matter and were randomly generated (like usernames, passwords, email addresses) that could assist in debugging a script failure.

Soft Asserts
------------

Selenium IDE has this notion of verify* which are apparently what are called 'soft asserts' as they look like an assert but don't end the script immediately. The Testing/Selenium driver also does not have this notion but by wrapping an assert in a try/catch block you can create this behaviour. Because we have subclassed PHPUnit_Framework_TestCase as CustomTestCase we can put the verify* commands that we need there.
```php
public function verifyEquals($want, $got)
{
    try {
        $this->assertEquals($want, $got);
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
}
```
I believe that the PHPUnit driver includes a number of these verify commands already, but I tend to only create them as I need them so one project might have a some and a different project might need others.

Data Driving
------------

PHPUnit can drive a test method with parameters returned from a method; either as an array of arrays or as an Iterator object. (Though so far as I can tell, no one has actually done the latter -- or at least written about it.) _SimpleDataProviderTest.php_ follows the array or arrays pattern. In theory, the other approach would be for CSV or DB queries -- someone point me to the blog post which explain this approach.

Listeners
---------

Standard xUnit workflow is setup -> script -> teardown. The integration with Sauce Labs OnDemand used to sit in the teardown phase of this, but due to a recent change in PHPUnit, it needs to be moved into a custom listener. The workflow is now:
* setup (includes/CustomTestCase.php)
* script (scripts/*)
* assertPostConditions (includes/CustomTestCase.php)
* tardown (includes/CustomTestCase.php)
* add* (listeners/StatusListener.php)
* endtest (listeners/StatusListener.php)

TO-DO
-----
* Data Providers (PHPUnit native)
* Oracles (curl)
* Oracles (db)
