<?php
/*
Plugin Name: Spelling Checker
Plugin URI: http://www.coldforged.org/spelling-checker-plugin-for-wordpress/
Description: Allows checking of spelling for posts, using the Speller Pages open source project at http://sourceforge.net/projects/spellerpages/. Configure on the <a href="admin.php?page=spell-plugin.php">Options &gt; Spell Checker</a> page. <br/><br/><i>If your are running WP 1.2, you need to configure this plugin by browsing to: <a href="../wp-content/plugins/spell-plugin.php?speller_setup">wp-content/plugins/spell-plugin.php?speller_setup</a></i>
Version: 0.92
Author: Brian "ColdForged" Dupuis
Author URI: http://www.coldforged.org/
Update: http://www.coldforged.org/plugin-update.php?p=544
*/ 

/*
Spelling Checker plugin for WordPress
Copyright (C) 2004  Brian "ColdForged" Dupuis

Based on Speller Pages
Copyright (C) 2003-2004 James Shimada

Portion of the plugin management code based on Spam Karma
Copyright (c) 2004 drDave & Owen Winkler

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

require_once((dirname(__FILE__)."/spell/spellInclude.php"));

/* Damn near all of this "option page insertion" code is from the simply awesome Spam Karma. All hail. */
if(isset( $_REQUEST['speller_setup']) && is_file(ABSPATH . "wp-admin/admin.php") )
{
    die( "If you are using WordPress version 1.3.2 and higher, please use the Admin menu to access the Spell Checker options (under Options >> Spell Checker). You can also use this direct link: <a href=\"". get_settings("siteurl") . "/wp-admin/admin.php?page=spell-plugin.php\">". get_settings("siteurl") . "/wp-admin/admin.php?page=spell-plugin.php</a>");
}

if (! function_exists('is_plugin_page'))
{
	function is_plugin_page()
	{
		return isset($_REQUEST['speller_setup']);
	}
}

if (! function_exists('speller_add_options_page')) 
{
	function speller_add_options_page() 
	{
		if (function_exists('add_options_page'))
			add_options_page(__("Spell Checker Options Page"), __('Spell Checker'), 7, basename(__FILE__));
	}

}

if (! function_exists('speller_option_set'))
{
	function speller_option_set($option) 
	{
		if (! $options = speller_get_settings('speller_options'))
			return false;
		else
			return (in_array($option, $options));
	}

}

if( ! function_exists('personal_dictionary_word_list') )
{
    function personal_dictionary_word_list()
	{
        $return_string = '';

        $current_settings = speller_get_settings('speller_settings');
		if( !file_exists( $current_settings['aspell_dict'] ) )
		{
			create_dictionary();
		}

        $lines = file( $current_settings['aspell_dict'] );
        $count = count( $lines );

        for( $i = 1; $i < $count; $i++ )
        {
            $return_string .= $lines[$i];
        }


        return $return_string;
	}
}


if (!function_exists('check_flag'))
{
	function check_flag($flagname, $allflags) 
	{
		echo (in_array($flagname, $allflags) ? 'checked="checked"' : '');
	}
}

if (function_exists('load_plugin_textdomain'))
{
	load_plugin_textdomain('spellerdomain');
	$insert_html = false;
}
else
	$insert_html = true;
	
if ($insert_html)
{
	global $speller_already_ran;
	if ($speller_already_ran)
		return;
	else
		$speller_already_ran = true;
	
	if(isset($_REQUEST['speller_setup']))
	{
		get_currentuserinfo();
		if ($user_level < 8)
			die ("Sorry, you must be logged in and at least a level 8 user to access Spell Checker's setup options.");
	}
}
	

