<?php
/****************************************************************************************//**
* \file unit_test.php																		*
* \brief A unit test harness for the bmlt_satellite_controller class.						*
* \version 1.0.2																			*
    
    This file is part of the Basic Meeting List Toolbox (BMLT).
    
    Find out more at: http://bmlt.magshare.org
    
    BMLT is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    BMLT is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this code.  If not, see <http://www.gnu.org/licenses/>.
********************************************************************************************/

/********************************************************************************************
*										UNIT TESTING HARNESS								*
*																							*
* This code is used for testing the class by allowing a direct call of the file. It will be	*
* disabled in actual implementation, so calls to the file will return nothing.				*
********************************************************************************************/

require_once ( 'bmlt_satellite_controller.class.php' );

/// This is the URI to resolve a test root server (remote). Default is the public trunk test (not stable).
define ('U_TEST_REMOTE_URI', 'http://bmlt.magshare.net/trunk/main_server' );

/// If running on localhost, you can specify a local root URI. Comment this out to always use remote.
define ('U_TEST_LOCAL_URI', 'http://localhost/test/bmlt_trunk' );

/// This is an ID for a specific meeting (with some changes) for the meeting changes test.
define ( 'U_TEST_MEETING_ID', 734 );

/****************************************************************************************//**
*	\brief Returns the URI for the test server. The default is the public trunk test server	*
*	and you can specify a local server if running on a localhost machine.					*
*																							*
*	NOTE: The caller can override the URI, by providing one in the 'test_uri' query param.	*
*	Example: unit_test.php?test_uri=http://bmlt.magshare.net/stable/main_server				*
*																							*
*	\returns A string. The URI to be presented to the class.								*
********************************************************************************************/
function u_test_get_test_server_uri ( )
{
	// First, establish our working URIs.
	$ret = U_TEST_REMOTE_URI;	// We return the remote by default.
	$local = ( defined ( 'U_TEST_LOCAL_URI' ) ? U_TEST_LOCAL_URI : null);

	// If we are running local, and have a local URI, we return that, instead.
	if ( $local && preg_match ( '|localhost|', $_SERVER['SERVER_NAME'] ) )
		{
		$ret = $local;
		}
	
	$override_uri = (isset ( $_GET['test_uri'] ) && $_GET['test_uri']) ? $_GET['test_uri'] : null;	// The caller can specify a URI that will override the presets.
	
	if ( $override_uri )
		{
		$ret = $override_uri;
		}
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Apply the URI, and see if the server is there (can also test for bad URI)		*
*																							*
*	\returns A string. The XHTML to be displayed (Can be an error message).					*
********************************************************************************************/
function u_test_apply_root_uri( &$in_test_subject,	///< The object to be tested.
								$in_root_server_uri	///< A string. The root server URI.
								)
{
	$ret = '<h3>URI Test</h3>';
	$ret .= '<div class="test_container_div">';
	$in_test_subject->set_m_root_uri ( $in_root_server_uri );
	$in_root_server_uri = $in_test_subject->get_m_root_uri ( );
	$ret .= 'The object has the following URI set: ';
	$ret .= htmlspecialchars ( $in_test_subject->get_m_root_uri() );
	$server_version = $in_test_subject->get_server_version();
	$error_message = $in_test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= '<br />The object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		$ret .= '<br />The server reports that its version is '.htmlspecialchars ( $server_version ).'<br />Test Passed!';
		}
	$ret .= '</div>';
	return $ret;
}

