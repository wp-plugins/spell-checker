<?php
include_once( dirname(dirname(dirname(dirname( __FILE__ )))) . "/wp-config.php");

# Configuration parameters.

# For security, only people registered and logged in can add words to the
# dictionary. If you want your readers to be able to add words to the
# dictionary from the comment checking form, set this to false.
$must_be_logged_in_to_add = true;

# In addition, you can define a minimum WordPress user level a user
# must be before they can add words to the dictionary.
$minimum_user_level_to_add_words = 8;

# path to the 'aspell' executable. Get this by typing "which aspell" 
# in a terminal on the web host.
$aspell_path = '/usr/bin/';

# aspell executable name
$aspell_prog = $aspell_path.'aspell';

# aspell language setting
$lang = 'en_US';

# point to aspell personal dictionary. This will be created in the first 
# invocation 
$aspell_dict = dirname( __FILE__ )."/aspell.personal";

# aspell options for creating a personal dictionary. Depending on your version,
# the default should be fine.
$aspell_dict_create = "create personal --lang=$lang --personal=$aspell_dict < ";

# aspell options for merging words into the personal dictionary
$aspell_dict_merge = "merge personal --lang=$lang --personal=$aspell_dict < ";

# aspell options for entering into "a" mode.
$aspell_opts = "-a --lang=$lang --personal=$aspell_dict";

# aspell options in the absence of a functional personal dictionary.
# Essentially a "safe mode" if we fail to create a dictionary.
$aspell_opts_nodict = "-a --lang=$lang";

# the temporary file directory.
$tempfiledir = "";


?>
