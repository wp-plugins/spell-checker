<?php
/*
Plugin Name: Spelling Checker
Plugin URI: http://www.coldforged.org/spelling-checker-plugin-for-wordpress/
Description: Allows checking of spelling for posts, using the Speller Pages open source project
at http://sourceforge.net/projects/spellerpages/. 
Version: 0.8
Author: Brian "ColdForged" Dupuis
Author URI: http://www.coldforged.org/
Update: http://www.coldforged.org/plugin-update.php?p=544
*/ 

/*
Spelling Checker plugin for WordPress
Copyright (C) 2004  Brian "ColdForged" Dupuis

Based on Speller Pages
Copyright (C) 2003-2004 James Shimada

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/


add_filter('admin_footer', 'spell_admin_footer', 9);
add_filter('admin_head', 'spell_admin_head', 9);

/*
   spell_admin_footer()
   Add the UI to check spelling for the post.
*/
function spell_admin_footer($content) {
   // Are we on the right page?
   if((preg_match('|post.php|i', $_SERVER["REQUEST_URI"]))||(preg_match('|page-new.php|i', $_SERVER["REQUEST_URI"]))) {
	   
   if(ini_get('safe_mode'))
   { ?>
<div id="spellingdiv">Spell Checker Disabled - Safe Mode on.</div>
<?php } else { ?>
<div style="display: inline" id="spellingdiv"><input type="button" value="Check Spelling" onClick="openSpellChecker();" /></div>
<?php } ?>
    <script language="JavaScript" type="text/javascript">
        var savebutton = document.getElementById("saveasdraft");
	if( !savebutton ) {
		savebutton = document.getElementById("save");
		if( !savebutton ) {
			savebutton = document.getElementById("savepage");
		}
	}
        var submitp = savebutton.parentNode;
        var substitution2 = document.getElementById("spellingdiv");
        submitp.insertBefore(substitution2, savebutton);
    </script>
   <?php
   }
}

/*
   spell_admin_head()
   Add the UI to check spelling for the post.
*/
function spell_admin_head($content) {
   // Are we on the right page?
   if(((preg_match('|post.php|i', $_SERVER["REQUEST_URI"]))||(preg_match('|page-new.php|i', $_SERVER["REQUEST_URI"])))&&!ini_get('safe_mode')) {
	   
   ?>
<!-- Source the JavaScript spellChecker object -->
<script language="javascript" type="text/javascript" src="<?php bloginfo( 'url' );?>/wp-content/plugins/spell/spellChecker.js">
</script>

<!-- Call a function like this to handle the spell check command -->
<script language="javascript" type="text/javascript">
function openSpellChecker() {
	// get the textarea we're going to check
	var txt = document.getElementById("content");
	// give the spellChecker object a reference to our textarea
	// pass any number of text objects as arguments to the constructor:
	var speller = new spellChecker( txt,"<?php bloginfo('url');?>" );
	// kick it off
	speller.openChecker();
}
</script>
   <?php
   }
}

function spell_insert_headers($text_field_id='comment') {
?>
<!-- Source the JavaScript spellChecker object -->
<script language="javascript" type="text/javascript" src="<?php bloginfo( 'url' );?>/wp-content/plugins/spell/spellChecker.js">
</script>

<!-- Call a function like this to handle the spell check command -->
<script language="javascript" type="text/javascript">
function openSpellChecker() {
        // get the textarea we're going to check
        var txt = document.getElementById('<?php echo $text_field_id;?>');
        // give the spellChecker object a reference to our textarea
        // pass any number of text objects as arguments to the constructor:
        var speller = new spellChecker( txt,"<?php bloginfo('url');?>" );
        // kick it off
        speller.openChecker();
}
</script>
<?php
}


function spell_insert_comment_button($button_class='', $tab_index='') {
?>
<input <?php if($button_class!='') echo 'class="' . $button_class . '" ';?>type="button" <?php if($tab_index!='') echo 'tabindex="' . $tab_index .'" ';?>value="Check Spelling" onClick="openSpellChecker();" />
   <?php
}
