<?php

#calendar
/*

ideally, this should be structured more like so:

$sys_nav['calendar'] = array(

    array(  'link' => 'calendar_gxlist.php',
            'name' => 'View/Edit' ),
    array(  'link' => 'calendar_gxlist.php?old=1',
            'name' => 'View/Edit Old Events' ),

);
*/


#calendar
$sys_nav[1][0]['link'] ='calendar_gxlist.php';
$sys_nav[1][0]['name'] ='View/Edit';
$sys_nav[1][0]['class'] ='view';
$sys_nav[1][0]['nav'] ='Calendar';
$sys_nav[1][0]['per'] ='11';

$sys_nav[1][1]['link'] ='calendar_gxlist.php?old=1';
$sys_nav[1][1]['name'] ='View/Edit Old Events';
$sys_nav[1][1]['class'] ='view';
$sys_nav[1][1]['per'] ='11';

$sys_nav[1][2]['link'] ='calendar_gxedit.php';
$sys_nav[1][2]['name'] ='Add Event';
$sys_nav[1][2]['class'] ='add';
$sys_nav[1][2]['per'] ='11';

$sys_nav[1][3]['link'] ='calendar_type.php?action=list';
$sys_nav[1][3]['name'] ='Event Types';
$sys_nav[1][3]['per'] ='11';


#faq
$sys_nav[4][0]['link'] ='faq.php?action=list';
$sys_nav[4][0]['name'] ='View/Edit';
$sys_nav[4][0]['nav'] ='FAQs';
$sys_nav[4][0]['class'] ='view';
$sys_nav[4][0]['per'] ='22';

$sys_nav[4][1]['link'] ='faq.php';
$sys_nav[4][1]['name'] ='Add';
$sys_nav[4][1]['class'] ='add';
$sys_nav[4][1]['per'] ='22';

$sys_nav[4][2]['link'] ='faq_type.php?action=list';
$sys_nav[4][2]['name'] ='FAQ Types';
$sys_nav[4][2]['per'] ='22';




#petition
$sys_nav[7][0]['name'] ='View/Edit';
$sys_nav[7][0]['link'] ='petition.php?action=list';
$sys_nav[7][0]['class'] ='view';
$sys_nav[7][0]['nav'] ='Petitions';
$sys_nav[7][0]['per'] ='74';

$sys_nav[7][1]['name'] ='Add';
$sys_nav[7][1]['link'] ='petition.php';
$sys_nav[7][1]['class'] ='add';

#photo gallery
$sys_nav[8][0]['link'] ='gallery.php';
$sys_nav[8][0]['name'] ='Add';
$sys_nav[8][0]['class'] ='add';
$sys_nav[8][0]['per'] ='34';

$sys_nav[8][1]['link'] ='gallery_list.php';
$sys_nav[8][1]['name'] ='View/Edit';
$sys_nav[8][1]['nav'] ='Photo Gallery';
$sys_nav[8][1]['per'] ='34';

$sys_nav[8][1]['class'] ='view';
$sys_nav[8][2]['link'] ='gallery_type.php?action=list';
$sys_nav[8][2]['name'] ='Gallery Types';
$sys_nav[8][2]['per'] ='34';

#email 
$sys_nav[9][0]['link'] ='../lists/admin';
$sys_nav[9][0]['name'] ='PHP List Admin';
$sys_nav[9][0]['class'] ='email';
$sys_nav[9][]['per'] ='38';

$sys_nav[9][1]['link'] ='email_lists.php';
$sys_nav[9][1]['name'] ='AMP Email Blast';
$sys_nav[9][1]['class'] ='email';
$sys_nav[9][1]['per'] ='38';

$sys_nav[9][2]['link'] ='email_listsedit.php';
$sys_nav[9][2]['name'] ='Add Email Lists';
$sys_nav[9][2]['class'] ='add';
$sys_nav[9][2]['per'] ='38';


#links
$sys_nav[11][0]['link'] ='links.php?action=list';
$sys_nav[11][0]['name'] ='View/Edit';
$sys_nav[11][0]['class'] ='view';
$sys_nav[11][0]['nav'] ='Links';
$sys_nav[11][0]['per'] ='26';

