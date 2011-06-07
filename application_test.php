<?php
/****************************************************************************************//**
* \file application_test.php																*
* \brief A high-levelunit test harness for the bmlt_satellite_controller class.				*
* \version 1.0.0																			*
    
    This file is part of the Basic Meeting List Toolbox (BMLT).
    
    Find out more at: http://magshare.org/bmlt
    
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
*							APPLICATION UNIT TESTING HARNESS								*
*																							*
* This file is designed to exercise the controller "driver" class at a much higher level	*
* than the unit_test.php file.																*
********************************************************************************************/

require_once ( 'bmlt_satellite_controller.class.php' );

/// This is the URI to resolve a test root server (remote). Default is the public trunk test (not stable).
define ('U_TEST_REMOTE_URI_1', 'http://bmlt.magshare.net/trunk/main_server' );
define ('U_TEST_REMOTE_URI_2', 'http://bmlt.magshare.net/stable/main_server' );
define ('U_TEST_REMOTE_URI_3', 'http://bmlt.newyorkna.org/main_server/' );

/// If running on localhost, you can specify a local root URI. Comment these out to always use remote.
define ('U_TEST_LOCAL_URI_1', 'http://localhost/magshare.org/public_html/projects/bmlt/trunk/main_server' );
define ('U_TEST_LOCAL_URI_2', 'http://localhost/magshare.org/public_html/projects/bmlt/stable/main_server' );

global	$test_servers;	///< This will hold an array of objects. Each will be a test server.

/****************************************************************************************//**
*	\brief Returns the URI for the test server. The default is the public trunk test server	*
*	and you can specify a local server if running on a localhost machine. In this test, 3	*
*	different URIs can be used, so the caller needs to specify which one. The first two can	*
*	be replaced by local proxies.															*
*																							*
*	NOTE: The caller can override the URI, by providing one in the 'test_uri_1',			*
*	'test_uri_2' or 'test_uri_3' query param (or all 3).									*
*	Example: unit_test.php?test_uri_1=http://bmlt.magshare.net/stable/main_server			*
*																							*
*	\returns A string. The URI to be presented to the class.								*
********************************************************************************************/
function u_test_get_test_server_uri_num ( $in_uri_number = 1	///< The number of the URI you want returned (1-3, and 4 is the first URI, over again.)
										)
{
	$local = null;
	$ret = null;
	$override_uri = null;
	
	// First, establish our working URIs.
	switch ( $in_uri_number )
		{
		case	1:
		case	4:
			$ret = U_TEST_REMOTE_URI_1;	// We return the remote by default.
			$local = ( defined ( 'U_TEST_LOCAL_URI_1' ) ? U_TEST_LOCAL_URI_1 : null);
			$override_uri = (isset ( $_GET['test_uri_1'] ) && $_GET['test_uri_1']) ? $_GET['test_uri_1'] : null;	// The caller can specify a URI that will override the presets.
		break;
		
		case	2:
			$ret = U_TEST_REMOTE_URI_2;	// We return the remote stable.
			$local = ( defined ( 'U_TEST_LOCAL_URI_2' ) ? U_TEST_LOCAL_URI_2 : null);
			$override_uri = (isset ( $_GET['test_uri_2'] ) && $_GET['test_uri_2']) ? $_GET['test_uri_2'] : null;	// The caller can specify a URI that will override the presets.
		break;
		
		case	3:
			$ret = U_TEST_REMOTE_URI_3;	// We return the remote by default.
			$override_uri = (isset ( $_GET['test_uri_3'] ) && $_GET['test_uri_3']) ? $_GET['test_uri_3'] : null;	// The caller can specify a URI that will override the presets.
		break;
		}
	
	// If the user has supplied a URI, then that is always used.
	if ( $override_uri )
		{
		$ret = $override_uri;
		}
	elseif ( $local && preg_match ( '|localhost|', $_SERVER['SERVER_NAME'] ) ) // If we are running local, and have a local URI, we return that, instead.
		{
		$ret = $local;
		}
	
	return $ret;
}