if( is_plugin_page() )
{
	if ($insert_html)
	{
	?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>WordPress &rsaquo; Options &rsaquo; Spell Checker</title>
		<link rel="stylesheet" href="../../wp-admin/wp-admin.css" type="text/css" />
		<link rel="shortcut icon" href="../../wp-images/wp-favicon.png" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
		</head>
		<body>
	<?php
	}


    $default_settings = array(
        'minimum_user_level' => 8,
        'aspell_path' => '/usr/bin/aspell',
        'language' => 'en_US',
        'aspell_dict' => dirname( __FILE__ )."/spell/aspell.personal",
		'tmpfiledir' => '/tmp',
		);

    $default_options = array(
        'enable_speller',
        'must_be_logged_in',
		'minimum_user_level_to_add',
    );


	if( isset( $_POST['update_options'] ) )
	{
		speller_update_option('speller_options',  $_POST['speller_options']);

		$new_settings = array_merge(speller_get_settings('speller_settings'), $_POST['speller_settings']);
		foreach($default_settings as $key => $val)
			if (empty($new_settings[$key]))
				$new_settings[$key] = $val;
		speller_update_option('speller_settings', $new_settings);

        update_personal_dictionary( $_POST['personal_dictionary_words'] );

		echo '<div class="updated"><p><strong>' . __('Options updated.', 'spellerdomain') . '</strong></p></div>';
		$speller_options = speller_get_settings('speller_options');
		$speller_settings = speller_get_settings('speller_settings');
		if(!is_array($speller_settings)) 
			$speller_settings = $default_settings;
		if(!is_array($speller_options)) 
			$speller_options = $default_options;
	}
	else
	{
		add_option('speller_options', $default_options);
		add_option('speller_settings', $default_settings);
		$speller_options = speller_get_settings('speller_options');
		$speller_settings = speller_get_settings('speller_settings');
		if(!is_array($speller_settings)) 
			$speller_settings = $default_settings;
		if(!is_array($speller_options)) 
			$speller_options = $default_options;
	}


	?>
    <div class="wrap">
        <h2><?php _e("Spell Checker Options", 'spellerdomain') ?></h2>
        <form name="spelling_checker_form" method="post">
            <fieldset class="options">
                <legend><?php _e("Personal Dictionary", 'spellerdomain')?></legend>
                <?php _e('The personal dictionary holds those words that you do not wish to be found as misspellings. You can add to that list of words here.', 'spellerdomain' ) ?>
                <p>
                    <textarea name="personal_dictionary_words" cols="60" rows="9" id="personal_dictionary_words" style="width: 98%; font-size: 12px;" class="code"><?php echo personal_dictionary_word_list(); ?></textarea>            
                </p>
            </fieldset>
            <fieldset class="options">
                <legend><?php _e("General Settings <em>(generally leave at default if you're not sure)</em>", 'spellerdomain')?></legend>
                <ul>
                    <li>
                        <label for="aspell_path">
                            <?php _e('Name and path to aspell executable:', 'spellerdomain' ) ?>
                        </label>
                        <input type="text" name="speller_settings[aspell_path]" value="<?php echo $speller_settings['aspell_path'] ?>" size="60">
                    </li>
                    <li>
                        <label for="aspell_dict">
                            <?php _e('Name and path to aspell personal dictionary:', 'spellerdomain' ) ?>
                        </label>
                        <input type="text" name="speller_settings[aspell_dict]" value="<?php echo $speller_settings['aspell_dict'] ?>" size="60">
                    </li>
                    <li>
                        <label for="language">
                            <?php _e('Aspell language:', 'spellerdomain' ) ?>
                        </label>
                        <input type="text" name="speller_settings[language]" value="<?php echo $speller_settings['language'] ?>" size="20">
                    </li>
                    <li>
                        <label for="tmpfiledir">
                            <?php _e('Path to use for temporary files', 'spellerdomain' ) ?>
                        </label>
                        <input type="text" name="speller_settings[tmpfiledir]" value="<?php echo $speller_settings['tmpfiledir'] ?>" size="20">
                    </li>
                    <li>
                        <label for="broken_aspell_support">
                            <input name="speller_options[]" type="checkbox" id="broken_aspell_support" value="broken_aspell_support" <?php check_flag('broken_aspell_support', $speller_options); ?> />
                            <?php _e('Enable manual personal dictionary handling for broken aspell installations.', 'spellerdomain' ) ?>
                        </label>
                    </li>
                </ul>
            </fieldset>
            <fieldset class="options">
                <legend><?php _e("Security Settings <em>(default values are the safest)</em>", 'spellerdomain') ?></legend>
                <ul>
                    <li>
                        <label for="must_be_logged_in">
                            <input name="speller_options[]" type="checkbox" id="must_be_logged_in" value="must_be_logged_in" <?php check_flag('must_be_logged_in', $speller_options); ?> />
                            <?php _e('Users must be logged in to add words to dictionary.', 'spellerdomain' ) ?>
                        </label>
                    </li>
                    <ul>
                        <li>
                            <label for="minimum_user_level_to_add">
                                <input name="speller_options[]" type="checkbox" id="minimum_user_level_to_add" value="minimum_user_level_to_add" <?php check_flag('minimum_user_level_to_add', $speller_options); ?> />
                                <?php _e('Users must be a minimum of level', 'spellerdomain' ) ?>
        						<input name="speller_settings[minimum_user_level]" type="textbox" id="minimum_user_level" value="<?php echo $speller_settings['minimum_user_level']; ?>" size="2" />
        						<label for="email_deleted_digest">
        						<?php _e('to add words to dictionary.', 'spellerdomain') ?></label>
                            </label>
                        </li>
                    </ul>
                </ul>
            </fieldset>

    	<p class="submit">
    		<input type="submit" name="update_options" value="<?php _e('Update Options') ?>" />
    	</p>
        </form>
    </div>
<?php


}
else
{
	if( speller_option_set( 'enable_speller' ) )
	{

		/*
		   spell_admin_footer()
		   Add the UI to check spelling for the post.
		*/
		if( !function_exists( 'spell_admin_footer' ) )
		{
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
		}
		
		/*
		   spell_admin_head()
		   Add the UI to check spelling for the post.
		*/
		if( !function_exists( 'spell_admin_head' ) )
		{
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
		} 
			
		if( !function_exists( 'spell_insert_headers' ) ) 
		{
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
		}
			
		if( !function_exists( 'spell_insert_comment_button' ) )
		{
			function spell_insert_comment_button($button_class='', $tab_index='') 
			{ ?>
				<input <?php if($button_class!='') echo 'class="' . $button_class . '" ';?>type="button" <?php if($tab_index!='') echo 'tabindex="' . $tab_index .'" ';?>value="Check Spelling" onClick="openSpellChecker();" />
				   <?php
			}
		}
	
		add_filter('admin_footer', 'spell_admin_footer', 9);
		add_filter('admin_head', 'spell_admin_head', 9);
	}
    else
	{
		// Define stubs for the case where the user hasn't configured us yet.
		if( !function_exists( 'spell_insert_headers' ) ) 
		{
			function spell_insert_headers($text_field_id='comment') {
			}
		}
		if( !function_exists( 'spell_insert_comment_button' ) )
		{
			function spell_insert_comment_button($button_class='', $tab_index='') {
			}
		}

	}

	add_action('admin_menu', 'speller_add_options_page');
}
