<?php

/*
OPT Awards Plugin for MyBB
Copyright (C) 2013 Dieter Gobbers aka Terran

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/* Exported by Hooks plugin Mon, 02 Sep 2013 09:26:22 GMT */

if ( !defined( 'IN_MYBB' ) )
{
	die( 'Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.' );
}

if ( !defined( "PLUGINLIBRARY" ) )
{
	define( "PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php" );
}

/* --- Plugin API: --- */

function optawards_info()
{
	return array(
		 'name' => 'OPT Awards',
		'description' => 'An Advanced Award System',
		'website' => 'http://opt-community.de/',
		'author' => 'Dieter Gobbers (@Terran_ulm)',
		'authorsite' => 'http://opt-community.de/',
		'version' => '1.0',
		'guid' => '',
		'compatibility' => '16*' 
	);
}

function optawards_activate()
{
	if ( !file_exists( PLUGINLIBRARY ) )
	{
		flash_message( "PluginLibrary is missing.", "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}
	
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	if ( $PL->version < 12 )
	{
		flash_message( "PluginLibrary is too old: " . $PL->version, "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}

	global $db, $lang, $cache;
	
	$lang->load('optawards');
	
	optawards_deactivate();

	// activate stylesheet
	optawards_setup_stylessheet();
	
	// Modify some templates.
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('postbit', '#'.preg_quote('{$post[\'user_details\']}').'#', '{$post[\'user_details\']}{$post[\'optawards\']}');
	find_replace_templatesets('postbit_classic', '#'.preg_quote('{$post[\'user_details\']}').'#', '{$post[\'user_details\']}{$post[\'optawards\']}');
	find_replace_templatesets('member_profile', '#'.preg_quote('{$signature}').'#', '{$signature}{$memprofile[\'optawards\']}');
	
	$result = $db->update_query("tasks", array("enabled" => intval(1)), "title='".$db->escape_string($lang->optawards_title)."'");
	$cache->update_tasks();

	change_admin_permission('tools','optawards');
}

function optawards_deactivate()
{
	if ( !file_exists( PLUGINLIBRARY ) )
	{
		flash_message( "PluginLibrary is missing.", "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}
	
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	if ( $PL->version < 12 )
	{
		flash_message( "PluginLibrary is too old: " . $PL->version, "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}

	global $db, $lang, $cache;
	
	$lang->load('optawards');
	
	$PL->stylesheet_deactivate('optawards');
	
	// Remove added variables.
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('postbit', '#'.preg_quote('{$post[\'optawards\']}').'#', '', 0);
	find_replace_templatesets('postbit_classic', '#'.preg_quote('{$post[\'optawards\']}').'#', '', 0);
	find_replace_templatesets('member_profile', '#'.preg_quote('{$memprofile[\'optawards\']}').'#', '', 0);

	$result = $db->update_query("tasks", array("enabled" => intval(0)), "title='".$db->escape_string($lang->optawards_title)."'");
	$cache->update_tasks();

	change_admin_permission('tools','optawards', -1);
}

function optawards_is_installed()
{
	
	if ( !file_exists( PLUGINLIBRARY ) )
	{
		flash_message( "PluginLibrary is missing.", "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}
	
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	if ( $PL->version < 12 )
	{
		flash_message( "PluginLibrary is too old: " . $PL->version, "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}

	global $db;

	// setup some helper functions
	function optawards_settinggroups_defined($settinggroup)
	{
		global $db;
		$query  = $db->simple_select( 'settinggroups', '*', 'name="'.$db->escape_string($settinggroup).'"' );
		$result = $db->fetch_array( $query );
		$db->free_result( $query );
		return (!empty($result));
	}
	
	function optawards_setting_defined($setting)
	{
		global $db;
		$query  = $db->simple_select( 'settings', '*', 'name="'.$db->escape_string($setting).'"' );
		$result = $db->fetch_array( $query );
		$db->free_result( $query );
		return (!empty($result));
	}

	// definitions:
	$settinggroups=array(
		'optawards_admin',
		'optawards_display'
	);
	$settings=array(
		'optawards_display_profile',
		'optawards_display_postbit',
		'optawards_admin_pm_deny',
		'optawards_admin_pm_default',
		'optawards_admin_pmuserid',
		'optawards_admin_pmuser',
		'optawards_admin_pmicon',
		'optawards_admin_granters'
	);
	$tables=array(
		'awards',
		'awards_granted',
		'award_classes',
		'award_requests'
	);
	
	// now check if the DB is setup
	$is_installed=true;
	foreach($settinggroups as $settinggroup)
	{
		if (!optawards_settinggroups_defined($settinggroup)) {
			$is_installed=false;
		}
	}
	foreach($settings as $setting)
	{
		if (!optawards_setting_defined($setting))
		{
			$is_installed=false;
		}
	}
	foreach($tables as $table)
	{
		if (!$db->table_exists($table))
		{
			$is_installed=false;
		}
	}
	
	return $is_installed;
}

function optawards_install()
{
	if ( !file_exists( PLUGINLIBRARY ) )
	{
		flash_message( "PluginLibrary is missing.", "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}
	
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	if ( $PL->version < 12 )
	{
		flash_message( "PluginLibrary is too old: " . $PL->version, "error" );
		admin_redirect( "index.php?module=config-plugins" );
	}
	
	global $db, $lang, $cache;
	
	$lang->load('optawards');
	
	$myplugin = optawards_info();
	
	// create ACP settings
	{
		$PL->settings( 'optawards_admin', $myplugin[ 'name' ].' Administration', $myplugin[ 'description' ].'. Configure the Award System Admin Settings.', array(
			 'pmuser' => array(
				'title' => 'Current User as PM Author',
				'description' => $lang->optawards_pmuser_description,
				'optionscode' => 'yesno',
				'value' => $lang->optawards_pmuser_defaults
			),
			'pmuserid' => array(
				'title' => 'PM UserID',
				'description' => $lang->optawards_pmuserid_description,
				'optionscode' => 'text',
				'value' => $lang->optawards_pmuserid_defaults
			),
			'pmicon' => array(
				'title' => 'PM Icon',
				'description' => $lang->optawards_pmicon_description,
				'optionscode' => 'text',
				'value' => $lang->optawards_pmicon_defaults
			),
			'pm_default' => array(
				'title' => 'Default PM message template',
				'description' => $lang->optawards_pm_default_description,
				'optionscode' => 'textarea',
				'value' => $lang->optawards_pm_default_defaults
			),
			'granters' => array(
				'title' => 'Which groups can manage the awards?',
				'description' => $lang->optawards_granters_description,
				'optionscode' => 'text',
				'value' => $lang->optawards_granters_defaults
			),
			'pm_deny' => array(
				'title' => 'PM message template for denied Awards',
				'description' => $lang->optawards_pm_deny_description,
				'optionscode' => 'textarea',
				'value' => $lang->optawards_pm_deny_defaults
			) 
		) );
		$PL->settings( 'optawards_display', $myplugin[ 'name' ].' Display Settings', $myplugin[ 'description' ].'. Configure how the awards are displayed.', array(
			 'profile' => array(
				'title' => 'Maximum Awards in Profile',
				'description' => $lang->optawards_profile_description,
				'optionscode' => 'text',
				'value' => $lang->optawards_profile_defaults
			),
			'postbit' => array(
				'title' => 'Maximum Awards in Posts',
				'description' => $lang->optawards_postbit_description,
				'optionscode' => 'text',
				'value' => $lang->optawards_postbit_defaults
			) 
		) );
	}
	
	// tables definition statements
	{
		$create_table_award_classes = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."award_classes` (
			`acid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Award Class ID',
			`name` varchar(255) NOT NULL COMMENT 'Name of the Award Class, used for multiple Awards',
			`singular` varchar(255) NOT NULL COMMENT 'Name of the Award Class for single Awards',
			`description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Description of the Award Class',
			`icon` varchar(255) NOT NULL DEFAULT '' COMMENT 'Award Class Icon (optional)',
			`displayorder` int(10) unsigned NOT NULL,
			PRIMARY KEY (`acid`),
			UNIQUE KEY `name` (`name`),
			UNIQUE KEY `singular` (`singular`),
			KEY `displayorder` (`displayorder`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Available Award Classes'";
		$create_table_awards = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards` (
			`aid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Award ID',
			`name` varchar(255) NOT NULL COMMENT 'Award Name',
			`description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Description of the Awards',
			`acid` int(11) NOT NULL COMMENT 'Award Class ID',
			`icon` varchar(255) NOT NULL COMMENT 'Award Icon URL',
			`iconlarge` varchar(255) NOT NULL COMMENT 'Large Award Icon URL',
			`usergroups` varchar(255) NOT NULL COMMENT 'the Award can be requested by those usergroups (everyone can be the receipient)',
			`visibility` int(11) NOT NULL DEFAULT '1' COMMENT 'Award visibility: 1 - everywhere, 2 - UserCP only, 3 Postbit only, 4 invisible',
			`displayorder` int(11) NOT NULL,
			`pm` varchar(255) NOT NULL DEFAULT '{default}' COMMENT 'PM template',
			`max_times` int(11) NOT NULL DEFAULT '-1' COMMENT 'how many times can a user receive the award? -1 = no limit',
			`recipients` int(11) NOT NULL DEFAULT '0' COMMENT 'how many users received this award?',
			PRIMARY KEY (`aid`),
			UNIQUE KEY `name` (`name`),
			KEY `displayorder` (`displayorder`),
			KEY `acid` (`acid`),
			KEY `visibility` (`visibility`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='available Awards'";
		$alter_table_awards = "ALTER TABLE `".TABLE_PREFIX."awards`
			ADD CONSTRAINT `".TABLE_PREFIX."awards_ibfk_1` FOREIGN KEY (`acid`) REFERENCES `".TABLE_PREFIX."award_classes` (`acid`)";
		$create_table_award_requests = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."award_requests` (
			`arid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'request id',
			`aid` int(11) NOT NULL COMMENT 'award id',
			`requested_for` int(10) unsigned NOT NULL COMMENT 'uid of the receipient of the award',
			`action_requested` enum('grant','revoke') NOT NULL COMMENT 'grant or revoke award?',
			`reason` varchar(255) NOT NULL DEFAULT 'no reason given' COMMENT 'why should the award be given?',
			`date_requested` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when was the request placed?',
			`requested_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid of who placed the award request',
			`last_processed_by` int(10) unsigned DEFAULT NULL COMMENT 'uid of who processed the award request the last time',
			`status` enum('new','accepted','rejected','deferred','retired','duplicate','granted','revoked','failed') NOT NULL DEFAULT 'new' COMMENT 'processing status of the request',
			`date_last_processed` timestamp NULL DEFAULT NULL COMMENT 'when was the request processed the last time?',
			`agid` int(10) unsigned DEFAULT NULL COMMENT 'award given id',
			PRIMARY KEY (`arid`),
			KEY `aid` (`aid`),
			KEY `requested_for` (`requested_for`),
			KEY `requested_by` (`requested_by`),
			KEY `processed_by` (`last_processed_by`),
			KEY `status` (`status`),
			KEY `agid` (`agid`),
			KEY `action_requested` (`action_requested`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8";
		$alter_table_award_requests = "ALTER TABLE `".TABLE_PREFIX."award_requests`
			ADD CONSTRAINT `".TABLE_PREFIX."award_requests_ibfk_1` FOREIGN KEY (`aid`) REFERENCES `".TABLE_PREFIX."awards` (`aid`)";
		$create_table_awards_granted = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards_granted` (
			`agid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
			`uid` int(10) NOT NULL COMMENT 'user id',
			`aid` int(11) NOT NULL COMMENT 'award id',
			`reason` varchar(255) NOT NULL DEFAULT '' COMMENT 'why was the award given?',
			`date_given` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when was the award given?',
			`requested_by` int(10) DEFAULT NULL COMMENT 'who requested the award?',
			`given_by` int(10) NOT NULL COMMENT 'who gave the award?',
			`arid` int(10) unsigned NOT NULL COMMENT 'award request id',
			PRIMARY KEY (`agid`),
			UNIQUE KEY `arid` (`arid`),
			KEY `uid` (`uid`),
			KEY `aid` (`aid`),
			KEY `date_given` (`date_given`),
			KEY `requested_by` (`requested_by`),
			KEY `given_by` (`given_by`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8";
		$alter_table_awards_granted = "ALTER TABLE `".TABLE_PREFIX."awards_granted`
			ADD CONSTRAINT `".TABLE_PREFIX."awards_granted_ibfk_1` FOREIGN KEY (`aid`) REFERENCES `".TABLE_PREFIX."awards` (`aid`),
			ADD CONSTRAINT `".TABLE_PREFIX."awards_granted_ibfk_2` FOREIGN KEY (`arid`) REFERENCES `".TABLE_PREFIX."award_requests` (`arid`) ON DELETE NO ACTION ON UPDATE CASCADE";
		
		// create tables
		$db->write_query($create_table_award_classes);
		$db->write_query($create_table_awards);
		$db->write_query($create_table_award_requests);
		$db->write_query($create_table_awards_granted);

		// alter tables
		$db->write_query($alter_table_awards);
		$db->write_query($alter_table_award_requests);
		$db->write_query($alter_table_awards_granted);
		}
	
	// create stylesheet
	optawards_setup_stylessheet();
	
	// create templates
	optawards_setup_templates();
	
	// create task
	require_once MYBB_ROOT."/inc/functions_task.php";

	$new_task = array(
		"title" => $db->escape_string($lang->optawards_title),
		"description" => $db->escape_string($lang->optawards_task_description),
		"file" => $db->escape_string('optawards'),
		"minute" => $db->escape_string('27'),
		"hour" => $db->escape_string('3'),
		"day" => $db->escape_string('*'),
		"month" => $db->escape_string('*'),
		"weekday" => $db->escape_string('*'),
		"enabled" => intval(0),
		"logging" => intval(1)
	);

	$new_task['nextrun'] = fetch_next_run($new_task);
	$tid = $db->insert_query("tasks", $new_task);
	$cache->update_tasks();

}

function optawards_uninstall()
{
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	$myplugin = optawards_info();
	$PL->settings_delete( 'optawards' );
	
	$PL->settings_delete('optawards_admin');
	$PL->settings_delete('optawards_display');
	
	global $db, $lang, $cache;
	
	$lang->load('optawards');
	
	// drop tables
	$tables=array(
		'awards_granted',
		'award_requests',
		'awards',
		'award_classes'
	);
	foreach($tables as $table)
	{
		$db->write_query("DROP TABLE ".TABLE_PREFIX.$table);
	}

	$PL->stylesheet_delete('optawards');
	$PL->templates_delete('optawards');
	
	$db->delete_query("tasks", "title='{$db->escape_string($lang->optawards_title)}'");
	$cache->update_tasks();

}


/* --- Hooks: --- */

/* --- Hook #15 - ACP Configuration Tab Handler --- */

$plugins->add_hook( 'admin_config_action_handler', 'optawards_admin_config_action_handler_15', 10 );

function optawards_admin_config_action_handler_15( &$action )
{
	$action[ 'optawards' ] = array(
		 'active' => 'optawards' 
	);
}

/* --- Hook #14 - ACP Awards Menu Entry --- */

$plugins->add_hook( 'admin_config_menu', 'optawards_admin_config_menu_14', 10 );

function optawards_admin_config_menu_14( &$submenu )
{
	global $lang;
	$lang->load( 'optawards' );
	$submenu[] = array(
		 'id' => 'optawards',
		'title' => $lang->optawards_title,
		'link' => 'index.php?module=config-optawards' 
	);
	
}

/* --- Hook #16 - ACP OPT Awards Settings Tab --- */

$plugins->add_hook( 'admin_load', 'optawards_admin_load_16', 10 );

function optawards_admin_load_16()
{
	global $lang, $mybb, $db, $page;
	
	// echo $page->active_action;
	
	if ( $page->active_action != 'optawards' )
		return false;
	
	$lang->load( 'optawards' );
	
	$page->add_breadcrumb_item( $lang->optawards_title, 'index.php?module=config-optawards' );
	
	$tabs[ 'optawards_list_awards' ]     = array(
		 'title' => $lang->optawards_list_awards,
		'link' => 'index.php?module=config-optawards',
		'description' => $lang->optawards_list_awards_description 
	);
	$tabs[ 'optawards_list_classes' ]    = array(
		 'title' => $lang->optawards_list_classes,
		'link' => 'index.php?module=config-optawards&action=listclasses',
		'description' => $lang->optawards_list_classes_description 
	);
	$tabs[ 'optawards_add_award' ]       = array(
		 'title' => $lang->optawards_add_award,
		'link' => 'index.php?module=config-optawards&action=addaward',
		'description' => $lang->optawards_add_award_description 
	);
	$tabs[ 'optawards_award_class_add' ] = array(
		 'title' => $lang->optawards_award_class_add,
		'link' => 'index.php?module=config-optawards&action=addclass',
		'description' => $lang->optawards_add_class_description 
	);
	
	$visibilities[ 1 ] = $lang->optawards_visibility_everywhere;
	$visibilities[ 2 ] = $lang->optawards_visibility_usercp;
	$visibilities[ 3 ] = $lang->optawards_visibility_postbit;
	$visibilities[ 4 ] = $lang->optawards_visibility_invisible;
	
	// default page
	if ( !$mybb->input[ 'action' ] )
	{
		//$page->add_breadcrumb_item($lang->optawards_title);
		$page->output_header( $lang->optawards_list_awards );
		$page->output_nav_tabs( $tabs, 'optawards_list_awards' );
		
		$form                = new Form( "index.php?module=config-optawards&amp;action=updateawards", "post" );
		$usergroups          = array();
		$ugquery             = $db->simple_select( "usergroups", "gid, title", "gid != '1'", array(
			 'order_by' => 'title',
			'order_dir' => 'ASC' 
		) );
		$usergroups[ 'all' ] = $lang->optawards_all_user_groups;
		$usergroups[ '' ]    = $lang->optawards_no_groups;
		while ( $usergroup = $db->fetch_array( $ugquery ) )
		{
			$usergroups[ (int) $usergroup[ 'gid' ] ] = $usergroup[ 'title' ];
		}
		$db->free_result( $ugquery );
		$acquery      = $db->simple_select( 'award_classes', 'acid, name, description', '', array(
			 'order_by' => 'displayorder',
			'order_dir' => 'ASC' 
		) );
		$awardclasses = array();
		while ( $awardclass = $db->fetch_array( $acquery ) )
		{
			$awardclasses[ $awardclass[ 'acid' ] ] = $awardclass[ 'name' ];
			
			$table = new Table;
			$table->construct_header( $lang->optawards_award_name );
			$table->construct_header( $lang->optawards_award_description );
			//$table->construct_header( $lang->optawards_award_class );
			$table->construct_header( $lang->optawards_award_icon, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->optawards_award_iconlarge, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->optawards_award_usergroups2, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->optawards_award_recipients, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->optawards_award_visibility, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->optawards_award_displayorder, array(
				 'class' => 'align_center' 
			) );
			$table->construct_header( $lang->options, array(
				 'class' => 'align_center' 
			) );
			
			$query = $db->simple_select( 'awards', '*', 'acid=' . $awardclass[ 'acid' ], array(
				 'order_by' => 'acid,displayorder',
				'order_dir' => 'ASC' 
			) );
			while ( $award = $db->fetch_array( $query ) )
			{
				$finalIconUrl = $award[ 'icon' ];
				$finalIconUrl = str_replace( '$mybburl', $mybb->settings[ 'bburl' ], $finalIconUrl );
				$finalIconUrl = str_replace( '$imgdir', $theme[ 'imgdir' ], $finalIconUrl );
				
				$finalIconLargeUrl = $award[ 'iconlarge' ];
				$finalIconLargeUrl = str_replace( '$mybburl', $mybb->settings[ 'bburl' ], $finalIconLargeUrl );
				$finalIconLargeUrl = str_replace( '$imgdir', $theme[ 'imgdir' ], $finalIconLargeUrl );
				
				$table->construct_cell( $award[ 'name' ] );
				$table->construct_cell( $award[ 'description' ] );
				//$table->construct_cell( $awardclasses[ $award[ 'acid' ] ] );
				$table->construct_cell( ( !empty( $award[ 'icon' ] ) ? '<img src="' . $finalIconUrl . '" class="awardimgsmall" /> ' : ' ' ), array(
					 'class' => 'align_center' 
				) );
				$table->construct_cell( ( !empty( $award[ 'iconlarge' ] ) ? '<img src="' . $finalIconLargeUrl . '" class="awardimglarge" /> ' : ' ' ), array(
					 'class' => 'align_center' 
				) );
				// $table->construct_cell( $award[ 'usergroups' ], array(
				// 'class' => 'align_center'
				// ) );
				$tusergroups = array();
				foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
				{
					$tusergroups[] = $usergroups[ $usergroup ];
				}
				$table->construct_cell( implode( '<br>', $tusergroups ), array(
					 'class' => 'align_center' 
				) );
				$table->construct_cell( $award[ 'recipients' ], array(
					 'class' => 'align_center' 
				) );
				$table->construct_cell( $visibilities[ $award[ 'visibility' ] ], array(
					 'class' => 'align_center' 
				) );
				
				$table->construct_cell( '<input type="text" name="award[' . $award[ 'aid' ] . ']" value="' . $award[ 'displayorder' ] . '" size="3" />', array(
					 'class' => 'align_center' 
				) );
				
				$popup = new PopupMenu( "award_{$award['aid']}", $lang->options );
				$popup->add_item( $lang->optawards_edit_award, "index.php?module=config-optawards&amp;action=editaward&amp;aid={$award['aid']}" );
				$popup->add_item( $lang->optawards_delete_award, "index.php?module=config-optawards&amp;action=deleteaward&amp;aid={$award['aid']}" );
				$table->construct_cell( $popup->fetch(), array(
					 'class' => 'align_center' 
				) );
				
				$table->construct_row();
			}
			$db->free_result( $query );
			
			$table->construct_cell( '<input type="submit" value="' . $lang->optawards_update_order . '" />', array(
				 'colspan' => 10 
			) );
			$table->construct_row();
			$table->output( $lang->optawards_table_awards . ": " . $awardclass[ 'name' ] . " - " . $awardclass[ 'description' ] );
		}
		$db->free_result( $acquery );
		
		$form->end;
		
		$page->output_footer();
	}
	
	// update sort order of the awards
	if ( $mybb->input[ 'action' ] == 'updateawards' )
	{
		$awards = $_REQUEST[ 'award' ];
		foreach ( $awards as $award => $key )
		{
			$key   = (int) $key;
			$award = (int) $award;
			
			$updated_record = array(
				 "displayorder" => $db->escape_string( $key ) 
			);
			$db->update_query( 'awards', $updated_record, "aid='" . $db->escape_string( $award ) . "'" );
		}
		
		// optawards_reorderawards();
		
		admin_redirect( "index.php?module=config-optawards" );
	}
	
	// edit awards
	if ( $mybb->input[ 'action' ] == 'editaward' || $mybb->input[ 'action' ] == 'addaward' )
	{
		if ( $mybb->input[ 'action' ] == 'addaward' )
		{
			$name         = '';
			$description  = '';
			$acid         = '';
			$icon         = '';
			$iconlarge    = '';
			$usergroups   = 'all';
			$pm           = '{default}';
			$visibility   = '1';
			$displayorder = '1';
		}
		else
		{
			$aid = (int) $_REQUEST[ 'aid' ];
			
			$query = $db->simple_select( 'awards', '*', 'aid=' . $aid, array(
				 'limit' => '1' 
			) );
			$award = $db->fetch_array( $query );
			$db->free_result( $query );
			
			$name         = $award[ 'name' ];
			$description  = $award[ 'description' ];
			$acid         = $award[ 'acid' ];
			$icon         = $award[ 'icon' ];
			$iconlarge    = $award[ 'iconlarge' ];
			$usergroups   = $award[ 'usergroups' ];
			$pm           = $award[ 'pm' ];
			$visibility   = $award[ 'visibility' ];
			$displayorder = $award[ 'displayorder' ];
		}
		
		if ( $mybb->request_method == 'post' )
		{
			// Check Post
			$name         = $mybb->input[ 'name' ];
			$description  = $mybb->input[ 'description' ];
			$acid         = $mybb->input[ 'acid' ];
			$icon         = $mybb->input[ 'icon' ];
			$iconlarge    = $mybb->input[ 'iconlarge' ];
			$usergroups   = implode( ",", $mybb->input[ 'usergroups' ] );
			$visibility   = $mybb->input[ 'visibility' ];
			$displayorder = $mybb->input[ 'displayorder' ];
			
			if ( empty( $name ) )
			{
				$errors[] = $lang->optawards_error_no_award_name;
			}
			
			if ( empty( $icon ) )
			{
				$errors[] = $lang->optawards_error_no_award_icon;
			}
			
			if ( empty( $iconlarge ) )
			{
				$errors[] = $lang->optawards_error_no_award_iconlarge;
			}
			
			if ( $errors )
			{
				$page->output_inline_error( $errors );
			}
			else
			{
				if ( $mybb->input[ 'action' ] == 'addaward' )
				{
					$new_record = array(
						 "name" => $db->escape_string( $name ),
						"description" => $db->escape_string( $description ),
						"acid" => $db->escape_string( $acid ),
						"icon" => $db->escape_string( $icon ),
						"iconlarge" => $db->escape_string( $iconlarge ),
						"usergroups" => $db->escape_string( $usergroups ),
						"visibility" => $db->escape_string( $visibility ),
						"displayorder" => $db->escape_string( $displayorder ) 
					);
					$db->insert_query( 'awards', $new_record );
					flash_message( $lang->optawards_award_added, 'success' );
				}
				else
				{
					$updated_record = array(
						 "name" => $db->escape_string( $name ),
						"description" => $db->escape_string( $description ),
						"acid" => $db->escape_string( $acid ),
						"icon" => $db->escape_string( $icon ),
						"iconlarge" => $db->escape_string( $iconlarge ),
						"usergroups" => $db->escape_string( $usergroups ),
						"visibility" => $db->escape_string( $visibility ),
						"displayorder" => $db->escape_string( $displayorder ) 
					);
					$db->update_query( 'awards', $updated_record, "aid='" . $db->escape_string( $aid ) . "'" );
					flash_message( $lang->optawards_award_edited, 'success' );
				}
				
				// optawards_reorderclasses();
				
				optawards_cache_awards();
				
				admin_redirect( "index.php?module=config-optawards" );
			}
		}
		
		$finalIconUrl = $icon;
		$finalIconUrl = str_replace( '$mybburl', $mybb->settings[ 'bburl' ], $finalIconUrl );
		$finalIconUrl = str_replace( '$imgdir', $theme[ 'imgdir' ], $finalIconUrl );
		
		$finalIconLargeUrl = $iconlarge;
		$finalIconLargeUrl = str_replace( '$mybburl', $mybb->settings[ 'bburl' ], $finalIconLargeUrl );
		$finalIconLargeUrl = str_replace( '$imgdir', $theme[ 'imgdir' ], $finalIconLargeUrl );
		
		if ( $mybb->input[ 'action' ] == 'addaward' )
		{
			$page->add_breadcrumb_item( $lang->optawards_edit_award, 'index.php?module=config-optawards&amp;action=addaward' );
			$page->output_header( $lang->optawards_add_award );
			$page->output_nav_tabs( $tabs, 'optawards_add_award' );
			$form = new Form( "index.php?module=config-optawards&amp;action=addaward", "post" );
		}
		else
		{
			$page->add_breadcrumb_item( $lang->optawards_edit_award, 'index.php?module=config-optawards&amp;action=editaward' );
			$page->output_header( $lang->optawards_edit_award );
			$page->output_nav_tabs( $tabs, 'optawards_edit_award' );
			$form = new Form( "index.php?module=config-optawards&amp;action=editaward", "post" );
		}
		
		$table = new Table;
		
		$table->construct_cell( $lang->optawards_award_name );
		$table->construct_cell( '<input type="text" size="50" name="name" value="' . $name . '" />' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_description );
		$table->construct_cell( '<input type="text" size="150" name="description" value="' . $description . '" />' );
		$table->construct_row();
		
		$query   = $db->simple_select( 'award_classes', 'acid, name', '', array(
			 'order_by' => 'displayorder',
			'order_dir' => 'ASC' 
		) );
		$options = array();
		while ( $awardclass = $db->fetch_array( $query ) )
		{
			$options[ $awardclass[ 'acid' ] ] = $awardclass[ 'name' ];
		}
		$db->free_result( $query );
		$table->construct_cell( $lang->optawards_award_class );
		$table->construct_cell( $form->generate_select_box( 'acid', $options, $acid, array(
			 'id' => 'acid' 
		) ) );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_icon );
		$table->construct_cell( '<input type="text" size="150" name="icon" value="' . $icon . '" />' . ( !empty( $icon ) ? '<br><img src="' . $finalIconUrl . '" /> ' : ' ' ) );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_iconlarge );
		$table->construct_cell( '<input type="text" size="150" name="iconlarge" value="' . $iconlarge . '" />' . ( !empty( $iconlarge ) ? '<br><img src="' . $finalIconLargeUrl . '" /> ' : ' ' ) );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_visibility );
		$table->construct_cell( $form->generate_select_box( 'visibility', $visibilities, $visibility, array(
			 'id' => 'visibility' 
		) ) );
		$table->construct_row();
		
		$options          = array();
		$query            = $db->simple_select( "usergroups", "gid, title", "gid != '1'", array(
			 'order_by' => 'title' 
		) );
		$options[ 'all' ] = $lang->optawards_all_user_groups;
		while ( $usergroup = $db->fetch_array( $query ) )
		{
			$options[ (int) $usergroup[ 'gid' ] ] = $usergroup[ 'title' ];
		}
		$db->free_result( $query );
		$table->construct_cell( $lang->optawards_award_usergroups );
		$table->construct_cell( $form->generate_select_box( 'usergroups[]', $options, explode( ",", $usergroups ), array(
			 'id' => 'usergroups',
			'multiple' => true,
			'size' => 5 
		) ) );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_pm_template );
		$table->construct_cell( '<textarea cols="80" rows="15" name="pm" id="pm">' . $pm . '</textarea>' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_displayorder );
		$table->construct_cell( '<input type="text" size="3" name="displayorder" value="' . $displayorder . '" />' );
		$table->construct_row();
		
		if ( $mybb->input[ 'action' ] == 'addaward' )
		{
			$table->construct_cell( '<input type="submit" value="' . $lang->optawards_add_award . '" />', array(
				 'colspan' => 2 
			) );
		}
		else
		{
			$table->construct_cell( '<input type="hidden" name="aid" value="' . $aid . '" /><input type="submit" value="' . $lang->optawards_edit_award . '" />', array(
				 'colspan' => 2 
			) );
		}
		$table->construct_row();
		
		$form->end;
		if ( $mybb->input[ 'action' ] == 'addaward' )
		{
			$table->output( $lang->optawards_add_award_description );
		}
		else
		{
			$table->output( $lang->optawards_edit_award_description );
		}
		
		$page->output_footer();
	}
	
	// echo " - ".$mybb->input['action'];
	
	// list award classes
	if ( $mybb->input[ 'action' ] == 'listclasses' )
	{
		$page->add_breadcrumb_item( $lang->optawards_list_classes, 'index.php?module=config-optawards&amp;action=listclasses' );
		$page->output_header( $lang->optawards_title );
		$page->output_nav_tabs( $tabs, 'optawards_list_classes' );
		
		$form  = new Form( "index.php?module=config-optawards&amp;action=updateclasses", "post" );
		$table = new Table;
		$table->construct_header( $lang->optawards_award_class_name );
		$table->construct_header( $lang->optawards_award_class_singular );
		$table->construct_header( $lang->optawards_award_class_description );
		$table->construct_header( $lang->optawards_award_class_icon, array(
			 'class' => 'align_center' 
		) );
		$table->construct_header( $lang->optawards_award_class_displayorder, array(
			 'class' => 'align_center' 
		) );
		$table->construct_header( $lang->options, array(
			 'class' => 'align_center' 
		) );
		
		$query = $db->simple_select( 'award_classes', '*', '', array(
			 'order_by' => 'displayorder',
			'order_dir' => 'ASC' 
		) );
		while ( $awardclass = $db->fetch_array( $query ) )
		{
			$finalIconUrl = $awardclass[ 'icon' ];
			$finalIconUrl = str_replace( '$mybburl', $mybb->settings[ 'bburl' ], $finalIconUrl );
			$finalIconUrl = str_replace( '$imgdir', $theme[ 'imgdir' ], $finalIconUrl );
			
			$table->construct_cell( $awardclass[ 'name' ] );
			$table->construct_cell( $awardclass[ 'singular' ] );
			$table->construct_cell( $awardclass[ 'description' ] );
			$table->construct_cell( ( !empty( $awardclass[ 'icon' ] ) ? '<img src="' . $finalIconUrl . '" /> ' : ' ' ), array(
				 'class' => 'align_center' 
			) );
			$table->construct_cell( '<input type="text" name="awardclass[' . $awardclass[ 'acid' ] . ']" value="' . $awardclass[ 'displayorder' ] . '" size="3" />', array(
				 'class' => 'align_center' 
			) );
			
			$popup = new PopupMenu( "award_{$awardclass['acid']}", $lang->options );
			$popup->add_item( $lang->optawards_award_class_edit, "index.php?module=config-optawards&amp;action=editclass&amp;acid={$awardclass['acid']}" );
			$popup->add_item( $lang->optawards_delete_class, "index.php?module=config-optawards&amp;action=deleteclass&amp;acid={$awardclass['acid']}" );
			$table->construct_cell( $popup->fetch(), array(
				 'class' => 'align_center' 
			) );
			
			$table->construct_row();
		}
		$db->free_result( $query );
		
		$table->construct_cell( '<input type="submit" value="' . $lang->optawards_update_order . '" />', array(
			 'colspan' => 6 
		) );
		$table->construct_row();
		
		$form->end;
		$table->output( $lang->optawards_table_award_classes );
		
		$page->output_footer();
	}
	
	// update sort order of the award classes
	if ( $mybb->input[ 'action' ] == 'updateclasses' )
	{
		$awardclasses = $_REQUEST[ 'awardclass' ];
		foreach ( $awardclasses as $awardclass => $key )
		{
			$key        = (int) $key;
			$awardclass = (int) $awardclass;
			
			$updated_record = array(
				 "displayorder" => $db->escape_string( $key ) 
			);
			$db->update_query( 'award_classes', $updated_record, "acid='" . $db->escape_string( $awardclass ) . "'" );
		}
		
		// optawards_reorderclasses();
		
		admin_redirect( "index.php?module=config-optawards&amp;action=listclasses" );
	}
	
	// add or edit award classes
	if ( $mybb->input[ 'action' ] == 'editclass' || $mybb->input[ 'action' ] == 'addclass' )
	{
		if ( $mybb->input[ 'action' ] == 'addclass' )
		{
			$name         = '';
			$singular     = '';
			$description  = '';
			$icon         = '';
			$displayorder = '';
		}
		else
		{
			$acid       = (int) $_REQUEST[ 'acid' ];
			$query      = $db->simple_select( 'award_classes', '*', 'acid=' . $acid, array(
				 'limit' => '1' 
			) );
			$awardclass = $db->fetch_array( $query );
			$db->free_result( $query );
			
			$name         = $awardclass[ 'name' ];
			$singular     = $awardclass[ 'singular' ];
			$description  = $awardclass[ 'description' ];
			$icon         = $awardclass[ 'icon' ];
			$displayorder = $awardclass[ 'displayorder' ];
		}
		
		if ( $mybb->request_method == 'post' )
		{
			// Check Post
			$name         = $mybb->input[ 'name' ];
			$singular     = $mybb->input[ 'singular' ];
			$description  = $mybb->input[ 'description' ];
			$icon         = $mybb->input[ 'icon' ];
			$displayorder = $mybb->input[ 'displayorder' ];
			
			if ( empty( $name ) )
			{
				$errors[] = $lang->optawards_error_no_name;
			}
			
			if ( empty( $singular ) )
			{
				$errors[] = $lang->optawards_error_no_singular;
			}
			
			if ( $errors )
			{
				$page->output_inline_error( $errors );
			}
			else
			{
				if ( $mybb->input[ 'action' ] == 'addclass' )
				{
					$new_record = array(
						 "name" => $db->escape_string( $name ),
						"singular" => $db->escape_string( $singular ),
						"description" => $db->escape_string( $description ),
						"icon" => $db->escape_string( $icon ),
						"displayorder" => $db->escape_string( $displayorder ) 
					);
					$db->insert_query( 'award_classes', $new_record );
					flash_message( $lang->optawards_award_class_added, 'success' );
				}
				else
				{
					$updated_record = array(
						 "name" => $db->escape_string( $name ),
						"singular" => $db->escape_string( $singular ),
						"description" => $db->escape_string( $description ),
						"icon" => $db->escape_string( $icon ),
						"displayorder" => $db->escape_string( $displayorder ) 
					);
					$db->update_query( 'award_classes', $updated_record, "acid='" . $db->escape_string( $acid ) . "'" );
					flash_message( $lang->optawards_award_class_edited, 'success' );
				}
				
				// optawards_reorderclasses();
				
				admin_redirect( "index.php?module=config-optawards&amp;action=listclasses" );
			}
		}
		
		if ( $mybb->input[ 'action' ] == 'addclass' )
		{
			$page->add_breadcrumb_item( $lang->optawards_award_class_edit, 'index.php?module=config-optawards&amp;action=addclass' );
			$page->output_header( $lang->optawards_title );
			$page->output_nav_tabs( $tabs, 'optawards_award_class_add' );
			$form = new Form( "index.php?module=config-optawards&amp;action=addclass", "post" );
		}
		else
		{
			$page->add_breadcrumb_item( $lang->optawards_award_class_edit, 'index.php?module=config-optawards&amp;action=editclass' );
			$page->output_header( $lang->optawards_title );
			$page->output_nav_tabs( $tabs, 'optawards_edit_class' );
			$form = new Form( "index.php?module=config-optawards&amp;action=editclass", "post" );
		}
		
		$table = new Table;
		
		$table->construct_cell( $lang->optawards_award_class_name );
		$table->construct_cell( '<input type="text" size="50" name="name" value="' . $name . '" />' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_class_singular );
		$table->construct_cell( '<input type="text" size="50" name="singular" value="' . $singular . '" />' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_class_description );
		$table->construct_cell( '<input type="text" size="150" name="description" value="' . $description . '" />' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_class_icon );
		$table->construct_cell( '<input type="text" size="150" name="icon" value="' . $icon . '" />' );
		$table->construct_row();
		
		$table->construct_cell( $lang->optawards_award_class_displayorder );
		$table->construct_cell( '<input type="text" size="3" name="displayorder" value="' . $displayorder . '" />' );
		$table->construct_row();
		
		if ( $mybb->input[ 'action' ] == 'addclass' )
		{
			$table->construct_cell( '<input type="submit" value="' . $lang->optawards_award_class_add . '" />', array(
				 'colspan' => 2 
			) );
		}
		else
		{
			$table->construct_cell( '<input type="hidden" name="acid" value="' . $acid . '" /><input type="submit" value="' . $lang->optawards_award_class_edit . '" />', array(
				 'colspan' => 2 
			) );
		}
		$table->construct_row();
		
		$form->end;
		if ( $mybb->input[ 'action' ] == 'addclass' )
		{
			$table->output( $lang->optawards_add_class_description );
		}
		else
		{
			$table->output( $lang->optawards_edit_class_description );
		}
		
		$page->output_footer();
	}
	
	// delete award classes
	if ( $mybb->input[ 'action' ] == 'deleteclass' )
	{
		if ( $mybb->request_method == 'post' )
		{
			if ( $mybb->input[ 'no' ] )
			{
				admin_redirect( "index.php?module=config-optawards&amp;action=listclasses" );
			}
			$page->add_breadcrumb_item( $lang->optawards_list_classes, 'index.php?module=config-optawards&amp;action=listclasses' );
			$page->output_header( $lang->optawards_title );
			$page->output_nav_tabs( $tabs, 'optawards' );
			
			$page->output_inline_error( $lang->optawards_delete_not_implemented );
			
			$page->output_footer();
			// admin_redirect("index.php?module=config-optawards&amp;action=listclasses");
		}
		else
		{
			$page->add_breadcrumb_item( $lang->optawards_delete_class, 'index.php?module=config-optawards&amp;action=deleteclass' );
			
			$page->output_confirm_action( 'index.php?module=config-optawards&amp;action=deleteclass', $lang->optawards_confirm_deleteclass );
			
			$page->output_footer();
		}
	}
	else
	{
		$page->output_header( $lang->optawards_title );
		$page->output_nav_tabs( $tabs, 'optawards_add' );
		
		$page->output_inline_error( 'undefined action' );
		
		$page->output_footer();
	}
}

/* --- Hook #17 - misc.php?action=showawards --- */

$plugins->add_hook( 'misc_start', 'optawards_misc_start_17', 10 );

function optawards_misc_start_17()
{
	global $db, $mybb, $lang, $templates, $headerinclude, $header, $footer, $theme, $cache;
	
	$lang->load( 'optawards' );
	
	// show all awards
	if ( $mybb->input[ 'action' ] == 'showawards' )
	{
		add_breadcrumb( $lang->optawards_show_awards, "misc.php?action=showawards" );
		
		// gather some data about the user viewing the awards
		$usergroups = optawards_get_usergroups( $mybb->user[ 'uid' ] );
		$groupslead = optawards_get_groupslead( $mybb->user[ 'uid' ] );
		$granter    = optawards_is_granter( $mybb->user[ 'uid' ], $usergroups );
		
		$content = '';
		$acquery = $db->simple_select( 'award_classes', '*', '', array(
			 'order_by' => 'acid',
			'order_dir' => 'ASC' 
		) );
		while ( $award_class = $db->fetch_array( $acquery ) )
		{
			$award_list = '';
			$query      = $db->simple_select( 'awards', '*', 'acid=' . intval( $award_class[ 'acid' ] ), array(
				 'order_by' => 'acid,displayorder',
				'order_dir' => 'ASC' 
			) );
			$count      = 0;
			while ( $award = $db->fetch_array( $query ) )
			{
				$count++;
				$trow        = alt_trow();
				$awardaction = array();
				
				if ( !$granter )
				{
					// request an awards - requires group membership unless the awards is available for everyone, not used for the groups leaders
					$request         = false;
					$awardactioncmd  = 'requestaward';
					$awardactiontext = $lang->optawards_page_list_action_request;
					foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
					{
						if ( !$request )
						{
							if ( ( $usergroup == 'all' || in_array( $usergroup, $usergroups ) ) && !in_array( $usergroup, $groupslead ) )
							{
								$request = true;
							}
						}
					}
					if ( $request )
						eval( "\$awardaction[] .= \"" . $templates->get( "optawards_awards_list_row_action" ) . "\";" );
					
					// recommend an awards - requires group membership unless the awards is available for everyone, not used for the groups leaders
					$recommend       = false;
					$awardactioncmd  = 'recommendaward';
					$awardactiontext = $lang->optawards_page_list_action_recommend;
					foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
					{
						if ( !$recommend )
						{
							if ( ( $usergroup == 'all' || in_array( $usergroup, $usergroups ) ) && !in_array( $usergroup, $groupslead ) )
							{
								$recommend = true;
							}
						}
					}
					if ( $recommend )
						eval( "\$awardaction[] .= \"" . $templates->get( "optawards_awards_list_row_action" ) . "\";" );
				}
				// grant awards - requires group leadership
				$grant           = false;
				$awardactioncmd  = 'grantaward';
				$awardactiontext = $lang->optawards_page_list_action_grant;
				foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
				{
					if ( !$grant )
					{
						if ( $granter || in_array( $usergroup, $groupslead ) )
						{
							$grant = true;
						}
					}
				}
				if ( $grant )
					eval( "\$awardaction[] .= \"" . $templates->get( "optawards_awards_list_row_action" ) . "\";" );
				
				if ( empty( $awardaction ) )
				{
					$awardaction[] = $lang->optawards_page_list_action_none;
				}
				
				$awardactions = implode( '<br>', $awardaction );
				
				eval( "\$award_list .= \"" . $templates->get( "optawards_awards_list_row" ) . "\";" );
			}
			$db->free_result( $query );
			if ( $count == 0 )
			{
				$trow = alt_trow();
				eval( "\$award_list .= \"" . $templates->get( "optawards_awards_list_empty" ) . "\";" );
			}
			
			eval( "\$content .= \"" . $templates->get( "optawards_awards_list" ) . "\";" );
			
			eval( "\$awards_page = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
			
		}
		$db->free_result( $acquery );
		output_page( $awards_page );
	} // if ( $mybb->input['action'] == 'showawards' )
	
	// show specific award
	if ( $mybb->input[ 'action' ] == 'viewaward' )
	{
		add_breadcrumb( $lang->optawards_view_award, "misc.php?action=viewaward&aid=" . $mybb->input[ 'aid' ] );
		
		// show award summary
		$query = $db->simple_select( 'awards', '*', 'aid=' . $mybb->input[ 'aid' ] );
		$award = $db->fetch_array( $query );
		$db->free_result( $query );
		$query            = $db->simple_select( 'award_classes', '*', 'acid=' . $award[ 'acid' ] );
		$award[ 'class' ] = $db->fetch_field( $query, 'name' );
		$db->free_result( $query );
		$usergroups                     = $cache->read( 'usergroups' );
		$usergroups[ 'all' ][ 'title' ] = $lang->optawards_all_user_groups;
		$usergroups[ '' ][ 'title' ]    = $lang->optawards_no_groups;
		// mydump($usergroups, '$usergroups');
		$tugs                           = array();
		foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
		{
			$tugs[] = $usergroups[ $usergroup ][ 'title' ];
		}
		$award[ 'usergroups' ] = implode( '<br>', $tugs );
		
		// show recipients
		$query = $db->simple_select( 'awards_granted', 'uid,reason,date_given', 'aid=' . $mybb->input[ 'aid' ] );
		$count = 0;
		while ( $award_granted = $db->fetch_array( $query ) )
		{
			$count++;
			$trow                        = alt_trow();
			$user                        = optawards_get_user_by_uid( $award_granted[ 'uid' ] );
			$usernameformated            = format_name( $user[ 'username' ], $user[ 'usergroup' ], $user[ 'displaygroup' ] );
			$award_granted[ 'username' ] = build_profile_link( $usernameformated, $award_granted[ 'uid' ] );
			eval( "\$users_list .= \"" . $templates->get( "optawards_award_view_row" ) . "\";" );
		}
		if ( $count == 0 )
			eval( "\$users_list .= \"" . $templates->get( "optawards_award_view_empty" ) . "\";" );
		
		
		eval( "\$content .= \"" . $templates->get( "optawards_award_view" ) . "\";" );
		
		eval( "\$awards_page = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
		output_page( $awards_page );
	} // if ( $mybb->input['action'] == 'viewaward' )
	
	// show users awards (full list)
	if ( $mybb->input[ 'action' ] == 'viewuserawards' )
	{
		add_breadcrumb( $lang->optawards_view_award, "misc.php?action=viewuserawards&uid=" . $mybb->input[ 'uid' ] );
		
		$username = optawards_get_username_by_uid( $mybb->input[ 'uid' ] );
		
		// get users full award list
		$query = $db->write_query( '
			SELECT * FROM ' . TABLE_PREFIX . 'awards_granted AS g
			JOIN ' . TABLE_PREFIX . 'awards AS a
				ON g.aid=a.aid
			WHERE g.uid=' . intval( $mybb->input[ 'uid' ] ) . '
				AND (visibility = 1 OR visibility = 2)
			ORDER BY g.date_given DESC
		' );
		
		$count = 0;
		while ( $award = $db->fetch_array( $query ) )
		{
			$count++;
			$trow = alt_trow();
			eval( "\$awards_list .= \"" . $templates->get( "optawards_user_view_row" ) . "\";" );
		}
		$db->free_result( $query );
		
		if ( $count == 0 )
			eval( "\$awards_list .= \"" . $templates->get( "optawards_user_view_empty" ) . "\";" );
		
		eval( "\$content .= \"" . $templates->get( "optawards_user_view" ) . "\";" );
		
		eval( "\$awards_page = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
		output_page( $awards_page );
	}
	
	// do award requests, recommendations and grants
	if ( $mybb->input[ 'action' ] == 'requestaward' || $mybb->input[ 'action' ] == 'recommendaward' || $mybb->input[ 'action' ] == 'grantaward' )
	{
		// check permissions first!
		
		// gather some data about the user accessing this page
		$usergroups = optawards_get_usergroups( $mybb->user[ 'uid' ] );
		$groupslead = optawards_get_groupslead( $mybb->user[ 'uid' ] );
		$granter    = optawards_is_granter( $mybb->user[ 'uid' ], $usergroups );
		
		// get award data
		$award = optawards_get_award_info( $mybb->input[ 'aid' ] );
		
		// check permissions first!
		$permitted = false;
		
		if ( $mybb->input[ 'action' ] == 'requestaward' || $mybb->input[ 'action' ] == 'recommendaward' )
		{
			foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
			{
				if ( ( $usergroup == 'all' || in_array( $usergroup, $usergroups ) ) && !in_array( $usergroup, $groupslead ) )
				{
					$permitted = true;
				}
			}
		}
		else // $mybb->input['action'] == 'grantaward'
		{
			if ( $granter )
			{
				$permitted = true;
			}
			else
			{
				foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
				{
					if ( in_array( $usergroup, $groupslead ) )
					{
						$permitted = true;
					}
				}
			}
		}
		
		if ( !$permitted )
		{
			error_no_permission();
		}
		
		// start the action processing
		// set default values for the input fields
		$reason                         = '';
		$requested_for                  = 0;
		$requested_by                   = $mybb->user[ 'uid' ];
		$status                         = 'new';
		$requestaction                  = 'grant';
		$usergroups                     = $cache->read( 'usergroups' );
		$usergroups[ 'all' ][ 'title' ] = $lang->optawards_all_user_groups;
		$usergroups[ '' ][ 'title' ]    = $lang->optawards_no_groups;
		// mydump($usergroups, '$usergroups');
		$tugs                           = array();
		foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
		{
			$tugs[] = $usergroups[ $usergroup ][ 'title' ];
		}
		$award[ 'usergroups' ] = implode( '<br>', $tugs );
		
		if ( $mybb->request_method == 'post' )
		{
			// process input data
			if ( $mybb->input[ 'my_post_key' ] != $mybb->post_code )
			{
				error_no_permission();
			}
			if ( empty( $mybb->input[ 'username' ] ) )
			{
				error( $lang->optawards_username_empty, $lang->optawards_error );
			}
			$uid = optawards_get_uid_by_username( $mybb->input[ 'username' ] );
			if ( empty( $uid ) )
			{
				error( $lang - optawards_username_not_found . $mybb->input[ 'username' ], $lang->optawards_error );
			}
			// insert award request for further processing
			$arid = optawards_add_award_request( $mybb->input[ 'aid' ], $uid, 'grant', $mybb->user[ 'uid' ], $mybb->input[ 'reason' ] );
			if ( $mybb->input[ 'action' ] == 'grantaward' )
			{
				// update award request as accepted
				optawards_update_award_requests( $arid, 'accepted', $mybb->user[ 'uid' ] );
				optawards_process_award_request( $arid );
			}
			redirect( "misc.php?action=showawards", $lang->optawards_award_request_added );
		}
		
		// build page
		$query=$db->simple_select(
			'award_classes',
			'name',
			'acid='.intval($award['acid'])
		);
		$award['class']=$db->fetch_field($query, 'name');
		
		if ( $mybb->input[ 'action' ] == 'requestaward' )
		{
			add_breadcrumb( $lang->optawards_request_award, "misc.php?action=requestaward&aid=" . $award[ 'aid' ] );
			$requested_for = $mybb->user[ 'uid' ];
			$username      = optawards_get_username_by_uid( $requested_for );
			eval( "\$select_user .= \"" . $templates->get( "optawards_select_user_hidden" ) . "\";" );
			$awardaction = 'requestaward';
		}
		elseif ( $mybb->input[ 'action' ] == 'recommendaward' )
		{
			add_breadcrumb( $lang->optawards_recommend_award, "misc.php?action=recommendaward&aid=" . $award[ 'aid' ] );
			eval( "\$select_user .= \"" . $templates->get( "optawards_select_user" ) . "\";" );
			$awardaction = 'recommendaward';
		}
		elseif ( $mybb->input[ 'action' ] == 'grantaward' )
		{
			add_breadcrumb( $lang->optawards_grant_award, "misc.php?action=grantaward&aid=" . $award[ 'aid' ] );
			eval( "\$select_user .= \"" . $templates->get( "optawards_select_user" ) . "\";" );
			$awardaction = 'grantaward';
		}
		eval( "\$content = \"" . $templates->get( "optawards_request_award_form" ) . "\";" );
		eval( "\$awards_processing = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
		output_page( $awards_processing );
	}
	
	
	// process requested awards
	if ( $mybb->input[ 'action' ] == 'processawardrequests' )
	{
		add_breadcrumb( $lang->optawards_process_award_requests, "misc.php?action=processawardrequests" );
		
		// check permissions first!
		
		// gather some data about the user accessing this page
		$usergroups = optawards_get_usergroups( $mybb->user[ 'uid' ] );
		$groupslead = optawards_get_groupslead( $mybb->user[ 'uid' ] );
		$granter    = optawards_is_granter( $mybb->user[ 'uid' ], $usergroups );
		
		// only users with grant permissions may access this page
		if ( empty( $groupslead ) && empty( $granter ) )
		{
			error_no_permission();
		}
		
		if ( !empty( $mybb->input[ 'par' ] ) && !empty( $mybb->input[ 'arid' ] ) )
		{
			$arid          = $mybb->input[ 'arid' ];
			$query         = $db->simple_select( 'award_requests', '*', 'arid=' . intval( $arid ) );
			$award_request = $db->fetch_array( $query );
			$db->free_result( $query );
			$aid = $award_request[ 'aid' ];
			
			$awards = $cache->read( 'optawards' );
			$award  = $awards[ $aid ];
			
			// are we responsible for this award?
			$responsible = false;
			if ( $granter )
			{
				$responsible = true;
			}
			else
			{
				foreach ( explode( ',', $award[ 'usergroups' ] ) as $usergroup )
				{
					if ( in_array( $usergroup, $groupslead ) )
					{
						$responsible = true;
					}
				}
			}
			if ( $responsible )
			{
				if ( $mybb->input[ 'par' ] == 'grant' )
				{
					optawards_update_award_requests( $arid, 'accepted', $mybb->user[ 'uid' ] );
					optawards_process_award_request( $arid );
				}
				elseif ( $mybb->input[ 'par' ] == 'deny' )
				{
					$placeholders     = array(
						 'recipient' => optawards_get_username_by_uid( $award_request[ 'requested_for' ] ),
						'requestor' => optawards_get_username_by_uid( $award_request[ 'requested_by' ] ),
						'processor' => optawards_get_username_by_uid( $mybb->user[ 'uid' ] ),
						'award' => $award[ 'name' ],
						'awardinfo' => $mybb->settings[ 'bburl' ] . '/misc.php?action=viewaward&aid=' . $aid,
						'description' => $award[ 'description' ],
						'icon' => $award[ 'iconlarge' ],
						'reason' => $award_request[ 'reason' ],
						'date' => $award_request[ 'date_requested' ] 
					);
					$pm_deny_template = optawards_fill_placeholders( $mybb->settings[ 'optawards_admin_pm_deny' ], $placeholders );
					
					if ( $mybb->request_method == 'post' )
					{
						verify_post_check( $mybb->input[ 'my_post_key' ] );
						if ( $mybb->input[ 'submit' ] == 'Submit' )
						{
							optawards_update_award_requests( $arid, 'rejected', $mybb->user[ 'uid' ] );
							$placeholders = array(
								 'denyreason' => $mybb->input[ 'reason' ] 
							);
							$message      = optawards_fill_placeholders( $pm_deny_template, $placeholders );
							$subject      = $lang->optawards_award_request_denied;
							optawards_send_pm( $award_request[ 'requested_for' ], $mybb->user[ 'uid' ], $subject, $message, 0 );
							redirect( 'misc.php?action=processawardrequests', $lang->optawards_award_processed );
						}
						else
						{
							redirect( 'misc.php?action=processawardrequests', $lang->optawards_award_processed );
						}
					}
					add_breadcrumb( $lang->optawards_deny_award, "misc.php?action=processawardrequests&arid=" . $arid . "&par=deny" );
					$pm_deny_template = optawards_parse_text( $pm_deny_template );
					eval( "\$content = \"" . $templates->get( "optawards_request_denied_form" ) . "\";" );
					eval( "\$deny_award = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
					output_page( $deny_award );
				}
				else
				{
					error( $lang->optawards_unknown_award_processing_action . $mybb->input[ 'par' ], $lang->optawards_award_processing );
				}
				redirect( 'misc.php?action=processawardrequests', $lang->optawards_award_processed );
			}
			else
			{
				error_no_permission();
			}
		}
		else
		{
			// start the action processing
			$query  = $db->simple_select( 'award_requests', '*', 'status="new"', array(
				 'order_by' => 'arid',
				'order_dir' => 'ASC' 
			) );
			$awards = $cache->read( 'optawards' );
			$count  = 0;
			while ( $award_request = $db->fetch_array( $query ) )
			{
				// may the user process this award request?
				$visible = false;
				if ( $granter )
				{
					$visible = true;
				}
				else
				{
					foreach ( explode( ',', $awards[ $award_request[ 'aid' ] ][ 'usergroups' ] ) as $usergroup )
					{
						if ( in_array( $usergroup, $groupslead ) )
						{
							$visible = true;
						}
					}
				}
				if ( $visible )
				{
					$count++;
					$trow                        = alt_trow();
					$iconlarge                   = $awards[ $award_request[ 'aid' ] ][ 'iconlarge' ];
					$awardname                   = $awards[ $award_request[ 'aid' ] ][ 'name' ];
					$user                        = optawards_get_user_by_uid( $award_request[ 'requested_for' ] );
					$usernameformated            = format_name( $user[ 'username' ], $user[ 'usergroup' ], $user[ 'displaygroup' ] );
					$award_request[ 'username' ] = build_profile_link( $usernameformated, $award_request[ 'requested_for' ] );
					eval( "\$award_requests .= \"" . $templates->get( "optawards_requests_row" ) . "\";" );
				}
			}
			$db->free_result( $query );
			if ( $count == 0 )
			{
				eval( "\$award_requests = \"" . $templates->get( "optawards_requests_empty" ) . "\";" );
			}
			eval( "\$content = \"" . $templates->get( "optawards_requests" ) . "\";" );
			eval( "\$award_page = \"" . $templates->get( "optawards_awards_page" ) . "\";" );
			output_page( $award_page );
		}
	}
}

/* --- Hook #18 - pending_award_requests --- */

$plugins->add_hook( 'global_end', 'optawards_global_end_18', 10 );

function optawards_global_end_18()
{
	global $templates, $db, $cache, $lang, $pending_award_requests, $mybb;
	
	if ( THIS_SCRIPT != 'index.php' )
		return;
	
	$lang->load( 'optawards' );
	
	$awardrequests = 0;
	
	$usergroups = optawards_get_usergroups( $mybb->user[ 'uid' ] );
	$groupslead = optawards_get_groupslead( $mybb->user[ 'uid' ] );
	$granter    = optawards_is_granter( $mybb->user[ 'uid' ], $usergroups );
	if ( empty( $groupslead ) && empty( $granter ) )
	{
		return;
	}
	
	if ( !$granter )
	{
		$query = $db->write_query( '
        SELECT * FROM ' . TABLE_PREFIX . 'award_requests AS r JOIN ' . TABLE_PREFIX . 'awards AS a ON r.aid = a.aid WHERE status="new"
        ' );
		while ( $request = $db->fetch_array( $query ) )
		{
			$usergroups = explode( ',', $request[ 'usergroups' ] );
			foreach ( $usergroups as $usergroup )
			{
				if ( in_array( $usergroup, $groupslead ) )
				{
					$awardrequests++;
				}
			}
		}
	}
	else
	{
		$query         = $db->simple_select( 'award_requests', 'count(*) as requests', 'status="new"' );
		$awardrequests = $db->fetch_field( $query, 'requests' );
	}
	$db->free_result( $query );
	
	if ( $awardrequests > 0 )
	{
		$pending_award_requests = optawards_fill_placeholders( $pending_award_requests, array(
			 'awardrequests' => intval( $awardrequests ) 
		) );
	}
	else
	{
		$pending_award_requests = '';
	}
}

/* --- Hook #21 - optawards_cache_templates --- */

$plugins->add_hook( 'global_start', 'optawards_global_start_21', 10 );

function optawards_global_start_21()
{
	global $templatelist, $mybb;
	
	if ( THIS_SCRIPT == 'showthread.php' )
	{
		$templatelist .= ', postbit_optawards_award, postbit_optawards';
	}
	if ( THIS_SCRIPT == 'member.php' && $mybb->input[ 'action' ] == 'profile' )
	{
		$templatelist .= ', postbit_optawards_award';
	}
}

/* --- Hook #20 - optawards_postbit --- */

$plugins->add_hook( 'postbit', 'optawards_postbit_20', 10 );
$plugins->add_hook( 'postbit_prev', 'optawards_postbit_20', 10 );
$plugins->add_hook( 'postbit_pm', 'optawards_postbit_20', 10 );
$plugins->add_hook( 'postbit_announcement', 'optawards_postbit_20', 10 );

function optawards_postbit_20( &$post )
{
	global $mybb, $cache, $templates, $lang;
	
	$lang->load( 'optawards' );
	
	$post[ 'optawards' ] = '';
	$awardicons          = '';
	
	$awards         = $cache->read( 'optawards' );
	$awards_granted = $cache->read( 'optawards_granted' );
	
	if ( empty( $post[ 'uid' ] ) )
	{
		$post[ 'uid' ] = -1;
	}
	
	if ( empty( $awards_granted[ $post[ 'uid' ] ] ) )
		return;
	
	$count = 0;
	foreach ( $awards_granted[ $post[ 'uid' ] ] as $aid => $date )
	{
		if ( $awards[ $aid ][ 'visibility' ] == 1 || $awards[ $aid ][ 'visibility' ] == 3 )
		{
			$count++;
			if ( $count <= $mybb->settings[ 'optawards_display_postbit' ] || $mybb->settings[ 'optawards_display_postbit' ] == -1 )
			{
				eval( "\$awardicons .= \"" . $templates->get( "optawards_postbit_award" ) . "\";" );
			}
		}
	}
	if ( $count > 0 )
	{
		$post[ 'optawards' ] = '<br>';
		eval( "\$post['optawards'] .= \"" . $templates->get( "optawards_postbit" ) . "\";" );
	}
}

/* --- Hook #22 - optawards_member_profile --- */

$plugins->add_hook( 'member_profile_end', 'optawards_member_profile_end_22', 10 );

function optawards_member_profile_end_22()
{
	global $cache, $lang, $mybb, $memprofile, $db, $templates, $theme;
	
	$lang->load( 'optawards' );
	
	$memprofile[ 'optawards' ] = '';
	$awardlist                 = '';
	
	$max_profile = intval( $mybb->settings[ 'optawards_display_profile' ] );
	
	if ( $max_profile > 0 || $max_profile == -1 )
	{
		// $awards=$cache->read('optawards');
		// $awards_granted=$cache->read('optawards_granted');
		
		$query = $db->write_query( '
			SELECT * FROM ' . TABLE_PREFIX . 'awards_granted AS g
			JOIN ' . TABLE_PREFIX . 'awards AS a
				ON g.aid=a.aid
			WHERE g.uid=' . intval( $memprofile[ 'uid' ] ) . '
				AND (visibility = 1 OR visibility = 2)
			ORDER BY g.date_given DESC
		' );
		
		$count = 0;
		while ( $award = $db->fetch_array( $query ) )
		{
			$count++;
			if ( $count <= $max_profile || $max_profile == -1 )
			{
				$trow = alt_trow();
				eval( "\$awardlist .= \"" . $templates->get( "optawards_member_profile_row" ) . "\";" );
			}
		}
		$db->free_result( $query );
		if ( $count == 0 )
		{
			eval( "\$awardlist .= \"" . $templates->get( "optawards_member_profile_empty" ) . "\";" );
		}
	}
	
	$lang->optawards_profile_title = optawards_fill_placeholders( $lang->optawards_profile_title, array(
		 'username' => htmlspecialchars_uni( $memprofile[ 'username' ] ) 
	) );
	eval( "\$memprofile['optawards'] .= \"" . $templates->get( "optawards_member_profile" ) . "\";" );
	
}

$plugins->add_hook('admin_config_permissions','optawards_admin_permissions');

function optawards_admin_permissions(&$admin_permissions)
{
	global $lang;
	
	$lang->load('optawards');
	
	$admin_permissions['optawards']=$lang->optawards_can_manage_awards;
}

// Award Processing Functions *********************************************************************************

function optawards_process_accepted_award_requests()
{
	global $db, $page, $error_handler;
	
	$query = $db->simple_select( 'award_requests', 'arid, last_processed_by', 'status="accepted"', array(
		 'order_by' => 'arid',
		'order_dir' => 'ASC' 
	) );
	while ( $arid = $db->fetch_field( $query, 'arid' ) )
	{
		$error = optawards_process_award_request( $arid );
		if ( $error[ 'failure' ] )
		{
			optawards_warning( $error[ 'message' ] );
		}
	}
	$db->free_result( $query );
}

function optawards_process_award_request( $arid )
{
	global $db, $lang, $cache, $mybb, $page;
	
	$result_ok  = array(
		 'failure' => false,
		'message' => '' 
	);
	$result_nok = array(
		 'failure' => true,
		'message' => 'to be filled' 
	);
	
	$lang->load( 'optawards' );
	
	/* gather all data for the request:
	 ** - award: ID, Name, Description and Icon
	 ** - recipient: ID and Name
	 ** - requestor: ID and Name
	 ** - request date
	 ** - processor: Name (the ID is given as a parameter)
	 ** - action
	 ** - status of the request (before we process it)
	 ** - reason
	 ** - message templates (for the PM)
	 */
	
	/* loading the award request will get us
	 ** - award: ID
	 ** - recipient: ID
	 ** - requestor: ID
	 ** - request date
	 ** - action
	 ** - status of the request
	 ** - reason
	 */
	$query         = $db->simple_select( 'award_requests', '*', 'arid=' . intval( $arid ) );
	$award_request = $db->fetch_array( $query );
	$db->free_result( $query );
	$processor_id = $award_request[ 'last_processed_by' ];
	if ( empty( $processor_id ) )
		$processor_id = -1;
	
	// only process accepted requests!
	if ( $award_request[ 'status' ] != 'accepted' )
	{
		mydump( $award_request[ 'status' ], '$award_request[\'status\'] (arid=' . $arid . ')' );
		$result_nok[ 'message' ] = optawards_fill_placeholders( $lang->optawards_error_award_not_accepted, array(
			 'arid' => $arid 
		) );
		return $result_nok;
	}
	
	/* get all the names and extra data
	 ** - award: Name, Description and Icon
	 ** - recipient: Name
	 ** - requestor: Name
	 ** - processor: Name
	 ** - message template of the award
	 */
	$query      = $db->simple_select( 'awards', '*', 'aid=' . intval( $award_request[ 'aid' ] ) );
	$award_data = $db->fetch_array( $query );
	$db->free_result( $query );
	$recipient = optawards_get_username_by_uid( $award_request[ 'requested_for' ] );
	if ( empty( $recipient ) ) // invalid recipient
	{
		optawards_update_award_requests( $arid, 'failed', $processor_id, 0 );
		$result_nok[ 'message' ] = optawards_fill_placeholders( $lang->optawards_error_invalid_recipient, array(
			 'uid' => $award_request[ 'requested_for' ] 
		) );
		return $result_nok;
	}
	$requestor = optawards_get_username_by_uid( $award_request[ 'requested_by' ] );
	$processor = optawards_get_username_by_uid( $processor_id );
	if ( $processor == $lang->optawards_pm_unknown_requestor )
		$processor = $lang->optawards_pm_mybb_engine;
	
	// build Icon string
	$icon = $award_data[ 'iconlarge' ];
	
	// build message
	$placeholders = array(
		 'default' => $mybb->settings[ 'optawards_admin_pm_default' ],
		'recipient' => $recipient,
		'requestor' => $requestor,
		'processor' => $processor,
		'award' => $award_data[ 'name' ],
		'awardinfo' => $mybb->settings[ 'bburl' ] . '/misc.php?action=viewaward&aid=' . $award_data[ 'aid' ],
		'description' => $award_data[ 'description' ],
		'icon' => $icon,
		'reason' => $award_request[ 'reason' ],
		'date' => $award_request[ 'date_requested' ] 
	);
	
	if ( $award_request[ 'action_requested' ] == 'grant' )
	{
		// should be an atomic operation...
		$agid = optawards_add_granted_award( $award_request[ 'requested_for' ], $award_request[ 'aid' ], $award_request[ 'reason' ], $award_request[ 'requested_by' ], $processor_id, $arid );
		optawards_update_award_requests( $arid, 'granted', $processor_id, $agid );
		
		// tell the user about his new award
		$subject = optawards_fill_placeholders( $lang->optawards_pm_given_subject, $placeholders );
		$message = optawards_fill_placeholders( $award_data[ 'pm' ], $placeholders );
		optawards_update_award_count( $award_request[ 'aid' ] );
		$result = optawards_send_pm( $award_request[ 'requested_for' ], $processor_id, $subject, $message, $mybb->settings[ 'optawards_admin_pmicon' ] );
		if ( $result[ 'messagesent' ] != 1 )
		{
			$result_nok[ 'message' ] = optawards_fill_placeholders( $lang->optawards_error_award_pm_failed, array(
				 'action' => $award_request[ 'action_requested' ] 
			) );
			return $result_nok;
		}
	}
	elseif ( $award_request[ 'action_requested' ] == 'revoke' )
	{
		// should be an atomic operation...
		optawards_remove_granted_award( $award_request[ 'agid' ] );
		optawards_update_award_requests( $arid, 'revoked', $processor_id );
		
		// we don't tell the user he lost an award
	}
	else // we never should be here!
	{
		$result_nok[ 'message' ] = optawards_fill_placeholders( $lang->optawards_error_award_action_unknown, array(
			 'action' => $award_request[ 'action_requested' ] 
		) );
		return $result_nok;
	}
	
	// all things went good
	optawards_cache_awards_granted();
	return $result_ok;
}

function optawards_update_award_count( $aid )
{
	global $db;
	
	$query = $db->simple_select( 'awards_granted', 'count(aid) as aid_count', 'aid=' . intval( $aid ) );
	$count = $db->fetch_field( $query, 'aid_count' );
	$db->free_result( $query );
	$db->update_query( 'awards', array(
		 'recipients' => $count 
	), 'aid=' . intval( $aid ) );
}

function optawards_add_award_request( $aid, $recipient, $action, $requested_by, $reason )
{
	global $db;
	$new_record = array(
		 'aid' => intval( $aid ),
		'requested_for' => intval( $recipient ),
		'action_requested' => $db->escape_string( $action ),
		'requested_by' => intval( $requested_by ),
		'reason' => $db->escape_string( $reason ) 
	);
	$db->insert_query( 'award_requests', $new_record );
	return $db->insert_id();
}

function optawards_update_award_requests( $arid, $status, $last_processed_by, $agid = 0 )
{
	global $db;
	
	date_default_timezone_set( 'UTC' );
	
	$update_record = array(
		 'status' => $db->escape_string( $status ),
		'last_processed_by' => intval( $last_processed_by ),
		'date_last_processed' => date( 'Y-m-d H:i:s' ) 
	);
	if ( $agid > 0 )
	{
		$update_record[ 'agid' ] = $agid;
	}
	$db->update_query( 'award_requests', $update_record, 'arid=' . $arid );
}

function optawards_add_granted_award( $recipient, $aid, $reason, $requested_by, $given_by, $arid )
{
	global $db;
	
	$db->insert_query( 'awards_granted', array(
		 'uid' => intval( $recipient ),
		'aid' => intval( $aid ),
		'reason' => $db->escape_string( $reason ),
		'requested_by' => intval( $requested_by ),
		'arid' => intval( $arid ),
		'given_by' => intval( $given_by ) 
	) );
	return $db->insert_id();
}

function optawards_remove_granted_award( $agid )
{
	global $db;
	
	$db->delete_query( 'awards_given', array(
		 'agid' => intval( $agid ) 
	) );
}

function optawards_get_award_info( $aid )
{
	global $db;
	
	$query = $db->simple_select( 'awards', '*', 'aid=' . intval( $aid ) );
	$award = $db->fetch_array( $query );
	$db->free_result( $query );
	return $award;
}

function optawards_cache_awards( $clear = false )
{
	global $cache;
	if ( $clear == true )
	{
		$cache->update( 'optawards', false );
	}
	else
	{
		global $db;
		$awards = array();
		$query  = $db->simple_select( 'awards', 'aid,name,icon,iconlarge,usergroups,visibility' );
		while ( $award = $db->fetch_array( $query ) )
		{
			$awards[ $award[ 'aid' ] ] = array(
				 'name' => $award[ 'name' ],
				'icon' => $award[ 'icon' ],
				'iconlarge' => $award[ 'iconlarge' ],
				'usergroups' => $award[ 'usergroups' ],
				'visibility' => $award[ 'visibility' ] 
			);
		}
		$db->free_result( $query );
		$cache->update( 'optawards', $awards );
	}
}

function optawards_cache_awards_granted( $clear = false )
{
	global $cache;
	if ( $clear == true )
	{
		$cache->update( 'optawards_granted', false );
	}
	else
	{
		global $db;
		$awards = array();
		$query  = $db->simple_select( 'awards_granted', 'uid,aid,date_given', '', array(
			 'order_by' => 'date_given',
			'order_dir' => 'DESC' 
		) );
		while ( $award = $db->fetch_array( $query ) )
		{
			$awards[ $award[ 'uid' ] ][ $award[ 'aid' ] ] = $award[ 'date_given' ];
		}
		$db->free_result( $query );
		$cache->update( 'optawards_granted', $awards );
	}
}

// Forum helper functions ****************************************************************************

// get username
function optawards_get_username_by_uid( $uid )
{
	global $db;
	
	$query  = $db->simple_select( 'users', 'username', 'uid=' . intval( $uid ) );
	$result = $db->fetch_field( $query, 'username' );
	$db->free_result( $query );
	if ( empty( $result ) )
		$result = $lang->optawards_pm_unknown_requestor;
	return $result;
}

function optawards_get_user_by_uid( $uid )
{
	global $db;
	
	$query  = $db->simple_select( 'users', '*', 'uid=' . intval( $uid ) );
	$result = $db->fetch_array( $query );
	$db->free_result( $query );
	return $result;
}

function optawards_get_uid_by_username( $username )
{
	global $db;
	
	$query  = $db->simple_select( 'users', 'uid', 'username="' . $db->escape_string( $username ) . '"' );
	$result = $db->fetch_field( $query, 'uid' );
	$db->free_result( $query );
	return $result;
}

function optawards_get_usergroups( $uid )
{
	global $db;
	
	$query = $db->simple_select( 'users', 'usergroup, additionalgroups', 'uid=' . intval( $uid ) );
	$data  = $db->fetch_array( $query );
	$db->free_result( $query );
	$usergroups = explode( ',', $data[ 'usergroup' ] . ',' . $data[ 'additionalgroups' ] );
	return $usergroups;
}

function optawards_get_groupslead( $uid )
{
	global $cache;
	
	$groupleaders = $cache->read( 'groupleaders' );
	$groupslead   = array();
	
	$groupleaders = $groupleaders[ $uid ];
	
	if ( !empty( $groupleaders ) )
	{
		foreach ( $groupleaders as $groupdata )
		{
			$groupslead[] = $groupdata[ 'gid' ];
		}
	}
	return $groupslead;
}

function optawards_is_granter( $uid, $usergroups )
{
	global $mybb;
	$granters = explode( ',', $mybb->settings[ 'optawards_admin_granters' ] );
	$granter  = false;
	foreach ( $granters as $tgrant )
	{
		if ( in_array( $tgrant, $usergroups ) )
			$granter = true;
	}
	return $granter;
}

// send a PM
function optawards_send_pm( $recipient, $sender, $subject, $message, $icon = 0 )
{
	global $mybb, $lang, $cache;
	
	// Check if send this award.
	if ( $mybb->settings[ 'enablepms' ] != 1 )
	{
		return false;
	}
	
	// We are ready to send it.
	require_once MYBB_ROOT . "inc/datahandlers/pm.php";
	$pmhandler = new PMDataHandler();
	
	// build PM data
	
	// recipient
	$toid   = array();
	$toid[] = intval( $recipient );
	
	// sender
	// Figure out if to use current connected user as PM sender.
	$fromid = intval( $mybb->settings[ 'optawards_admin_pmuserid' ] );
	if ( $mybb->settings[ 'optawards_admin_pmuser' ] == 1 && $mybb->user[ 'uid' ] )
	{
		$fromid = $sender;
	}
	elseif ( $fromid < 1 )
	{
		$fromid = -1;
	}
	$pm = array(
		 'subject' => $subject,
		'message' => $message,
		'icon' => $icon,
		'fromid' => intval( $fromid ),
		'toid' => $toid 
	);
	
	$pmhandler->admin_override = true;
	$pmhandler->set_data( $pm );
	
	if ( !$pmhandler->validate_pm() )
	{
		$pmhandler->is_validated = true;
		$pmhandler->errors       = array();
	}
	$pminfo = $pmhandler->insert_pm();
	return $pminfo;
}

// String Processing Funtions ******************************************************************************

// replace the placeholders by their content/values
function optawards_fill_placeholders( $parseme, $placeholders = array() )
{
	if ( !empty( $parseme ) )
	{
		foreach ( $placeholders as $key => $value )
		{
			$parseme = str_replace( '{' . $key . '}', $value, $parseme );
		}
	}
	
	return $parseme;
}

// Parse data with the mybb parser
function optawards_parse_text( $message )
{
	global $parser;
	if ( !is_object( $parser ) )
	{
		require_once MYBB_ROOT . 'inc/class_parser.php';
		$parser = new postParser;
	}
	$parser_options = array(
		 'allow_html' => 0,
		'allow_smilies' => 1,
		'allow_mycode' => 1,
		'filter_badwords' => 1,
		'shorten_urls' => 1 
	);
	$message        = $parser->parse_message( $message, $parser_options );
	return $message;
}

// display or log warnings
function optawards_warning( $message )
{
	global $error_handler;
	$error_handler->error( E_USER_WARNING, $message );
}

// templates are a big mess so I put it to the end of the file
function optawards_setup_templates()
{
	global $PL;

	$PL->templates(
		'optawards',
		'OPT Awards',
		array(
			'postbit' => '<div class="awards">
'.					'<fieldset class="awards">
'.					'<legend class="awards">{$lang->optawards_postbit_label}</legend>
'.					'{$awardicons}
'.					'</fieldset>
'.					'</div>',
			'postbit_award' => '<a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$aid}" title="{$awards[$aid][\'name\']}"><img src="{$awards[$aid][\'icon\']}" alt="{$awards[$aid][\'name\']}" class="award" /></a>',
			'select_user' => '<tr class>
'.					'	<td class="trow1" style=""><strong>{$lang->optawards_username}</strong></label>
'.					'		<div class="description">{$lang->optawards_username_recipient}</div>
'.					'		<div class="form_row"><input type="text" name="username" value="" class="text_input"></div>
'.					'	</td>
'.					'</tr>',
			'select_user_hidden' => '<tr class>
'.					'	<td class="trow1" style=""><strong>{$lang->optawards_username}</strong></label>
'.					'		<div class="form_row"><input type="hidden" name="username" value="{$username}"><em>{$username}</em></div>
'.					'	</td>
'.					'</tr>',
			'awards_page' => '<html>
'.					'	<head>
'.					'		<title>{$mybb->settings[\'bbname\']} - {$lang->optawards_page_title}</title>
'.					'		{$headerinclude}
'.					'	</head>
'.					'	<body>
'.					'		{$header}
'.					'		{$content}
'.					'		{$footer}
'.					'	</body>
'.					'</html>',
			'awards_list' => '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" colspan="5">
'.					'			<strong>{$award_class[\'name\']} - {$award_class[\'description\']}</strong>
'.					'		</td>
'.					'	</tr>
'.					'	<tr>
'.					'		<td class="tcat smalltext" align="center" width="1%"><strong>{$lang->optawards_page_list_award}</strong></td>
'.					'		<td class="tcat smalltext" width="15%"><strong>{$lang->optawards_page_list_name}</strong></td>
'.					'		<td class="tcat smalltext"><strong>{$lang->optawards_page_list_description}</strong></td>
'.					'		<td class="tcat smalltext" align="center"><strong>{$lang->optawards_page_list_recipients}</strong></td>
'.					'		<td class="tcat smalltext" align="center"><strong>{$lang->optawards_page_list_actions}</strong></td>
'.					'	</tr>
'.					'	{$award_list}
'.					'</table>
'.					'<br>',
			'awards_list_row' => '<tr>
'.					'	<td class="{$trow}" align="center"><a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$award[\'aid\']}" title="{$award[\'name\']}"><img src="{$award[\'iconlarge\']}" alt="{$award[\'name\']}" class="awardimglarge"/></a></td>
'.					'	<td class="{$trow}"><a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$award[\'aid\']}" title="{$award[\'name\']}">{$award[\'name\']}</a></td>
'.					'	<td class="{$trow}">{$award[\'description\']}</td>
'.					'	<td class="{$trow}" align="center"><a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$award[\'aid\']}" title="{$lang->optawards_page_list_show_recipients}">{$award[\'recipients\']}</a></td>
'.					'	<td class="{$trow}" align="center"><div class="awardactions">{$awardactions}</div></td>
'.					'</tr>',
			'awards_list_row_action' => '<a href="{$mybb->settings[\'bburl\']}/misc.php?action={$awardactioncmd}&aid={$award[\'aid\']}">{$awardactiontext}</a>',
			'awards_list_empty' => '<tr>
'.					'	<td class="trow1" colspan="6" align="center">
'.					'		{$lang->optawards_page_list_empty}
'.					'	</td>
'.					'</tr>',
			'award_view' => '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" width="15%"><strong>{$lang->optawards_page_list_name}</strong></td>
'.					'		<td class="thead" align="center" width="1%"><strong>{$lang->optawards_award_iconlarge}</strong></td>
'.					'		<td class="thead" align="center" width="1%"><strong>{$lang->optawards_award_icon}</strong></td>
'.					'		<td class="thead"><strong>{$lang->optawards_page_list_description}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_award_class}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_award_usergroups2}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_page_list_recipients}</strong></td>
'.					'	</tr>
'.					'<tr>
'.					'	<td class="tcat ">{$award[\'name\']}</td>
'.					'	<td class="tcat " align="center"><img src="{$award[\'iconlarge\']}" alt="{$award[\'name\']}" class="awardimglarge"/></td>
'.					'	<td class="tcat " align="center"><img src="{$award[\'icon\']}" alt="{$award[\'name\']}" class="awardimgsmall"/></td>
'.					'	<td class="tcat ">{$award[\'description\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'class\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'usergroups\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'recipients\']}</td>
'.					'</tr>
'.					'</table>
'.					'<br>
'.					'<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" width="15%"><strong>{$lang->optawards_username}</strong></td>
'.					'		<td class="thead"><strong>{$lang->optawards_reason}</strong></td>
'.					'		<td class="thead" align="center" width="20%"><strong>{$lang->optawards_page_view_date}</strong></td>
'.					'	</tr>
'.					'	{$users_list}
'.					'</table>',
			'award_view_row' => '<tr>
'.					'	<td class="{$trow}">{$award_granted[\'username\']}</td>
'.					'	<td class="{$trow}">{$award_granted[\'reason\']}</td>
'.					'	<td class="{$trow}" align="center">{$award_granted[\'date_given\']}</td>
'.					'</tr>',
			'award_view_empty' => '<tr>
'.					'	<td class="trow1" colspan="3" align="center">{$lang->optawards_page_view_empty}</td>
'.					'</tr>',
			'user_view' => '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" colspan="4">
'.					'			<strong>{$username}</strong>
'.					'		</td>
'.					'	</tr>
'.					'	<tr>
'.					'		<td class="tcat smalltext" align="center" width="1%"><strong>{$lang->optawards_page_list_award}</strong></td>
'.					'		<td class="tcat smalltext"><strong>{$lang->optawards_reason}</strong></td>
'.					'		<td class="tcat smalltext" align="center" width="20%"><strong>{$lang->optawards_page_view_date}</strong></td>
'.					'	</tr>
'.					'	{$awards_list}
'.					'</table>',
			'user_view_row' => '<tr>
'.					'	<td class="{$trow}" align="center"><a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$award[\'aid\']}" title="{$award[\'name\']}"><img src="{$award[\'iconlarge\']}" alt="{$award[\'name\']}" /></a></td>
'.					'	<td class="{$trow}">{$award[\'reason\']}</td>
'.					'	<td class="{$trow}" align="center">{$award[\'date_given\']}</td>
'.					'</tr>',
			'user_view_empty' => '<tr>
'.					'	<td class="trow1" colspan="3" align="center">{$lang->optawards_page_list_empty}</td>
'.					'</tr>',
			'requests_pending' => '<div class="pm_alert" id="pm_notice">
'.					'	<div class="float_right"><a href="misc.php?action=processawardrequests" title="{$lang->optawards_pending_award_requests}"></div>
'.					'	<div>{$privatemessage_text}</div>
'.					'</div>
'.					'<br />',
			'requests' => '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" colspan="5">
'.					'			<strong>{$lang->optawards_requests_pending}</strong>
'.					'		</td>
'.					'	</tr>
'.					'	<tr>
'.					'		<td class="tcat smalltext" width="1%"><strong>{$lang->optawards_username}</strong></td>
'.					'		<td class="tcat smalltext" align="center" width="1%"><strong>{$lang->optawards_page_list_award}</strong></td>
'.					'		<td class="tcat smalltext" width="15%"><strong>{$lang->optawards_page_list_name}</strong></td>
'.					'		<td class="tcat smalltext"><strong>{$lang->optawards_reason}</strong></td>
'.					'		<td class="tcat smalltext" align="center" width="1%"><strong>{$lang->optawards_page_list_actions}</strong></td>
'.					'	</tr>
'.					'	{$award_requests}
'.					'</table>
'.					'<br>',
			'requests_row' => '<tr>
'.					'	<td class="{$trow} ">{$award_request[\'username\']}</td>
'.					'	<td class="{$trow}" align="center" width="1%"><img src="{$iconlarge}" class="awardimglarge"></td>
'.					'	<td class="{$trow}"><strong>{$awardname}</strong></td>
'.					'	<td class="{$trow}"><strong>{$award_request[\'reason\']}</strong></td>
'.					'	<td class="{$trow}" align="center"><div class="awardactions">
'.					'		<a href="misc.php?action=processawardrequests&arid={$award_request[\'arid\']}&par=grant">grant request</a>
'.					'		<br>
'.					'		<a href="misc.php?action=processawardrequests&arid={$award_request[\'arid\']}&par=deny">deny request</a>
'.					'	</div></td>
'.					'</tr>',
			'requests_empty' => '<tr>
'.					'	<td class="trow1" colspan="5" align="center">{$lang->optawards_requests_empty}</td>
'.					'</tr>',
			'request_denied_form' => '<form action="misc.php?action=processawardrequests&arid={$arid}&par=deny" method="post">
'.					'<input type="hidden" name="my_post_key" value="{$mybb->post_code}">
'.					'<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" width="100%" colspan="2"><strong>{$lang->optawards_deny_award}</strong></td>
'.					'	</tr>
'.					'	{$select_user}
'.					'	<tr>
'.					'		<td class="trow2" style=""><strong>{$lang->optawards_reason}</strong>
'.					'			<div class="description">{$lang->optawards_deny_reason_description}</div>
'.					'			<div class="form_row"><textarea cols="80" rows="15" name="reason" id="reason">{$reason}</textarea></div>
'.					'		</td>
'.					'		<td class="trow2" style=""><fieldset class="pm_template">
'.					'			<legend>PM Template:</legend>
'.					'			<div>{$pm_deny_template}</div>
'.					'		</fieldset></td>
'.					'	</tr>
'.					'	<tr>
'.					'		<td class="trow1" colspan="2">
'.					'			<input type="submit" value="Submit" name="submit" class="submit_button">
'.					'			<input type="submit" value="Cancel" name="submit" class="submit_button">
'.					'			<input type="reset" value="Reset" class="submit_button">
'.					'		</td>
'.					'	</tr>
'.					'</table>
'.					'</form>',
			'request_award_form' => '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" width="15%"><strong>{$lang->optawards_page_list_name}</strong></td>
'.					'		<td class="thead" align="center" width="1%"><strong>{$lang->optawards_award_iconlarge}</strong></td>
'.					'		<td class="thead" align="center" width="1%"><strong>{$lang->optawards_award_icon}</strong></td>
'.					'		<td class="thead"><strong>{$lang->optawards_page_list_description}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_award_class}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_award_usergroups2}</strong></td>
'.					'		<td class="thead" align="center"><strong>{$lang->optawards_page_list_recipients}</strong></td>
'.					'	</tr>
'.					'<tr>
'.					'	<td class="tcat ">{$award[\'name\']}</td>
'.					'	<td class="tcat " align="center"><img src="{$award[\'iconlarge\']}" alt="{$award[\'name\']}" class="awardimglarge"/></td>
'.					'	<td class="tcat " align="center"><img src="{$award[\'icon\']}" alt="{$award[\'name\']}" class="awardimgsmall"/></td>
'.					'	<td class="tcat ">{$award[\'description\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'class\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'usergroups\']}</td>
'.					'	<td class="tcat " align="center">{$award[\'recipients\']}</td>
'.					'</tr>
'.					'</table>
'.					'<br>
'.					'<form action="misc.php?action={$awardaction}&aid={$award[\'aid\']}" method="post">
'.					'<input type="hidden" name="my_post_key" value="{$mybb->post_code}">
'.					'<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'	<tr>
'.					'		<td class="thead" width="100%"><strong>{$lang->optawards_award_data}</strong></td>
'.					'	</tr>
'.					'	{$select_user}
'.					'	<tr>
'.					'		<td class="trow2" style=""><strong>{$lang->optawards_reason}</strong></label>
'.					'			<div class="description">{$lang->optawards_reason_description}</div>
'.					'			<div class="form_row"><textarea cols="80" rows="15" name="reason" id="reason">{$reason}</textarea></div>
'.					'		</td>
'.					'	</tr>
'.					'	<tr>
'.					'		<td class="trow1"><input type="submit" value="Submit" class="submit_button"><input type="reset" value="Reset" class="submit_button"></td>
'.					'	</tr>
'.					'</table>
'.					'</form>',
			'member_profile' => '<br />
'.					'<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
'.					'<tr>
'.					'<td class="thead" colspan="2"><a style="float:right;" href="{$mybb->settings[\'bburl\']}/misc.php?action=viewuserawards&uid={$memprofile[\'uid\']}">{$lang->optawards_profile_viewall}</a><strong>{$lang->optawards_profile_title}</strong></td>
'.					'</tr>
'.					'{$awardlist}
'.					'</table>',
			'member_profile_row' => '<tr>
'.					'	<td class="tcat" rowspan="2" width="1">
'.					'		<a href="{$mybb->settings[\'bburl\']}/misc.php?action=viewaward&aid={$award[\'aid\']}" title="{$award[\'name\']}"><img src="{$award[\'iconlarge\']}" alt="{$award[\'name\']}" class="awardimglarge"/></a>
'.					'	</td>
'.					'	<td class="{$trow} smalltext" >
'.					'		<span style="float:right;">{$award[\'date_given\']}</span> {$award[\'name\']}
'.					'	</td>
'.					'</tr>
'.					'<tr>
'.					'	<td class="{$trow}" >
'.					'		{$award[\'reason\']}
'.					'	</td>
'.					'</tr>',
			'member_profile_empty' => '<tr>
'.					'	<td class="trow1" colspan="2">
'.					'		{$lang->optawards_profile_empty}
'.					'	</td>
'.					'</tr>'
		)
	);
}

function optawards_setup_stylessheet()
{
	global $PL;
	
	$styles=array(
		'div.awards' => array(
			'white-space' => 'normal',
			'max-width' => '162px',
			'margin-left' => 'auto',
			'margin-right' => 'auto'
		),
		'div.awardactions' => array(
			'white-space' => 'nowrap'
		),
		'span.awardaction_grant' => array(
			'color' => '#ff0000'
		),
		'fieldset.awards' => array(
			'padding' => '2px'
		),
		'legend.awards' => array(
			'text-align' => 'left'
		),
		'img.award' => array(
			'max-width' => '50px',
			'height' => '20px',
			'float' => 'left'
		),
		'img.awardimglarge' => array(
			'max-width' => '150px',
			'max-height' => '150px',
			'display' => 'block',
			'margin-left' => 'auto',
			'margin-right' => 'auto'
		),
		'img.awardimgsmall' => array(
			'max-width' => '50px',
			'max-height' => '20px',
			'display' => 'block',
			'margin-left' => 'auto',
			'margin-right' => 'auto'
		)
	);
	$PL->stylesheet(
		'optawards',
		$styles
	);
}


/* Exported by Hooks plugin Mon, 02 Sep 2013 09:26:22 GMT */
?>