/****************************************************************************************//**
*	\brief Apply the URI, and see if the server is there (can also test for bad URI)		*
*																							*
*	\returns A string. The XHTML to be displayed (Can be an error message).					*
********************************************************************************************/
function u_test_get_standard_server_parameters ( &$in_test_subject,	///< The object to be tested.
												$in_root_server_uri	///< A string. The root server URI.
												)
{
	$ret = '<h1 id="standard_query_test"># BEGIN STANDARD SERVER QUERY UNIT TESTS</h1>';
	$ret .= '<div class="test_container_div">';
		$ret .= '<h3>Standard Server Parameters Test</h3>';
		$in_test_subject->set_m_root_uri ( $in_root_server_uri );
		$ret .= '<div class="test_container_div">';
		
			$server_formats = $in_test_subject->get_server_formats();
			$error_message = $in_test_subject->get_m_error_message();
			if ( $error_message )
				{
				$ret .= '<br />The object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$server_langs_ret = $in_test_subject->get_server_langs();
				$server_langs = '';
				$def_lang = '';
				foreach ( $server_langs_ret as $lang )
					{
					if ( $server_langs )
						{
						$server_langs .= ', ';
						}
					$server_langs .= $lang['name'];
					
					if ( $lang['default'] )
						{
						$server_langs .= ' (default language)';
						if ( isset ( $lang['key'] ) )
							{
							$def_lang = $lang['key'];
							}
						}
					}
				$error_message = $in_test_subject->get_m_error_message();
				if ( $error_message )
					{
					$ret .= '<br />The object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					$server_service_bodies = $in_test_subject->get_server_service_bodies();
					$error_message = $in_test_subject->get_m_error_message();
					if ( $error_message )
						{
						$ret .= '<br />The object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						$server_meeting_keyss = $in_test_subject->get_server_meeting_keys();
						$error_message = $in_test_subject->get_m_error_message();
						if ( $error_message )
							{
							$ret .= '<br />The object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
							}
						else
							{
							$ret .= '<h4>The server at '.htmlspecialchars ( $in_root_server_uri ).' supports the following languages:</h4><div class="test_container_div">'.htmlspecialchars ( $server_langs );
							$ret .= '</div><h4>The server at '.htmlspecialchars ( $in_root_server_uri ).' supports the following formats:</h4>';
							foreach ( $server_formats as $id => $format )
								{
								$ret .= '<div class="test_container_div"><strong>'.htmlspecialchars ( $id ).' '.htmlspecialchars ($format['key_string']).'</strong> ('.htmlspecialchars ($format['description_string']).')</div>';
								}
							$ret .= '<h4>The server at '.htmlspecialchars ( $in_root_server_uri ).' has the following Service bodies:</h4><div class="test_container_div">';
							foreach ( $server_service_bodies as $sb )
								{
								$ret .= u_test_draw_service_body ( $sb );
								}
							$ret .= '</div><h4>The server at '.htmlspecialchars ( $in_root_server_uri ).' has the following Meeting Keys:</h4>';
							foreach ( $server_meeting_keyss as $key )
								{
								$ret .= '<div class="test_container_div">'.htmlspecialchars ($key).'</div>';
								}
							}
						}
					}
				}
		$ret .= '</div>';
	$ret .= '</div>';
	$ret .= '<h1># END STANDARD SERVER QUERY UNIT TESTS</h1>';
	return $ret;
}