/****************************************************************************************//**
*	\brief Instantiates the three server instances we'll be using for this test.			*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_instantiate_servers()
{
	$ret = '<h1>Instantiate 4 Instances for 3 different servers (The 4th instance points to the first server)</h1>';
	$ret .= '<div class="test_container_div">';
	global	$test_servers;
	
	for ( $c = 0; $c < 4; $c++ )
		{
		$uri = u_test_get_test_server_uri_num ( $c + 1 );
		$start_time = microtime(true);
		$test_servers[$c] = new bmlt_satellite_controller ( $uri );
		if ( $test_servers[$c] instanceof bmlt_satellite_controller )
			{
			$error = $test_servers[$c]->get_m_error_message();
			$end_time = microtime(true);
			
			if ( !$error )
				{
				$uri = $test_servers[$c]->get_m_root_uri();
				$total_time = intval (($end_time * 1000.0) - ($start_time * 1000.0));
				if ( $total_time > 1000 )
					{
					$total_time /= 1000.0;
					$total_time = htmlspecialchars ( $total_time ).' seconds';
					}
				else
					{
					$total_time = htmlspecialchars ( $total_time ).' milliseconds';
					}
				$ret .= '<h2>Instantiation of server '.strval ( $c + 1).' succeeded for the URI "'.htmlspecialchars( $uri ).'", and took '.htmlspecialchars ( $total_time ).' to complete.</h2>';
				}
			else
				{
				$ret .= '<h2>Instantiation of server '.strval ( $c + 1).' reported the following error: '.htmlspecialchars ( $error ).'</h2>';
				}
			}
		}
	
	$ret .= '</div>';
	return $ret;
}

/****************************************************************************************//**
*	\brief Looks for Monday day meetings around SoHo, in New York							*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_monday_in_soho()
{
	global	$test_servers;

	$ret = '<h1>Use the First Server to Search for Monday Day Meetings (7AM - 5:30PM), Around Central Park, in Manhattan, NY</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );
	// The first two set up a location (SoHo, New York)
	$test_servers[0]->set_current_transaction_parameter ( 'SearchString', 'Central Park, Manhattan, NY' );
	$error_message = $test_servers[0]->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		$test_servers[0]->set_current_transaction_parameter ( 'StringSearchIsAnAddress', true );
		$error_message = $test_servers[0]->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		else
			{
			// This specifies Monday (2)
			$test_servers[0]->set_current_transaction_parameter ( 'weekdays', 2 );
			$error_message = $test_servers[0]->get_m_error_message();
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				// We want meetings that start after 7AM
				$test_servers[0]->set_current_transaction_parameter ( 'StartsAfterH', 7 );
				$error_message = $test_servers[0]->get_m_error_message();
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					// We want meetings that start before 5:30PM
					$test_servers[0]->set_current_transaction_parameter ( 'StartsBeforeH', 17 );
					$error_message = $test_servers[0]->get_m_error_message();
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						// We want meetings that start before 5:30PM
						$test_servers[0]->set_current_transaction_parameter ( 'StartsBeforeM', 30 );
						$error_message = $test_servers[0]->get_m_error_message();
						if ( $error_message )
							{
							$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
							}
						else
							{
							$search_result = $test_servers[0]->meeting_search();
							$error_message = $test_servers[0]->get_m_error_message();
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
										$ret .= u_test_application_draw_meeting ( $meeting );
										}
									
									$ret .= '</div>';
									}
					
								$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] );
								}
							}
						}
					}
				}
			}
		}
	
	$ret .= '</div>';
	return $ret;
}

/****************************************************************************************//**
*	\brief Looks for Saturday evening meetings around The Hamptons, in New York				*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_weekend_at_bernies()
{
	global	$test_servers;

	$ret = '<h1>Use the Fourth (Actually, the First) Server to Search for Friday and Saturday Evening Meetings (After 6PM), Within 15 Miles of Bridgehampton, in Eastern Long Island, NY</h1>';
	$ret .= '<div class="test_container_div">';
	
	$start_time = microtime ( true );
	// The first two set up a location (SoHo, New York)
	$test_servers[3]->set_current_transaction_parameter ( 'SearchString', 'Bridgehampton, NY' );
	$error_message = $test_servers[3]->get_m_error_message();
	if ( $error_message )
		{
		$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
		}
	else
		{
		$test_servers[3]->set_current_transaction_parameter ( 'StringSearchIsAnAddress', true );
		$error_message = $test_servers[3]->get_m_error_message();
		if ( $error_message )
			{
			$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
			}
		else
			{
			// We want meetings within 15 miles of the place.
			$test_servers[3]->set_current_transaction_parameter ( 'SearchStringRadius', 15 );
			$error_message = $test_servers[3]->get_m_error_message();
			if ( $error_message )
				{
				$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
				}
			else
				{
				// This specifies Friday (6) and Saturday (7)
				$test_servers[3]->set_current_transaction_parameter ( 'weekdays', array ( 6, 7 ) );
				$error_message = $test_servers[3]->get_m_error_message();
				if ( $error_message )
					{
					$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
					}
				else
					{
					// We want meetings that start after 6PM
					$test_servers[3]->set_current_transaction_parameter ( 'StartsAfterH', 18 );
					$error_message = $test_servers[3]->get_m_error_message();
					if ( $error_message )
						{
						$ret .= 'The test failed, because the object reported the following error: "'.htmlspecialchars ( $error_message ).'"';
						}
					else
						{
						$search_result = $test_servers[3]->meeting_search();
						$error_message = $test_servers[3]->get_m_error_message();
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
									$ret .= u_test_application_draw_meeting ( $meeting );
									}
								
								$ret .= '</div>';
								}
				
							$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] );
							}
						}
					}
				}
			}
		}
	
	$ret .= '</div>';
	return $ret;
}

/****************************************************************************************//**
*	\brief Displays one meeting in a hierarchical fashion.									*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_application_draw_meeting ( $in_meeting_array
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
*	\brief Tests usage of the class in a serialized (persistent) manner. The two objects	*
*	are serialized, unserialized to new objects, and then the new objects are used in the	*
*	same tests as above. We do this by simply re-executing meeting searches.				*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_application_persistency_test()
{
	$ret = '<h1>We have killed, then resurrected, the first object, and will re-use its corpse for a repeat of the first search.</h1>';
	$ret .= '<div class="test_container_div">';
	
	global $test_servers;
	
	$start_time = microtime ( true );
	$ts3_serialized = serialize ( $test_servers[0] );	// OK, we have serialized the object.
	$test_servers[0] = null;							// Scrag the object. It is dead.
	$test_servers[0] = unserialize ( $ts3_serialized );	// They LAUGHED at me at Heidleberg! They said I was mad, MAD! BWUHHAAAAAHAAAAA!
	
	// OK. Notice no setup, no nothing. Just straight to the meeting search. Do not pass Go. Do not collect $200.
	$search_result = $test_servers[0]->meeting_search();
	$error_message = $test_servers[0]->get_m_error_message();
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
				$ret .= u_test_application_draw_meeting ( $meeting );
				}
			
			$ret .= '</div>';
			}

		$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] );
		}
	
	$ret .= '</div>';
	
	$ret .= '<h1>We have killed, then resurrected, the last object, and will re-use its corpse for a repeat of the last search.</h1>';
	$ret .= '<div class="test_container_div">';
	
	global $test_servers;
	
	$start_time = microtime ( true );
	$ts3_serialized = serialize ( $test_servers[3] );	// OK, we have serialized the object.
	$test_servers[3] = null;							// Scrag the object. It is dead.
	$test_servers[3] = unserialize ( $ts3_serialized );	// They LAUGHED at me at Heidleberg! They said I was mad, MAD! BWUHHAAAAAHAAAAA!
	
	// OK. Notice no setup, no nothing. Just straight to the meeting search. Do not pass Go. Do not collect $200.
	$search_result = $test_servers[3]->meeting_search();
	$error_message = $test_servers[3]->get_m_error_message();
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
				$ret .= u_test_application_draw_meeting ( $meeting );
				}
			
			$ret .= '</div>';
			}

		$ret .= '<h3>The query URI was: '.htmlspecialchars ( $search_result['uri'] );
		}
	
	$ret .= '</div>';
	
	/*
		How do you like them apples, eh? The really neat thing about this test, is that there was zero time spent
		polling the server for the info. It came already set in the object. That saved us a lot of time.
	*/
	return $ret;
}

