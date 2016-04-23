<?php

// english

// Awards ACP

$l['optawards_title'] = 'OPT Awards';
$l['optawards_task_description'] = 'process all pending accepted award request';

$l['optawards_list_awards'] = 'List Awards';
$l['optawards_list_awards_description'] = 'Customize Awards';
$l['optawards_table_awards'] = 'Available Awards';

$l['optawards_add_award'] = 'Add Award';
$l['optawards_award_add'] = 'Add Award';
$l['optawards_add_award_description'] = 'Add a new Award';
$l['optawards_award_added'] = 'Award added';

$l['optawards_edit_award'] = 'Edit Award';
$l['optawards_award_edit'] = 'Edit Award';
$l['optawards_edit_award_description'] = 'Edit an Award';
$l['optawards_award_edited'] = 'Award edited';

$l['optawards_delete_award'] = 'Delete Award';
$l['optawards_delete_award_description'] = 'Delete an Award';
$l['optawards_confirm_deleteaward'] = 'Do you really want to delete this award?';

$l['optawards_error_no_award_name'] = 'missing name for the award';
$l['optawards_error_no_award_icon'] = 'missing small icon URL';
$l['optawards_error_no_award_iconlarge'] = 'missing large icon URL';

$l['optawards_award_name'] = 'Name';
$l['optawards_award_description'] = 'Description';
$l['optawards_award_class'] = 'Award Class';
$l['optawards_award_icon'] = 'Small Icon';
$l['optawards_award_iconlarge'] = 'Large Icon';
$l['optawards_award_usergroups'] = 'Usergroups';
$l['optawards_award_usergroups2'] = 'Requestable by';
$l['optawards_award_pm_template'] = 'PM Template<p>you can use BB code and those placeholders:
</p>
Default:<ul>
<li>{default} - use the global award template</li>
</ul>
Names:<ul>
<li>{recipient} - name of the award recipient</li>
<li>{requestor} - name of who requested the award</li>
<li>{processor} - who processed/granted the award</li>
</ul>
Award Data:<ul>
<li>{award} - name of the award</li>
<li>{description} - description of the awards</li>
<li>{reason} - why was the award requested?</li>
<li>{icon} - icon of the award (large version)</li>
<li>{date} - when was the award requested?</li>
<li>{awardinfo} - link to the awards information page</li>
</ul>';
$l['optawards_award_recipients'] = '# Recipients';
$l['optawards_award_visibility'] = 'Visibility';
$l['optawards_award_displayorder'] = 'Displayorder';

$l['optawards_visibility_everywhere'] = 'everywhere';
$l['optawards_visibility_usercp'] = 'UserCP only';
$l['optawards_visibility_postbit'] = 'PostBit only';
$l['optawards_visibility_invisible'] = 'invisible';


// Award Classes ACP

$l['optawards_list_classes'] = 'Award Classes';
$l['optawards_list_classes_description'] = 'available Award Classes';
$l['optawards_table_award_classes'] = 'Available Award Classes';

$l['optawards_add_class'] = 'Add Award Class';
$l['optawards_award_class_add'] = 'Add Award Class';
$l['optawards_add_class_description'] = 'Add a new Award Class';
$l['optawards_award_class_added'] = 'Award Class added';

$l['optawards_edit_class'] = 'Edit Award Class';
$l['optawards_award_class_edit'] = 'Edit Award Class';
$l['optawards_edit_class_description'] = 'Edit an Award Class';
$l['optawards_award_class_edited'] = 'Award Class edited';

$l['optawards_delete_class'] = 'Delete Award Class';
$l['optawards_delete_class_description'] = 'Delete an Award Class';
$l['optawards_confirm_deleteclass'] = 'Do you really want to delete this Award Class?';

$l['optawards_error_no_class_name'] = 'missing name for the award class';
$l['optawards_error_no_class_singular'] = 'missing singular name for the award class';

$l['optawards_award_class_name'] = 'Name';
$l['optawards_award_class_singular'] = 'Singular Name';
$l['optawards_award_class_description'] = 'Description';
$l['optawards_award_class_icon'] = 'Icon';
$l['optawards_award_class_displayorder'] = 'Displayorder';


// General

$l['optawards_update_order'] = 'Update Display Order';
$l['optawards_delete_not_implemented'] = 'Delete Function not implemented';
$l['optawards_all_user_groups'] = 'All User Groups';
$l['optawards_no_groups'] = 'No User Groups';

// PMs
$l['optawards_pm_noreason'] = 'no reason given';
$l['optawards_pm_given_subject'] = '!Dir wurde die Auszeichnung "{award}" verliehen!';
$l['optawards_pm_unknown_requestor'] = 'unknown';
$l['optawards_pm_mybb_engine'] = 'MyBB Engine';

// process awards task
$l['optawards_error_award_not_accepted'] = 'award request "{arid}" is not "accepted"';
$l['optawards_error_award_action_unknown'] = 'unknown award request action "{action}"';
$l['optawards_error_award_pm_failed'] = 'award request "{action}" failed';
$l['optawards_error_invalid_recipient'] = 'award request for UID {uid} failed: UID unknown';


// misc.php award pages
$l['optawards_page_title'] = 'Awards';
$l['optawards_page_list_award'] = 'Award';
$l['optawards_page_list_name'] = 'Name';
$l['optawards_page_list_description'] = 'Description';
$l['optawards_page_list_empty'] = 'no awards found in the database';
$l['optawards_page_list_recipients'] = '# Recipients';
$l['optawards_page_list_show_recipients'] = 'Show Recipients';
$l['optawards_page_list_actions'] = 'Actions';
$l['optawards_page_list_action_request'] = 'request Award';
$l['optawards_page_list_action_recommend'] = 'recommend Award';
$l['optawards_page_list_action_grant'] = '<span class="awardaction_grant">grant Award</span>';
$l['optawards_page_list_action_none'] = 'n/a';

