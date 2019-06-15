A template for Moodle modules.  Updated from Moodle HQ's moodle-mod_widget template.

Added:

 - Custom renderer
 - Mustache template
 - Working backup/restore functionality for Moodle2

Instructions for installing:
============================

Download the zip file or clone the repository into your moodle/mod folder using the instructions given under the button "Clone or download".

Assuming you are going to change your module name from widget to something more relevant, do the following.

Rename these files:
===================
All 4 files in backup/moodle2 should have the name of your new module.

The lang/en/widget.php file should be renamed to the name of your new module.

Replace widget with your new module name
========================================
Carry out a search and replace for "widget" replacing it with the name of your new module.  You can do this in a number of ways depending on your text editor.  If you don't have one handy, download Brackets (http://brackets.io/) which is free, open source and handles this stuff well.

Navigate to your admin dashboard and install the new module.

For newbie users
================
You may notice a reference to a local class debugging.  This is a simple script that allows you to output debugging information to file.

It looks like this"

<pre>
namespace mod_widget\local;

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
\mod_widget\local\debugging::logit("What is in a widegt: ", $widget);
</pre>
Using Xdebug
============
Brackets, Sublime, PHP Storm and many other editors or IDEs use this.  If you are using Linux, there's plenty of info to google.

Windows users
=============
Whether by choice or not, many people are stuck with MS.  Xampp is a workable development environment.  Install the basic Xampp rather than the Moodle/Xampp package.  Install Moodle under htdocs and change the existing index file if desired.

Also install, at minumum, Git for Windows (even if you don't use it - and you should - you can use the git bash command line for many tasks).

This article is helpful for installing xdebug on xampp:
https://gist.github.com/odan/1abe76d373a9cbb15bed

Have fun developing for Moodle.  This activity module is an
example from MoodleBites for Developers level 2.

https://www.moodlebites.com/mod/page/view.php?id=19542

Richard Jones, richardnz@outlook.com
Karapiro Village, NZ
September 16th, 2018.