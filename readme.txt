=== Spell Checker ===
Tags: spelling, admin, comments
Contributors: coldforged

The Spelling Checker plug-in for WordPress provides a built-in facility for 
spelling checks on posts from within the administration pages as well as 
spelling checks on comments via simple calls added to your templates. It 
requires no changes to the WordPress code-base to use, you merely drop it in to 
the plug-ins folder and enable it in the interface. This version has been tested 
in both WordPress 1.2.1 as well as WordPress 1.5 Strayhorn. 

== Installation ==

* The easiest way to install the plugin is to use the One-Click Install option 
of the WordPress Plugin Manager. 
* Otherwise, install the spell-plugin.php file 
in the 'wp-content/plugins' directory and the rest in a directory you create 
called 'wp-content/spell-plugin. 
* The plugin must be enabled from the plugins 
page, then ''you must visit the Spell Checker options page at least once'' 
before the plugin will be finally enabled. Follow the appropriate link in the 
Spell Checker entry of the Plugins page. 

== Frequently Asked Questions ==

= How do I use it? =

On the "Write Post" page there should be a new button called "Check Spelling". 
Click it. You will get a new window that performs the spelling checking. Note 
that you must have Javascript enabled for this to work. You must also have the 
"aspell" executable on your hosting machine for this to work as that is the 
spelling service provider used. 

= How do I enable people to spell check their comments? = 

Adding the ability for your readers to check the spelling of their comment text 
has never been easier, though it may reduce your enjoyment of their 
inadvertently incorrect spellings. Simply modify your comments template to call 
the following function somewhere within it (this inserts the necessary 
Javascript for opening the spelling checker window): 

'spell_insert_headers();'

For default WordPress installations, you need no parameters. If for whatever 
reason your textarea is a different id than the default "comment" id, you can 
specify that id as a parameter to this call, for example: 

'spell_insert_headers("differentcommentid");'


Now, the only thing left to do is insert the following code where you want the 
"Check Spelling" button to appear: 

'spell_insert_comment_button();'

For more flexibility you can specify the following parameters to this function: 

* $button_class - If so desired, you can apply a class specifier to the 
resulting button. Leaving this as the default causes no class to be specified. 
For instance, to specify a class of "buttonclass", simply specify "buttonclass" 
as the first parameter to the function. 
* $tabindex - To enhance usability, you can specify a tabindex to include in the 
button declaration. If undeclared, no tabindex is specified. Note that if you 
don't wish to specify a class you must pass an empty string as the first 
parameter. As an example to specify a tabindex of 4 with no class, call the 
function as follows: 

'spell_insert_comment_button( '', "4" );'

For more flexibility you can add words to the dictionary so that they are not 
marked as misspelled. By default only people who are registered users of your 
blog and are logged in ''and'' have a user level greater than 8 can add words to 
the dictionary. This is a security feature so that people can't add nonsense to 
your dictionary. People who do not meet these requirements will simply not have 
an "Add" button on the form. You can modify this behavior with the from the 
Spell Checker options administration page. 

= FAQ = 

Q. I get an error when try to check the spelling and it says something 
like "The required field 'name' is missing." What do I do? 
A. That indicates that the language setting you have selected -- it defaults to 
en_US -- is not appropriate for your aspell installation. Just going by 
eyeballing statistics, my first suggestion would be to try setting the language 
to "english" (don't copy the quotes, just the word) and if that still causes 
problems, cut it back to "en". If neither work ask you hosting provider for the 
appropriate setting for their aspell installation. 

Q. I get blank windows in the spelling checker box. Why? 
A. It's likely you either don't have the 'aspell' executable or the path to it 
is not correctly configured. To find out, get a shell to your host and perform a 
"which aspell". If it tells you there is no such thing install it if possible. 
If it provides a path (something like '/usr/local/bin/aspell') then open the 
Spell Checker options administration page and make sure the path matches. When 
you first install the plugin, this is precisely how the plugin determines the 
location of the aspell executable. 

Q. I get errors in Internet Explorer that say something like "Error: Object 
expected" on Line 44. 
A. This is another case of an aspell path problem. See the previous question for 
details. 

Q. I don't get a button. Instead it says something about "safe_mode". 
A. Servers that run PHP in safe_mode sometimes do not allow execution of 
executables in certain ways. Unfortunately, this plugin requires the execution 
of the aspell executable to do its work. Until the author figures out a way 
around it, there is no support for hosts that run PHP in safe_mode. Sorry. The 
current version checks for safe_mode and refuses to even try to run, just to 
save some support questions. 

Q. I don't see an "Add" button on my checker! 
A. By default you must be logged in to your WordPress installation and the user 
you log in as must have a user level of 8 before you see an "Add" button. You 
can modify this behavior with the from the Spell Checker options administration 
page. 

Q. My "Add" button grays out and changes to "Adding..." when I add a word and 
then never comes back. 
Q. I get a failure when adding a word to the dictionary. 
A. Something is probably not configured correctly for your particular "aspell" 
installation. I gave a best first approximation of the command to add words to 
dictionaries, but aspell has many versions and may have a different set of 
parameters for you. First, check in the "wp-content/spell-plugin/" directory for 
a file called "add_failed.out". It should have some data for you. Try to 
determine what happened and perhaps experiment with your aspell installation and 
determine the correct parameters. 

Q. It doesn't work! I get the "Spell check in progress..." indication in the 
popup window, but then just a blank page. 
A. This is a permissions problem with the personal dictionary. Open the Spell 
Checker options administration page and change the location of the personal 
dictionary to be a place where the ''Apache web server can write.'' Note that to 
specify, for instance, your home directory, you must provide the full path to 
that directory, as the web server generally runs under a different user id. 

Q. I get failures (of various kinds) when trying to add a word to the dictionary 
and I'm running WordPress 1.2.1. 
A. If you leave the options at the default -- especially the security option 
that users must be logged in to the blog in order to add words -- you will 
likely fail to add words in WordPress 1.2.1. This is a problem with a relative 
path in the wp-admin/auth.php file that the plugin uses to log you in. To fix, I 
highly recommend you upgrade to 1.2.2 as soon as possible. 

If all else fails and you never wind up with an aspell personal dictionary (by 
default created at "wp-content/plugins/spell/aspell.personal") or you determine 
that your version of aspell doesn't support creation or merging of personal 
dictionaries -- yes, this ''does'' happen -- there's one last thing to try. Open 
the Spell Checker options administration page and enable the "Enable manual 
personal dictionary handling for broken aspell installations" option. This will 
turn on some functionality that will circumvent the aspell personal dictionary 
creation and merging functions and handle that inside the plugin. 

= Special thanks =

This code is merely a mild reworking of the 
[http://sourceforge.net/projects/spellerpages Speller Pages] project on Source 
Forge to work within the confines of the WordPress plug-in framework. All credit 
for the actual spelling checker goes to them. The method of getting the 
functionality into the interface without code changes was inspired by the 
[http://www.nosq.com/technology/2004/10/runphp-wordpress-plug-in/ RunPHP] 
plugin. Options administration inspired by 
[http://www.unknowngenius.com/blog/wordpress/spam-karma Spam Karma]. 

== Screenshots ==

1. Fear the beauty.