$l['optawards_username'] = 'Recipient';
$l['optawards_reason'] = 'Reason';
$l['optawards_page_view_date'] = 'Date Granted';
$l['optawards_page_view_empty'] = 'no recipients found';
$l['optawards_username_recipient'] = 'Insert the username of the user to give/revoke the selected award.';
$l['optawards_reason_description'] = 'Enter the reason why the award should be given, you can use MyBB Code:';
$l['optawards_award_data'] = 'Award Request';
$l['optawards_award_request_added'] = 'your award request is added';
$l['optawards_requests_empty'] = 'no award requests found';
$l['optawards_requests_pending'] = 'Pending Award Requests';

$l['optawards_show_awards'] = 'Show Awards';
$l['optawards_view_award'] = 'View Award';
$l['optawards_request_award'] = 'Request Award';
$l['optawards_recommend_award'] = 'Recommend Award';
$l['optawards_grant_award'] = 'Grant Award';
$l['optawards_deny_award'] = 'Deny Award';
$l['optawards_deny_reason_description'] = 'Enter the reason why the award is denied, you can use MyBB Code:';
$l['optawards_process_award_requests'] = 'Process Award Requests';
$l['optawards_unknown_award_processing_action'] = 'unknown award processing action: ';
$l['optawards_award_processing'] = 'Award Processing';
$l['optawards_award_processed'] = 'The Award request has been processed';
$l['optawards_award_request_denied'] = 'Your Award request has been denied';

$l['optawards_error'] = 'Error';
$l['optawards_username_empty'] = 'Username is empty!';
$l['optawards_username_not_found'] = 'Username not found: ';

$l['optawards_pending_award_requests'] = 'There are {awardrequests} Award Requests pending, please process them!';

// display @ postbit, profile etc
$l['optawards_postbit_label'] = 'Latest Awards';
$l['optawards_profile_title'] = '{username}\' awards.';
$l['optawards_profile_viewall'] = '[View all]';
$l['optawards_profile_empty'] = 'This user has no awards at this time.';

// ACP settings
$l['optawards_pmuser_description'] = 'Choose if use the granting user as the PM author.';
$l['optawards_pmuserid_description'] = 'Choose the PM author. -1 = MyBB Engine. (Only works if above is set to [NO])';
$l['optawards_pmicon_description'] = 'Choose PM icon. -1 = no icon';
$l['optawards_pm_default_description'] = 'you can use BB code and those placeholders:
<p>
Names:<ul>
<li>{recipient} - name of the award receipient</li>
<li>{requestor} - name of who requested the award</li>
<li>{processor} - who processed/granted the award</li>
</ul>
Award Data:<ul>
<li>{award} - name of the award</li>
<li>{description} - description of the awards</li>
<li>{reason} - why was the award requested?</li>
<li>{icon} - icon of the award (large version)</li>
<li>{date} - when was the award requested?</li>
<li>{awardinfo} - link to the awards information page</li>
</ul>';
$l['optawards_granters_description'] = 'A comma separated list of <em>groups</em> which may grant awards independant of the awards usergroups.<br>
By default all group leaders can manage awards which are configured for their group.<br>
This setting is required for groups without leaders or awards which are available for "all".';
$l['optawards_pm_deny_description'] = 'you can use BB code and those placeholders:
<p>
Names:<ul>
<li>{recipient} - name of the award receipient</li>
<li>{requestor} - name of who requested the award</li>
<li>{processor} - who processed/granted the award</li>
</ul>
Award Data:<ul>
<li>{award} - name of the award</li>
<li>{description} - description of the awards</li>
<li>{reason} - why was the award requested?</li>
<li>{denyreason} - why was the award request denied?</li>
<li>{icon} - icon of the award (large version)</li>
<li>{date} - when was the award requested?</li>
<li>{awardinfo} - link to the awards information page</li>
</ul>';
$l['optawards_profile_description'] = 'Enter a maximum number of awards to be shown at profile. 0 = none, -1 = unlimited.';
$l['optawards_postbit_description'] = 'Enter a maximum number of awards to be shown at posts. 0 = none, -1 = unlimited.';

// ACP setting defaults
$l['optawards_pmuser_defaults'] = '1';
$l['optawards_pmuserid_defaults'] = '-1';
$l['optawards_pmicon_defaults'] = '1';
$l['optawards_pm_default_defaults'] = "Step forward {recipient}!

Hereby your are granted the Award ''[url={awardinfo}]{award}[/url]''[font=Courier][size=x-small][1][/size][/font] because of the following achivements:
[i]{reason}

{date}, {requestor}[/i]

Be proud of it!
[img]{icon}[/img]

Processed by: {processor}

Requested by: {requestor}

The Forum Team


[1]: {description}";
$l['optawards_granters_defaults'] = '4';
$l['optawards_pm_deny_defaults'] = "Hello {requestor}!

The Award ''[url={awardinfo}]{award}[/url]'' for {recipient} requested because of
[i]{reason}[/i]
has been denied.

The reason for denying the Award:
[i]{denyreason}[/i]


Best regards,

{processor}";
$l['optawards_profile_defaults'] = '-1';
$l['optawards_postbit_defaults'] = '-1';

$l['optawards_can_manage_awards'] = 'Can manage the awards?';

// english

?>
