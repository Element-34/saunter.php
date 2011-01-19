(Selenium) Page Objects in PHP
==============================

Page Objects 101
----------------

'Page Objects' is a pattern for creating Selenium scripts that makes heavy use of OO principles to enable code reuse and improve maintenance. Rather than having test methods that are a series of Se commands that are sent to the server, your scripts become a series of interactions with objects that represent a page (or part of one) -- thus the name.  

Without Page Objects
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

With Page Objects
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

As you can see, not only is the script that uses POs [slightly] more human readable, but it is much more maintainable since it really does separate the page interface from the implementation so that _when_ something on the page changes only the POs themselves need to change and not ten billion scripts.  

Anatomy of a PHP Page Object
----------------------------

Page Objects have two parts
* Elements
* Actions

_Elements_

Some languages (like Python) let you have class attributes that are other object instances, but PHP restricts you to scalar values. Because of this, _Elements_ are implemented by overriding the __get() and __set() on the containing PO.  

For putting information into a form, in this case one with a username and password text field, you would override the __set() as such.

    function __set($property, $value) {
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

Setting an element then becomes

    $form->username = "monkey";

Similarly, if you wanted to retrieve a text value from the page you would override the __get().

    function __get($property) {
      switch($property) {
        case "error_message":
          return $this->selenium->getText($this->locators[$property]);
        default:
          return $this->$property;
      }

This means that when trying to check that something on the page is what you expected you would do

    $this->assertEquals($form->error_message, "Incorrect username or password.");
    
Notice that in both situations, the 'case' value for the switch is the element name used in the script

_Actions_

Actions are the part of the page that does something, like submitting a form, or clicking a link. These are implemented as methods on the PO, for example, submitting the login form is implemented as such.

    function login() {
      $this->selenium->click($this->locators['submit_button']);
      $this->selenium->waitForPageToLoad(parent::$string_timeout);
    }

so you can call it as thus.

    $form->login();

Locators
--------

One of things POs help you with is isolating your locators since they are tucked away in a class rather than spread throughout your scripts. I _highly_ suggest that you go all the way and move your locators from in the actual Se calls to a class property.

    private $locators = array(
      "username" => "username",
      "password" => "password",
      "submit_button" => "submit",
      "error_message" => "css=div.error > p"
    );

Now your locators truly are _change in one spot and fix all the broken-ness_. DRY code is good code.


Sharing the server connection
-----------------------------

It has been pointed out to me that what I have done to share the established connection/session to the Se server is borderline evil, but I understand it which trumps evil in my books. In order to make sure we can send / receive from the Se server, I make the connection to it a Singleton which gets set as a class property in its constructor.

    function __construct() {
      $this->selenium = SeleniumConnection::getInstance()->selenium;
    } 

Apparently what I wanted was to use Dependency Injection but I only really understood it last weekend so this works -- if slightly evil.

Intermediary Parent
-------------------

If you look at the the actual script you'll notice that it extends _CustomTestCase_ and not _PHPUnit_Framework_TestCase_ as you might expect. This little layer of redirection lets us add custom asserts and/or exceptions for readability in our scripts.

Custom synchronization would go in the _BasePage_ class as our scripts will no longer need to worry about it -- that a responsibility of the PO.

One thing that drove me bonkers for a month or so was the test discovery of PHPUnit failing my runs because CustomTestCase didn't have any tests. Philosophy aside whether or not failing the run is the right thing to do, the way to make it go away is to make the class _abstract_.

    abstract class CustomTestCase extends PHPUnit_Framework_TestCase {

With the class marked as abstract, PHP won't try to create an instance of it which has a nice side effect of meaning discovery skips over it.

TO-DO
-----
* Groups
* Logging
* Loading 'stuff' from config files
* Sauce Labs OnDemand integration