/****************************************************************************************//**
*	\brief Displays Service bodies in a hierarchical fashion.								*
*																							*
*	\returns A string. The XHTML to be displayed (Can be an error message).					*
********************************************************************************************/
function u_test_draw_service_body ( $in_service_body_array
									)
{
	$ret = '<div class="name_div"><strong>'.htmlspecialchars ( $in_service_body_array['name'] ).'</strong></div>';
	$ret .= '<div class="test_container_div">';
        $ret .= '<div class="type_div">'.htmlspecialchars ( $in_service_body_array['type'] ).'</div>';
        $ret .= '<div class="desc_div">'.htmlspecialchars ( $in_service_body_array['description'] ).'</div>';
        $ret .= '<div class="uri_div">'.htmlspecialchars ( $in_service_body_array['uri'] ).'</div>';
        $ret .= '<div class="kmluri_div">'.htmlspecialchars ( $in_service_body_array['kmluri'] ).'</div>';
        if ( isset ( $in_service_body_array['children'] ) && is_array ( $in_service_body_array['children'] ) && count ( $in_service_body_array['children'] ) )
            {
            $ret .= '<div class="test_container_div">';
            foreach ( $in_service_body_array['children'] as $child )
                {
                $ret .= u_test_draw_service_body ( $child );
                }
            $ret .= '</div>';
            }
        $ret .= '</div>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Tests the various ways the class deals with the root URI	. This will also		*
*	exercise the cURL calling and server communication.										*
*																							*
*	\returns A string. The XHTML to be displayed (Can be an error message).					*
********************************************************************************************/
function u_test_server_root_uri( &$in_test_subject,	///< The object to be tested.
								$in_uri				///< The base URI.
								)
{
	$ret = '<h1 id="root_uri_accessor_test"># BEGIN ROOT URI ACCESSOR UNIT TESTS</h1>';
	$ret .= '<div class="test_container_div">';
		// First, simply test that the object saves the URI, and that it can use it to connect to the server.
		$ret .= '<h2>Test With Good URI</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = $in_uri;
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $in_uri );
		$ret .= '</div>';
		
		// Next, make sure that a trailing slash is removed from a given URI.
		$ret .= '<h2>Test With Good URI (With Slash at End)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = $in_uri.'/';
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
		
		// Next, make sure that the default 'http://' is added to the URI.
		$ret .= '<h2>Test With Good URI (With No Protocol Preamble)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = str_replace ( 'http://', '', $in_uri );
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
		
		// This is a typical "Copy and Paste" typo.
		$ret .= '<h2>Test With Bad URI (Typical Typo Mistake -Will be Corrected)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = str_replace ( 'http://', 'ttp://', $in_uri );
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
		
		// This specifies an invalid protocol. It will be replaced by the default HTTP.
		$ret .= '<h2>Test With Bad URI (Invalid Protocol -Will be Corrected)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = str_replace ( 'http://', 'wcal://', $in_uri );
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
		
		// This is a typical "Copy and Paste" typo. This one will not be corrected, as it is merely a missing piece from the end.
		$ret .= '<h2>Test With Bad URI (Typical Typo Mistake -Will Not be Corrected)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = preg_replace ( '|.$|','', $in_uri );
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
		
		// This one is just plain bad.
		$ret .= '<h2>Test With Bad URI (Will Not be Corrected)</h2>';
		$ret .= '<div class="test_container_div">';
		$test_uri = $in_uri.'/monkee-business';
		$ret .= 'We are giving the object the following URI: '.htmlspecialchars ( $test_uri ).'<br />';
		$ret .= u_test_apply_root_uri ( $in_test_subject, $test_uri );
		$ret .= '</div>';
	$ret .= '</div>';
	$ret .= '<h1># END ROOT URI ACCESSOR UNIT TESTS</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Tests the outgoing parameter storage.											*
*																							*
*	\returns A string. The XHTML to be displayed (Can be an error message).					*
********************************************************************************************/
function u_test_server_parameters_outgoing( &$in_test_subject	///< The object to be tested.
											)
{
	$ret = '<h1 id="outgoing_parameter_test"># BEGIN OUTGOING PARAMETER STORAGE UNIT TESTS</h1>';
	$ret .= '<div class="test_container_div">';
		$ret .= '<h2>Place two Values By Two Keys, Then Retrieve Them</h2>';
		$ret .= '<div class="test_container_div">';
			$test_key = 'SearchString';
			$test_val = 'Test Value String 1';
			$in_test_subject->set_m_outgoing_parameter ( $test_key, $test_val );
			$error_message = $in_test_subject->get_m_error_message();
			
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$test_key2 = 'meeting_key_value';
				$test_val2 = 'Test Value String 2';
				$in_test_subject->set_m_outgoing_parameter ( $test_key2, $test_val2 );
				$error_message = $in_test_subject->get_m_error_message();
				
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					$returned_val = $in_test_subject->get_m_outgoing_parameter ( $test_key );
					$error_message = $in_test_subject->get_m_error_message();
					
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						if ( is_string ( $returned_val ) && ( strcmp ( $returned_val, $test_val ) === 0 ) )
							{
							$ret .= 'Test Passed! The object stored and returned the first string correctly.';
							}
						else
							{
							$ret .= "Test Failed! '$returned_val', which was returned from the object, is not '$test_val', which was stored in the object! ($test_key)";
							}
						}
					
					$returned_val2 = $in_test_subject->get_m_outgoing_parameter ( $test_key2 );
					
					$ret .= '<br />';
					
					$error_message = $in_test_subject->get_m_error_message();
					
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						if ( is_string ( $returned_val2 ) && ( strcmp ( $returned_val2, $test_val2 ) === 0 ) )
							{
							$ret .= 'Test Passed! The object stored and returned the second string correctly.';
							}
						else
							{
							$ret .= "Test Failed! '$returned_val2', which was returned from the object, is not '$test_val2', which was stored in the object! ($test_key2)";
							}
						}
					}
				}
		$ret .= '</div>';
		
		$ret .= '<h2>Make Sure That The Errors Are Set for Bad Key Selection</h2>';
		$ret .= '<div class="test_container_div">';
		// At this point, the storage has one element, with a key of $test_key, and a value of $test_val2. We deliberately ask for a bad key.
		
		$returned_val = $in_test_subject->get_m_outgoing_parameter ( 'badz-maru' );
		$error_message = $in_test_subject->get_m_error_message();
		
		if ( $error_message )
			{
			if ( $returned_val === null )
				{
				$ret .= 'The test passed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'", but the following value was returned: "'.htmlspecialchars ( $returned_val ).'"';
				}
			
			// Extra test. Make sure the error message clears.
			
			$in_test_subject->clear_m_error_message ();
			
			if ( $in_test_subject->get_m_error_message() )
				{
				$ret .= '<br />The error message was not cleared! Test fail!';
				}
			}
		else
			{
			$ret .= 'The Test Failed! We were expecting an error!';
			}
		
		// Now, we set a bad key.
		$in_test_subject->set_m_outgoing_parameter ( 'spaz', $test_val );
		$error_message = $in_test_subject->get_m_error_message();
		$ret .= '<br />';
		if ( $error_message )
			{
			$ret .= 'The test passed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			$in_test_subject->clear_m_error_message ();
			}
		else
			{
			$ret .= 'The Test Failed! We were expecting an error!';
			}
		
		// Just make sure we don't get anything returned from a null request.
		$returned_val = $in_test_subject->get_m_outgoing_parameter ( null );
		
		if ( $returned_val !== null )
			{
			$ret .= '<br />The Test Failed! We got data from a null request!';
			}
		$ret .= '</div>';
			
	$ret .= '</div>';
	$ret .= '<h1># END OUTGOING PARAMETER STORAGE UNIT TESTS</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Performs a basic meeting search													*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_basic_search( $in_root_server_uri	///< The root uri of the server to be searched.
							)
{
	$ret = '<h1 id="basic_search_test"># BEGIN BASIC SEARCH UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );
	$test_subject = new bmlt_satellite_controller ( $in_root_server_uri );

	$error_message = $test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		// OK. Very simple search. Look for meetings around Mineola, NY. This will return around 10 meetings.
		$test_subject->set_current_transaction_parameter ( 'SearchString', 'Mineola, NY' );
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		else
			{
			$test_subject->set_current_transaction_parameter ( 'StringSearchIsAnAddress', true );
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$search_result = $test_subject->meeting_search();
				$error_message = $test_subject->get_m_error_message();
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				elseif ( isset ( $search_result ) && is_array ( $search_result ) && count ( $search_result ) )
					{
					$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
					// We now have a meeting search result. Let's display it.
					$ret .= '<h2>Test Passed! (The complete setup and search took ';
					if ( $total_time > 1000 )
						{
						$total_time /= 1000.0;
						$ret .= htmlspecialchars ( $total_time ).' seconds';
						}
					else
						{
						$ret .= htmlspecialchars ( $total_time ).' milliseconds';
						}
					$ret .= ' to run.) Here are the returned meetings:</h2><div class="test_container_div">';
					foreach ( $search_result['meetings'] as $meeting )
						{
						$ret .= u_test_draw_meeting ( $meeting );
						}
					
					$ret .= '</div>';
					$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] ).'<br />(Search for all meetings near Mineola, NY)</h3>';
					}
				else
					{
					$ret .= 'Test Failed! No Meetings Returned!';
					}
				}
			}
		}
	
	$ret .= '</div>';
	$ret .= '<h1># END BASIC SEARCH UNIT TEST</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Performs a more complex meeting search											*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_complex_search( $in_root_server_uri,		///< The root uri of the server to be searched.
								&$serialized_transaction	///< This will hold the serialized transaction for another test.
							)
{
	$ret = '<h1 id="complex_search_test"># BEGIN COMPLEX SEARCH UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );
	$test_subject = new bmlt_satellite_controller ( $in_root_server_uri );
	$error_message = $test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		// OK. Very simple search. Look for meetings around Mineola, NY. This will return around 10 meetings.
		$test_subject->set_current_transaction_parameter ( 'SearchString', 'Bleeker Street at 14th Street, Manhattan, NY' );
		$error_message = $test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		else
			{
			$test_subject->set_current_transaction_parameter ( 'StringSearchIsAnAddress', true );
			$error_message = $test_subject->get_m_error_message();
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$test_subject->set_current_transaction_parameter ( 'weekdays', array ( 1, 2, 3, 4, 5 ) );
				$error_message = $test_subject->get_m_error_message();
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					$test_subject->set_current_transaction_parameter ( 'formats', array ( 1, 17 ) );
					$error_message = $test_subject->get_m_error_message();
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						$test_subject->set_current_transaction_parameter ( 'langs', array ( 'en' ) );
						if ( $error_message )
							{
							$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
							}
						else
							{
							$search_result = $test_subject->meeting_search();
							$error_message = $test_subject->get_m_error_message();
							if ( $error_message )
								{
								$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
								}
							elseif ( isset ( $search_result ) && is_array ( $search_result ) && count ( $search_result ) )
								{
								if ( !isset ( $search_result['meetings'] ) || !is_array ( $search_result['meetings'] ) || !count ( $search_result['meetings'] ) )
									{
									$ret .= '<h2>Test Failed! No meetings were returned!</h2>';
									}
								else
									{
									$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
									// We now have a meeting search result. Let's display it.
									$ret .= '<h2>Test Passed! (The complete setup and search took ';
									if ( $total_time > 1000 )
										{
										$total_time /= 1000.0;
										$ret .= htmlspecialchars ( $total_time ).' seconds';
										}
									else
										{
										$ret .= htmlspecialchars ( $total_time ).' milliseconds';
										}
									$ret .= ' to run.) Here are the returned meetings:</h2><div class="test_container_div">';
									foreach ( $search_result['meetings'] as $meeting )
										{
										$ret .= u_test_draw_meeting ( $meeting );
										}
									
									$ret .= '</div>';
									}

								$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] ).'<br />(Search for open, English-speaking, beginners\' meetings, on Sunday, Monday, Tuesday, Wednesday, or Thursday, near Bleeker Street and 14th Street, in Manhattan)</h3>';
								$serialized_transaction = $search_result['serialized'];
								}
							else
								{
								$ret .= 'Test Failed! No Meetings Returned!';
								}
							}
						}
					}
				}
			}
		}
	
	$ret .= '</div>';
	$ret .= '<h1># END COMPLEX SEARCH UNIT TEST</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Performs a more complex meeting search, but only gets a couple of the fields.	*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_filtered_search( $in_root_server_uri		///< The root uri of the server to be searched.
							)
{
	$ret = '<h1 id="partial_search_test"># BEGIN FILTERED SEARCH UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );
	$test_subject = new bmlt_satellite_controller ( $in_root_server_uri );
	$error_message = $test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		// OK. Very simple search. Look for meetings around Mineola, NY. This will return around 10 meetings.
		$test_subject->set_current_transaction_parameter ( 'SearchString', 'Bleeker Street at 14th Street, Manhattan, NY' );
		$error_message = $test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		else
			{
			$test_subject->set_current_transaction_parameter ( 'StringSearchIsAnAddress', true );
			$error_message = $test_subject->get_m_error_message();
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				$test_subject->set_current_transaction_parameter ( 'data_field_key', 'location_municipality,meeting_name' );
				$error_message = $test_subject->get_m_error_message();
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					$search_result = $test_subject->meeting_search(true);
					$error_message = $test_subject->get_m_error_message();
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					elseif ( isset ( $search_result ) && is_array ( $search_result ) && count ( $search_result ) )
						{
						if ( !isset ( $search_result['meetings'] ) || !is_array ( $search_result['meetings'] ) || !count ( $search_result['meetings'] ) )
							{
							$ret .= '<h2>Test Failed! No meetings were returned!</h2>';
							}
						else
							{
							$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
							// We now have a meeting search result. Let's display it.
							$pass = true;
							foreach ( $search_result['meetings'] as $meeting )
								{
								foreach ( $meeting as $key => $value )
									{
									if ( ($key != 'location_municipality') && ($key != 'meeting_name' ) )
										{
										$ret .= 'The meeting should not have the '.htmlspecialchars ( $key ).' field!!';
										$pass = false;
										break;
										}
									}
								}
							
							if ( $pass )
								{
								$ret .= '<h2>Test Passed! (The complete setup and search took ';
								if ( $total_time > 1000 )
									{
									$total_time /= 1000.0;
									$ret .= htmlspecialchars ( $total_time ).' seconds';
									}
								else
									{
									$ret .= htmlspecialchars ( $total_time ).' milliseconds';
									}
								$ret .= ' to run.).';
								}
							else
								{
								$ret .= 'Test Failed! The meeting data had extra fields!';
								}
							}
	
						$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] ).'</h3>';
						}
					else
						{
						$ret .= 'Test Failed! No Meetings Returned!';
						}
					}
				}
			}
		}
	
	$ret .= '</div>';
	$ret .= '<h1># END FILTERED SEARCH UNIT TEST</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Performs a more complex meeting search											*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_serialized_search ( $in_test_subject,		///< An initialized object.
									$serialized_transaction	///< This has the serialized transaction for the test.
									)
{
	$ret = '<h1 id="serialized_search_test"># BEGIN SERIALIZED SEARCH UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );

	$in_test_subject->apply_serialized_transaction ( $serialized_transaction );
	$error_message = $in_test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		$search_result = $in_test_subject->meeting_search();
		$error_message = $in_test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		elseif ( isset ( $search_result ) && is_array ( $search_result ) && count ( $search_result ) )
			{
			if ( !isset ( $search_result['meetings'] ) || !is_array ( $search_result['meetings'] ) || !count ( $search_result['meetings'] ) )
				{
				$ret .= '<h2>Test Failed! No meetings were returned!</h2>';
				}
			else
				{
				$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
				// We now have a meeting search result. Let's display it.
				$ret .= '<h2>Test Passed! (The complete search took ';
				if ( $total_time > 1000 )
					{
					$total_time /= 1000.0;
					$ret .= htmlspecialchars ( $total_time ).' seconds';
					}
				else
					{
					$ret .= htmlspecialchars ( $total_time ).' milliseconds';
					}
				$ret .= ' to run.) Here are the returned meetings:</h2><div class="test_container_div">';
				foreach ( $search_result['meetings'] as $meeting )
					{
					$ret .= u_test_draw_meeting ( $meeting );
					}
				
				$ret .= '</div>';
				}

			$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] );
			}
		else
			{
			$ret .= 'Test Failed! No Meetings Returned!';
			}
		}
	
	$ret .= '</div>';
	$ret .= '<h1># END SERIALIZED SEARCH UNIT TEST</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Displays one meeting in a hierarchical fashion.									*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_draw_meeting ( $in_meeting_array
							)
{
	$ret = '<div class="name_div"><h3>'.htmlspecialchars ( $in_meeting_array['meeting_name'] ).' ('.htmlspecialchars ( $in_meeting_array['id_bigint'] ).')</h3>';
	foreach ( $in_meeting_array as $key => $value )
		{
		if ( $key != 'meeting_name' && $key != 'id_bigint' )
			{
			$ret .= '<div class="test_container_div"><strong>'.htmlspecialchars ( $key ).':</strong> '.htmlspecialchars ( $value ).'</div>';
			}
		}
	$ret .= '</div>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Queries the server for changes.													*
*																							*
*	This function requires that the root server be version 1.8.13 or above. It queries the	*
*	server for all the meeting changes that occurred in a six-week window, beginning seven	*
*	weeks ago, and ending one week ago. The changes are returned with the most recent ones	*
*	first, going down to the oldest.														*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_changes ( $in_root_server_uri		///< The server URI.
						)
{
	$ret = null;
	$ret = '<h1 id="change_request_test"># BEGIN CHANGE REQUEST UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	$ret .= '<h2 id="change_request_test"># BEGIN GENERAL CHANGES TEST</h1>';
	
	$start_time = microtime ( true );
	$test_subject = new bmlt_satellite_controller ( $in_root_server_uri );
	$error_message = $test_subject->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		// We will get changes from 7 weeks ago, to 1 week ago (six week period).
		$start_date = time() - (7 * 24 * 60 * 60 * 7);
		$end_date = time() - (7 * 24 * 60 * 60);
		$change_array = $test_subject->get_meeting_changes ( $start_date, $end_date );
		$error_message = $test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		elseif ( is_array ( $change_array ) && count ( $change_array ) )
			{
			$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
			// We now have a meeting search result. Let's display it.
			$ret .= '<h2>Test Passed! (The complete transaction took ';
			if ( $total_time > 1000 )
				{
				$total_time /= 1000.0;
				$ret .= htmlspecialchars ( $total_time ).' seconds';
				}
			else
				{
				$ret .= htmlspecialchars ( $total_time ).' milliseconds';
				}
			$ret .= ' to run.) Here are the meeting changes from '.htmlspecialchars ( date ('F j, Y', $start_date ) ).' to '.htmlspecialchars ( date ('F j, Y', $end_date ) ).':</h2><div class="test_container_div">';
			$ret .= '<pre>'.htmlspecialchars ( print_r ( $change_array, true ) ).'</pre>';
			$ret .= '</div>';
			}
		else
			{
			$ret .= 'The test failed, because the object returned no meeting changes for the given period.';
			}
		}
	$ret .= '<h2># END GENERAL CHANGES TEST</h1>';
	$ret .= '</div>';
	$ret .= '<div class="test_container_div">';
	$ret .= '<h2 id="change_meeting_request_test"># BEGIN SPECIFIC MEETING CHANGES TEST</h1>';
		$change_array = $test_subject->get_meeting_changes ( null, null, U_TEST_MEETING_ID );
		$error_message = $test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		elseif ( is_array ( $change_array ) && count ( $change_array ) )
			{
			$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
			// We now have a meeting search result. Let's display it.
			$ret .= '<h2>Test Passed! (The complete transaction took ';
			if ( $total_time > 1000 )
				{
				$total_time /= 1000.0;
				$ret .= htmlspecialchars ( $total_time ).' seconds';
				}
			else
				{
				$ret .= htmlspecialchars ( $total_time ).' milliseconds';
				}
			$ret .= ' to run.) Here are the meeting changes for this meeting:</h2><div class="test_container_div">';
			$ret .= '<pre>'.htmlspecialchars ( print_r ( $change_array, true ) ).'</pre>';
			$ret .= '</div>';
			}
		else
			{
			$ret .= 'The test failed, because the object returned no meeting changes for the given period.';
			}
	$ret .= '<h2># END SPECIFIC MEETING CHANGES TEST</h1>';
	$ret .= '</div>';
	$ret .= '<h1># END CHANGE REQUEST UNIT TEST</h1>';
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Runs the unit tests.																*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test()
{
	// We return a fully-qualified XHTML 1.0 Strict page.
	$ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>bmlt_satellite_controller Class Unit Test (###-SERVER-URI-###)</title>';
	$ret .= '<style type="text/css">';
	$ret .= '*{margin:0;padding:0}';
	$ret .= 'body{font-family:Courier;font-size:small}';
	$ret .= '.test_container_div{padding-left:20px}';
	$ret .= '</style>';
	$ret .= '</head><body>';

	// Start by getting the main URI
	$uri = u_test_get_test_server_uri();
	
	// The URI goes in the page title.
	$ret = str_replace ( '###-SERVER-URI-###', htmlspecialchars ( $uri ), $ret );
	
	//#######################
	//### 	START TEST		#
	//#######################
	
	$ret .= '<h1>BEGIN UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	
	$ret .= '<strong>USAGE:</strong> Override URI by "test_uri=<em>XXX</em>"<br />';
	$ret .= '<strong>USING:</strong> '.htmlspecialchars ( $uri );

	// The first test we run is to make sure that the parameterized constructor works OK.
	$ret .= '<h1 id="constructor_unit_test"># BEGIN CONSTRUCTOR UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
		// We are just making sure the URI gets in there properly.
		$start_time = microtime ( true );
		$test_subject = new bmlt_satellite_controller ( $uri );
		$total_time = intval ( (microtime ( true ) - $start_time) * 1000 );
		$error_message = $test_subject->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			$ret .= '</div><h1># END CONSTRUCTOR UNIT TEST</h1>';
			}
		else
			{
			$ret .= '<h2>Auto-Setup Constructor Test</h2>';
			$ret .= '<div class="test_container_div">The initialization (setting up the object) took ';
			if ( $total_time > 1000 )
				{
				$total_time /= 1000.0;
				$ret .= htmlspecialchars ( $total_time ).' seconds';
				}
			else
				{
				$ret .= htmlspecialchars ( $total_time ).' milliseconds';
				}
			
			$ret .= ' to run.<br />The object has the following URI set via the constructor: ';
			$ret .= htmlspecialchars ( $test_subject->get_m_root_uri() );
			// These all need to be already loaded, so we access the stored parameters.
			$server_version = $test_subject->get_server_version();
			$server_langs = $test_subject->get_m_outgoing_parameter('langs');
			$server_formats = $test_subject->get_m_outgoing_parameter('formats');
			$server_services = $test_subject->get_m_outgoing_parameter('services');
			$server_meeting_key = $test_subject->get_m_outgoing_parameter('meeting_key');
			
			if ( $server_version && is_array ( $server_langs ) && is_array ( $server_formats ) && is_array ( $server_services ) && is_array ( $server_meeting_key ) )
				{
				$ret .= '<br />The test passed, because all the data items were set up and cached.';
				}
			else
				{
				$ret .= '<br />The test failed, because not all the data items were cached.';
				}
			
			$ret .= '</div>';
		$ret .= '</div>';
		$ret .= '<h1># END CONSTRUCTOR UNIT TEST</h1>';
		
		// After that, we relase the original object, and instantiate a new "blank slate" object.
		$test_subject = null;
		
		// We will test the various accessors, here.
		$test_subject = new bmlt_satellite_controller;
		
		if ( $test_subject )
			{
			// First, we test the various ways the class deals with the root server URI.
			$ret .= u_test_server_root_uri ( $test_subject, $uri );
			$ret .= u_test_server_parameters_outgoing ( $test_subject );
			$ret .= u_test_get_standard_server_parameters ( $test_subject, $uri );
			$ret .= u_test_basic_search ( $uri );
			$ret .= u_test_filtered_search ( $uri );
			$serialized_transaction = null;
			$ret .= u_test_complex_search ( $uri, $serialized_transaction );
			$ret .= u_test_serialized_search ( $test_subject, $serialized_transaction );
			$ret .= u_test_changes ( $uri );
			}
		else
			{
			$ret .= 'FAIL: Cannot instantiate bmlt_satellite_controller object!';
			}
		$ret .= '</div>';
		}
	
	$ret .= '<h1>END UNIT TEST</h1>';
	
	//#######################
	//### 	END TEST		#
	//#######################

	$ret .= '</body></html>';	// Wrap up the page.
	
	return $ret;
}

/********************************************************************************************
*										UNIT TESTING MAIN									*
/*******************************************************************************************/

// This calls the unit test.
echo u_test();
?>