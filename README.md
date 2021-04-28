A template for Moodle modules.  Updated from Moodle HQ's moodle-mod_collaborate template.

Added:

 - Mustache template
 - Working backup/restore functionality for Moodle2
 - No grades or events implemented

Instructions for installing:
============================

Download the zip file or clone the repository into your moodle/mod folder using the instructions given under the button "Clone or download".

Assuming you are going to change your module name from collaborate to something more relevant, do the following.

Rename these files:
===================
All 4 files in backup/moodle2 should have the name of your new module.

The lang/en/collaborate.php file should be renamed to the name of your new module.

Replace collaborate with your new module name
========================================
Carry out a search and replace for "collaborate" replacing it with the name of your new module.  You can do this in a number of ways depending on your text editor.  If you don't have one handy, download Brackets (http://brackets.io/) which is free, open source and handles this stuff well.

Navigate to your admin dashboard and install the new module.

For newbie users
================
You may notice a reference to a local class debugging.  This is a simple script that allows you to output debugging information to file.

It looks like this"

<pre>
namespace mod_collaborate\local;

class debugging {
    public static function logit($message, $value) {

        $file = fopen('mylog.log', 'a');

        if ($file) {
            fwrite($file, print_r($message, true));
            fwrite($file, print_r($value, true));
            fwrite($file, "\n");
            fclose($file);
        }
    }
}
</pre>

Place the above code in a file called debugging.php.

Modify the file location (mylog.log) if desired.  Anywhere you want to view the contents of an object use:
<pre>
\mod_collaborate\local\debugging::logit("What is in a widegt: ", $collaborate);
</pre>

Using Xdebug
============
Brackets, Sublime, PHP Storm and many other editors or IDEs use this.  If you are using Linux, there's plenty of info to google.

Windows users
=============
Whether by choice or not, many people are stuck with MS.  Xampp is a workable development environment.  Install the basic Xampp rather than the Moodle/Xampp package.  Install Moodle under htdocs and change the existing index file if desired.

Also install, at minumum, Git for Windows (even if you don't use it - and you should - you can use the git bash command line for many tasks).

This is further described in the free course: MoodleBites for TechPrep
https://www.moodlebites.com/enrol/index.php?id=228

This article is helpful for installing xdebug on xampp:
https://gist.github.com/odan/1abe76d373a9cbb15bed

Changes
=======
1.1 - 27/08/20 - MINOR change to rendering
Put the renderer function code into classes/output/view.php
Called the template by the same name and now use the core renderer to display the page content.
Removed the renderer.php file.

Further information
===================
Have fun developing for Moodle.  This activity module is an
example from MoodleBites for Developers level 2.

https://www.moodlebites.com/mod/page/view.php?id=19542

Richard Jones, richardnz@outlook.com
Pirongia, NZ
August 27th, 2020.