$sys_nav[11][1]['link'] ='links.php';
$sys_nav[11][1]['name'] ='Add';
$sys_nav[11][1]['class'] ='add';
$sys_nav[11][1]['per'] ='26';

$sys_nav[11][2]['link'] ='link_type.php?action=list';
$sys_nav[11][2]['name'] ='Link Types';
$sys_nav[11][2]['per'] ='26';

#content
$sys_nav['content']['title'] ='Content';
$sys_nav['content'][0]['link'] ='articlelist.php';
$sys_nav['content'][0]['name'] ='View/Edit Content';
$sys_nav['content'][0]['class'] ='view';
$sys_nav['content'][0]['per'] ='1';

$sys_nav['content'][1]['link'] ='article_edit.php';
$sys_nav['content'][1]['name'] ='Add Content';
$sys_nav['content'][1]['class'] ='add';
$sys_nav['content'][1]['per'] ='2';

$sys_nav['content'][2]['link'] ='article_list.php?&class=2';
$sys_nav['content'][2]['name'] ='View/Edit Homepage Content';
$sys_nav['content'][2]['class'] ='home';
$sys_nav['content'][2]['per'] ='1';

$sys_nav['content'][3]['link'] ='article_fpedit.php';
$sys_nav['content'][3]['name'] ='Add Homepage Content';
$sys_nav['content'][3]['class'] ='add';
$sys_nav['content'][3]['per'] ='2';

$sys_nav['content'][4]['title'] ='Docs and Images';
$sys_nav['content'][4]['per'] ='85';

$sys_nav['content'][5]['link'] ='docdir.php';
$sys_nav['content'][5]['name'] ='View Documents';
$sys_nav['content'][5]['class'] ='doc';
$sys_nav['content'][5]['per'] ='85';

$sys_nav['content'][6]['link'] ='imgdir.php';
$sys_nav['content'][6]['name'] ='View Images';
$sys_nav['content'][6]['class'] ='img';
$sys_nav['content'][6]['per'] ='85';

$sys_nav['content'][7]['link'] ='doc_upload.php';
$sys_nav['content'][7]['name'] ='Upload Document';
$sys_nav['content'][7]['class'] ='upload';
$sys_nav['content'][7]['per'] ='85';

$sys_nav['content'][8]['link'] ='imgup.php';
$sys_nav['content'][8]['name'] ='Upload Images';
$sys_nav['content'][8]['class'] ='upload';
$sys_nav['content'][8]['per'] ='85';

$sys_nav['content'][9]['title'] ='Sections';

$sys_nav['content'][10]['link'] ='edittypes.php';
$sys_nav['content'][10]['name'] ='View/Edit Sections';
$sys_nav['content'][10]['class'] ='view';
$sys_nav['content'][10]['per'] ='9';

$sys_nav['content'][11]['link'] ='type_edit.php';
$sys_nav['content'][11]['name'] ='Add Section';
$sys_nav['content'][11]['class'] ='add';
$sys_nav['content'][11]['per'] ='4';

$sys_nav['content'][12]['link'] ='class.php';
$sys_nav['content'][12]['name'] ='Add Class';
$sys_nav['content'][12]['class'] ='add';
$sys_nav['content'][12]['per'] ='8';


#web actions
$sys_nav[21][0]['link'] ='sendfax_list.php';
$sys_nav[21][0]['name'] ='View/Edit';
$sys_nav[21][0]['class'] ='view';
$sys_nav[21][0]['nav'] ='Web Actions';
$sys_nav[21][0]['per'] ='31';

$sys_nav[21][1]['link'] ='sendfax_edit.php';
$sys_nav[21][1]['name'] ='Add';
$sys_nav[21][1]['class'] ='add';
$sys_nav[21][1]['per'] ='31';

# Comments
$sys_nav[23][0]['link'] ='comments.php?action=list';
$sys_nav[23][0]['name'] ='View/Edit';
$sys_nav[23][0]['class'] ='view';

