<?php 

include_once( dirname( __FILE__ ) . "/spellConfig.php");

// Optionally only allow logged-in users to add words.
// If so, require the auth script so that only people logged
// in can get in.
if( $must_be_logged_in_to_add )
{
	require_once( dirname(dirname(dirname(dirname( __FILE__ )))) . "/wp-admin/auth.php" );
}

function response_xml($return_val) {
	header("Content-type: text/xml");

	print '<?xml version="1.0" encoding="iso-8859-1" ?>';
	print '<data>';
	print "   <returnvalue>$return_val</returnvalue>";
	print '</data>';
}

function add_word_to_dictionary($word_to_add) {

	global $aspell_prog;
	global $aspell_dict_merge;
	global $aspell_dict;
	global $tempfiledir;
	global $fh;
	$aspell_err = "";

	# create temp file
	$tempfilename = tempnam( $tempfiledir, 'aspell_data_' );
	# open temp file, add the submitted text.
	if( $tempfile = fopen( $tempfilename, 'w' )) {
		$return_val = 0;
		$command_array = array();

		fwrite( $tempfile, $word_to_add );
		# exec aspell command - redirect STDERR to STDOUT
		$cmd = "$aspell_prog $aspell_dict_merge ".$tempfilename." 2>&1";
		$aspellret = exec( $cmd, $command_array, $return_val );
		if( $return_val != 0 )
		{
			$debug = fopen( dirname( __FILE__)."/add_failed.out", 'a' );
			fwrite( $debug, "-------------------------------------------\nFAILED TO ADD WORDS TO DICTIONARY:\n" );
			foreach( $command_array as $line )
				fwrite( $debug, $line."\n" );
			fclose($debug);
		}

		response_xml($return_val==0?"SUCCESS":"FAILURE");
	}
    else
	{
		response_xml("FAILURE");
	}

	unlink( $tempfilename );
}

function add_word_to_dictionary_manually($word_to_add) {
    global $aspell_dict;
	$success = true;

	if( file_exists( $aspell_dict ) )
	{
		// Write the new word to the end of the file.
		$lines = file( $aspell_dict );
		$count = count( $lines );

        if( ( $dict = fopen( $aspell_dict, 'wb' ) ) !== false )
		{
			// Empty the file.
			ftruncate( $dict, 0);	  
			  
			// Now, update the word count on the first line. It's the last of three 
			// space-delimited strings (e.g. personal_ws-1.1 english 15)
			$elements = explode( ' ', $lines[0] );
			$count = intval(rtrim( $elements[2]));
			$elements[2] = strval( $count + 1 );
			$lines[0] = implode( ' ', $elements ) . "\n";

			// Write the new file back out. 
			foreach( $lines as $line ) 
			{
				if( !fwrite( $dict, $line ) )
				{
					$success = false;
				} 
			}

			fwrite( $dict, $word_to_add."\n" );
		}
		else
		{
			$success = false;
		} 
		fclose( $dict );
	}
	else
	{
		$success = false;
	} 
		 

	if( $success )
	{
		response_xml("SUCCESS");
	}
	else
	{
		response_xml("FAILURE");
	}  
}

# we only authorize a certain level of user to add words to the 
# dictionary.

get_currentuserinfo();
if((!$must_be_logged_in_to_add) || ($must_be_logged_in_to_add && ($user_level >= $minimum_user_level_to_add_words)))
{
	if( isset($_REQUEST["word"] ) )
	{
        if( $broken_aspell_personal_dictionary )
		{
			add_word_to_dictionary_manually( $_REQUEST["word"] );
		}
        else
		{
			add_word_to_dictionary( $_REQUEST["word"]);
		}
	}
}

?>
