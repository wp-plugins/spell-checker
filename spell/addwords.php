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

# we only authorize a certain level of user to add words to the 
# dictionary.

get_currentuserinfo();
if((!$must_be_logged_in_to_add) || ($must_be_logged_in_to_add && ($user_level >= $minimum_user_level_to_add_words)))
{
	if( isset($_REQUEST["word"] ) )
	{
		add_word_to_dictionary( $_REQUEST["word"]);
	}
}

?>