# Vol
#$sys_nav[40][0]['link'] ='';
#$sys_nav[40][0]['name'] ='';
#$sys_nav[40][1]['link'] ='';
#$sys_nav[40][1]['name'] ='';
#$sys_nav[40][2]['link'] ='';
#$sys_nav[40][2]['name'] ='';
#$sys_nav[40][3]['link'] ='';
#$sys_nav[40][3]['name'] ='';
#$sys_nav[40][4]['link'] ='';
#$sys_nav[40][4]['name'] ='';
#$sys_nav[40][5]['link'] ='';
#$sys_nav[40][5]['name'] ='';
#$sys_nav[40][6]['link'] ='';
#$sys_nav[40][6]['name'] ='';
#$sys_nav[40][7]['link'] ='';
#$sys_nav[40][7]['name'] ='';
#$sys_nav[40][8]['link'] ='';
#$sys_nav[40][8]['name'] ='';

# Quotes
$sys_nav[41][0]['link'] ='quotes.php?action=list';
$sys_nav[41][0]['name'] ='View/Edit';
$sys_nav[41][0]['class'] ='view';
$sys_nav[41][0]['nav'] ='Quotes';
$sys_nav[41][0]['per'] ='99';

$sys_nav[41][1]['link'] ='quotes.php';
$sys_nav[41][1]['name'] ='Add';
$sys_nav[41][1]['class'] ='add';
$sys_nav[41][1]['per'] ='99';

# RSS
$sys_nav[45]['title'] ='RSS Syndication';

$sys_nav[45][0]['link'] ='rssfeed.php?action=list';
$sys_nav[45][0]['name'] ='View/Edit Feeds';
$sys_nav[45][0]['class'] ='view';
$sys_nav[45][0]['per'] ='102';

$sys_nav[45][1]['link'] ='rssfeed.php';
$sys_nav[45][1]['name'] ='Add Feed';
$sys_nav[45][1]['class'] ='add';
$sys_nav[45][1]['per'] ='102';

$sys_nav[45][2]['title'] ='RSS Aggregator';
$sys_nav[45][2]['per'] ='102';

$sys_nav[45][3]['link'] ='feeds_add.php';
$sys_nav[45][3]['name'] ='Add/Edit';
$sys_nav[45][3]['class'] ='add';
$sys_nav[45][]['per'] ='102';

$sys_nav[45][4]['link'] ='feeds_update.php';
$sys_nav[45][4]['name'] ='Update Feeds';
$sys_nav[45][4]['per'] ='102';

$sys_nav[45][5]['link'] ='feeds_view.php?action=list';
$sys_nav[45][5]['name'] ='View/Filter Feeds';
$sys_nav[45][5]['class'] ='view';
$sys_nav[45][5]['per'] ='102';

# navigation
$sys_nav[30]['title'] ='Navigation';
$sys_nav[30][0]['link'] ='nav_position.php?mod_id=1';
$sys_nav[30][0]['name'] ='Default Navigation Layout';
$sys_nav[30][0]['per'] ='47';

$sys_nav[30][1]['link'] ='nav_position.php?mod_id=2';
$sys_nav[30][1]['name'] ='Homepage Navigation Layout';
$sys_nav[30][1]['per'] ='47';

$sys_nav[30][2]['link'] ='nav.php?action=list';
$sys_nav[30][2]['name'] ='View/Edit Navigation Files';
$sys_nav[30][2]['class'] ='view';
$sys_nav[30][2]['per'] ='47';

$sys_nav[30][3]['link'] ='nav.php';
$sys_nav[30][3]['name'] ='Add Navigation Files';
$sys_nav[30][3]['class'] ='add';
$sys_nav[30][3]['per'] ='48';

#templates
$sys_nav[31]['title'] ='Template';
$sys_nav[31][0]['link'] ='template.php?action=list';
$sys_nav[31][0]['name'] ='View/Edit Template';
$sys_nav[31][0]['class'] ='view';
$sys_nav[31][0]['per'] ='49';