/****************************************************************************************//**
*	\brief Runs the unit tests.																*
*																							*
*	\returns A string. The XHTML to be displayed.											*
********************************************************************************************/
function u_test_application()
{
	// We return a fully-qualified XHTML 1.0 Strict page.
	$ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>bmlt_satellite_controller Application-Level Unit Test</title>';
	$ret .= '<style type="text/css">';
	$ret .= '*{margin:0;padding:0}';
	$ret .= 'body{font-family:Courier;font-size:small}';
	$ret .= '.test_container_div{padding-left:20px}';
	$ret .= '</style>';
	$ret .= '</head><body>';
	
	//######################################
	//### 	START APPLICATION UNIT TEST    #
	//######################################
	
	$ret .= '<h1>BEGIN UNIT TEST</h1>';
	$ret .= '<div class="test_container_div">';
	// The first thing that we do is create three different connections to 3 different servers, and 1 separate connection to the first server (2 connections to the same server).
	$ret .= u_test_instantiate_servers();
	/*
		OK. At this point, we have 3 separate servers connected and instantiated, with one server accessed by two connections.
		What is really cool, is that each connection is COMPLETELY INDEPENDENT of other connections. Even the two connections
		to the same server (0 & 3) don't know -or care- about each other. You can use these to execute two completely different searches
		on the same database, like looking for Monday day meetings in SoHo, and Friday and Saturday evening meetings in the Hamptons.
		Now, we start to use these connections.
		I'm assuming that all these servers cover the same geographic area, so the following searches will be based on that assumption.
	*/
	
	$ret .= u_test_monday_in_soho();
	$ret .= u_test_weekend_at_bernies();
	$ret .= u_test_application_persistency_test();
	
	$ret .= '</div>';
	$ret .= '<h1>END UNIT TEST</h1>';
	
	//######################################
	//### 	 END APPLICATION UNIT TEST     #
	//######################################

	$ret .= '</body></html>';	// Wrap up the page.
	
	return $ret;
}

/********************************************************************************************
*										UNIT TESTING MAIN									*
/*******************************************************************************************/

// This calls the unit test.
echo u_test_application();
?>