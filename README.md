DESCRIPTION
-----------

This is a class that is to be used by PHP coders, for writing custom satellite implementations
of the BMLT (Basic Meeting List Toolbox).

It is a class that handles the controller layer in the BMLT client/server system. This class will
deal with all HTTP interactions, and uses the JSON interface of the BMLT root server to operate.

It is designed to provide a functional interface to a communication infrastructure for
BMLT satellites. It communicates with the root server using the JSON variant of the REST
interface.

This class will not use a class to hold transactions or parameters. This is because [the
fox ain't worth the chase](http://philip.greenspun.com/humor/eecs-difference-explained)
That being said, transactions can be serialized and restored. This class is to be used as
a "driver," not a communication stack. It is up to the implementation to do things like
manage multiple transactions and whatnot.

REQUIREMENTS
------------

This class requires a root server version of at least 1.8.1, and 1.8.31 introduces some new details
that will add more capability. It *MAY* work with older root servers, but there are no guarantees as
to the level of functionality available.

 
INSTALLATION
------------

Simply include the bmlt_satellite_controller.class.php file, and instantiate an instance of
bmlt_satellite_controller.

See the unit_test.php and application_test.php files for usage examples.

This class is used in all of the current CMS satellite classes, via [the BMLT Satellite Base Class](https://bmlt.app/specific-topics/bmlt-satellite-base-class/)

CHANGELIST
----------

***Version 1.1.1* ** *- November 12, 2023*

- Fixed warning using strcmp with null.


***Version 1.1.0* ** *- September 6, 2022*

- Converted all XML endpoints to JSON.

***Version 1.0.19* ** *- October 31, 2018*

- Adding composer.json file to allow this to be pulled in to other projects easier.

***Version 1.0.18* ** *- November 10, 2017*

- The spoofed user agent in the call_curl function caused problems with some security software. I changed it to one that I hope works better.

***Version 1.0.17* ** *- June 17, 2017*

- Added a workaround for some non-standard SSL certs.

***Version 1.0.16* ** *- March 19, 2017*

- Fixed a couple of extremely minor issues in the call_curl method that could cause warnings.

***Version 1.0.15* ** *- March 17, 2017*

- Added a blank "index.php" file to prevent dir listings.

***Version 1.0.14* ** *- May 2, 2016*

- Adjusted the README to compensate for Atlassian's new format.
- Added [Doxygen](http://doxygen.nl) documentation.

***Version 1.0.13* ** *- April 21, 2016*

- Got rid of the useless and non-functional application test.
- Replaced tabs with spaces (detabbed).
- Fixed up this README a bit.

***Version 1.0.12* ** *- April 15, 2016*

- Changes to documentation -that's all.

***Version 1.0.11* ** *- July 31, 2014*

- Added a useragent to the cal_curl function, as some servers may block cURL.

***Version 1.0.10* ** *- May 10, 2013*

- Fixed some warnings for strict mode.

***Version 1.0.9* ** *- April 18, 2013*

- Fixed an issue with the curl call that might interfere with sessions.

***Version 1.0.8* ** *- April 16, 2013*

- Fixed an issue that gave Drupal 7 fits.

***Version 1.0.7* ** *- March 29, 2013*

- Added a bit of code to preserve the session across the call.

***Version 1.0.6* ** *- June 26, 2011*

- Fixed the Service Body bug I introduced when I added it.

***Version 1.0.5* ** *- June 25, 2011*

- Added the Service body ID to the Changes function. NOTE: Requires Root server version 1.8.32 or greater.

***Version 1.0.4* ** *- June 7, 2011*

- Moved the project to GitHub
- Added the capability to extract more information about Service bodies. NOTE: Requires Root server version 1.8.31 or greater.
- Added the capability to look up changes for just one meeting (by ID). NOTE: Requires Root server version 1.8.31 or greater.