$sys_nav[31][1]['link'] ='template.php';
$sys_nav[31][1]['name'] ='Add Template';
$sys_nav[31][1]['class'] ='add';
$sys_nav[31][1]['per'] ='49';

$sys_nav[31][2]['link'] ='css_edit.php';
$sys_nav[31][2]['name'] ='Edit Standard CSS';
$sys_nav[31][2]['class'] ='view';
$sys_nav[31][2]['per'] ='49';

$sys_nav[31][3]['link'] ='css_list.php';
$sys_nav[31][3]['name'] ='View/Edit Custom CSS';
$sys_nav[31][3]['class'] ='view';
$sys_nav[31][2]['per'] ='49';


$sys_nav['module'][]['per'] ='';
#modules
$sys_nav['module']['title'] ='Modules';
$sys_nav['module'][0]['link'] ='module_header.php?action=list';
$sys_nav['module'][0]['name'] ='View Intro Text';
$sys_nav['module'][0]['class'] ='view';
$sys_nav['module'][0]['per'] ='45';

$sys_nav['module'][1]['link'] ='module_hedear.php';
$sys_nav['module'][1]['name'] ='Add Intro Text';
$sys_nav['module'][1]['class'] ='add';
$sys_nav['module'][1]['per'] ='46';

$sys_nav['module'][2]['link'] ='module.php';
$sys_nav['module'][2]['name'] ='Add Module';
$sys_nav['module'][2]['class'] ='add';
$sys_nav['module'][2]['per'] ='46';

$sys_nav['module'][3]['link'] ='module.php?action=list';
$sys_nav['module'][3]['name'] ='Edit Module Settings';
$sys_nav['module'][3]['class'] ='view';
$sys_nav['module'][3]['per'] ='45';

#forms
$sys_nav['udm']['title'] ='Forms';
$sys_nav['udm'][0]['link'] ='modinput4_list.php';
$sys_nav['udm'][0]['name'] ='View/Edit Forms';
$sys_nav['udm'][0]['class'] ='view';
$sys_nav['udm'][0]['per'] ='54';

$sys_nav['udm'][1]['link'] ='modinput4_new.php';
$sys_nav['udm'][1]['name'] ='Add Form';
$sys_nav['udm'][1]['class'] ='add';
$sys_nav['udm'][1]['per'] ='55';

$sys_nav['udm'][2]['link'] ='modinput4_search.php';
$sys_nav['udm'][2]['name'] ='Search Form Data';
$sys_nav['udm'][2]['class'] ='search';
$sys_nav['udm'][2]['per'] ='54';

#system
$sys_nav['system']['title'] ='System Setting';
$sys_nav['system'][1]['link'] ='per.php?action=list';
$sys_nav['system'][1]['name'] ='System Permissions';
$sys_nav['system'][1]['class'] ='user';
$sys_nav['system'][1]['per'] ='51';

$sys_nav['system'][0]['link'] ='user.php?action=list';
$sys_nav['system'][0]['name'] ='System Users';
$sys_nav['system'][0]['class'] ='user';
$sys_nav['system'][0]['per'] ='51';

$sys_nav['system'][2]['link'] ='sysvar.php';
$sys_nav['system'][2]['name'] ='System Settings';
$sys_nav['system'][2]['class'] ='settings';
$sys_nav['system'][2]['per'] ='52';

$sys_nav['system'][3]['link'] ='wizard_setup.php';
$sys_nav['system'][3]['name'] ='Setup Wizard';
$sys_nav['system'][3]['class'] ='settings';
$sys_nav['system'][5]['per'] ='52';

$sys_nav['system'][4]['link'] ='flushcache.php';
$sys_nav['system'][4]['name'] ='Flush Cache';
$sys_nav['system'][4]['per'] ='52';

$sys_nav['system'][5]['link'] ='logout.php';
$sys_nav['system'][5]['name'] ='Log Out';
$sys_nav['system'][5]['class'] ='exit';


#
#$sys_nav[][]['link'] ='';
#$sys_nav[][]['name'] ='';


#$nav_link .= '<div width= "100%"><fieldset   style="  border: 1px solid black;">';
#$nav_link .= 'test';


?>
