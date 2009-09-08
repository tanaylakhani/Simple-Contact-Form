<?php

/*
Plugin Name: Simple contact form
Description: Providing a simple contact form on your wordpress site at side bar.
Author: Gopi.R
Version: 2.0
Plugin URI: http://gopi.coolpage.biz/demo/2009/08/16/simple-contact-form/
Author URI: http://gopi.coolpage.biz/demo/2009/08/16/simple-contact-form/
Donate link: http://gopi.coolpage.biz/demo/2009/08/16/simple-contact-form/
*/

function gCF()
{
	
	?>
    <link rel='stylesheet' href='<?php echo get_option('siteurl'); ?>/wp-content/plugins/simple-contact-form/style.css' type='text/css' />
    <form action="#" name="gcf" id="gcf">
      <div style='padding-top:4px;'> <span id="gcf_alertmessage"></span> </div>
      <div style='padding-top:4px;'> Your name </div>
      <div>
        <input name="gcf_name" class="gcftextbox" type="text" id="gcf_name" maxlength="120">
      </div>
      <div style='padding-top:4px;'> Your email </div>
      <div>
        <input name="gcf_email" class="gcftextbox" type="text" id="gcf_email" maxlength="120">
      </div>
      <div style='padding-top:4px;'> Enter your message </div>
      <div>
        <textarea name="gcf_message" class="gcftextarea" rows="3" id="gcf_message"></textarea>
      </div>
      <div style="padding-top:4px;" >
        <input type="button" name="button" value="Submit" onclick="javascript:gcf_submit(this.parentNode,'<?php echo get_option('siteurl'); ?>/wp-content/plugins/simple-contact-form/');">
      </div>
    </form>
    <script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/simple-contact-form/simple-contact-form.js"></script>
<?php
}

function gCF_install() 
{
	global $wpdb, $wp_version;
	$gCF_table = $wpdb->prefix . "gCF";
	add_option('gCF_table', $gCF_table);
	
	if($wpdb->get_var("show tables like '". $gCF_table . "'") != $gCF_table) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". $gCF_table . "` (
			  `gCF_id` int(11) NOT NULL auto_increment,
			  `gCF_name` varchar(120) NOT NULL,
			  `gCF_email` varchar(120) NOT NULL,
			  `gCF_message` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			  `gCF_ip` varchar(50) NOT NULL,
			  `gCF_date` datetime NOT NULL default '0000-00-00 00:00:00',
			  PRIMARY KEY  (`gCF_id`) )
			");
	}
	
	add_option('gCF_title', "Contact Us");
	add_option('gCF_fromemail', "admin@contactform.com");
	add_option('gCF_On_Homepage', "YES");
	add_option('gCF_On_Posts', "YES");
	add_option('gCF_On_Pages', "YES");
	add_option('gCF_On_Archives', "NO");
	add_option('gCF_On_Search', "NO");
}

function gCF_widget($args) 
{
	if(is_home() && get_option('gCF_On_Homepage') == 'YES') {	$display = "show";	}
	if(is_single() && get_option('gCF_On_Posts') == 'YES') {	$display = "show";	}
	if(is_page() && get_option('gCF_On_Pages') == 'YES') {	$display = "show";	}
	if(is_archive() && get_option('gCF_On_Archives') == 'YES') {	$display = "show";	}
	if(is_search() && get_option('gCF_On_Search') == 'YES') {	$display = "show";	}
	
	if($display == "show")
	{
		extract($args);
		echo $before_widget . $before_title;
		echo get_option('gCF_title');
		echo $after_title;
		gCF();
		echo $after_widget;
	}
}
	
function gCF_control() 
{
	
	echo '<p>Simple contact form.<br> To change the setting goto <br>SETTING TAB --> Simplae contact form --> Setting.';
	echo '<br><a href="options-general.php?page=simple-contact-form/setting.php">';
	echo 'click here</a></p>';
	
}

function gCF_widget_init()
{
  	register_sidebar_widget(__('Simple contact form'), 'gCF_widget');   
	
	if(function_exists('register_sidebar_widget')) 
	{
		register_sidebar_widget('Simple contact form', 'gCF_widget');
	}
	
	if(function_exists('register_widget_control')) 
	{
		register_widget_control(array('Simple contact form', 'widgets'), 'gCF_control', 400, 400);
	} 
}

function gCF_deactivation() 
{
	delete_option('gCF_title');
	delete_option('gCF_On_Homepage');
	delete_option('gCF_On_Posts');
	delete_option('gCF_On_Pages');
	delete_option('gCF_On_Archives');
	delete_option('gCF_On_Search');
}

function gCF_admin()
{

?>
<div class="wrap">
  <div class="tool-box">
    <?php
	$title = __('Simple contact form');
	global $wpdb;
	$gcf_table = get_option('gCF_table');
	
	if(@$_GET["AC"]=="DEL" && @$_GET["DID"] > 0) 
	{ 
	
		$wpdb->get_results("delete from $gcf_table where gCF_id=".@$_GET["DID"]);
	}
	
	$data = $wpdb->get_results("select * from $gcf_table order by gCF_id desc");
	if ( empty($data) ) 
	{ 
		echo "<div id='message' class='error'><p>No data available.</p></div>";
		return;
	}
	
	?>
    <h2><?php echo wp_specialchars( $title ); ?></h2>
    <script language="javascript" type="text/javascript">
	function _dealdelete(id)
	{
		if(confirm("Do you want to delete this record?"))
		{
			document.frm.action="options-general.php?page=simple-contact-form/simple-contact-form.php&AC=DEL&DID="+id;
			document.frm.submit();
		}
	}	
	</script>
    <form name="frm" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th width="12%" align="left">Name</th>
            <th width="16%" align="left">Email</th>
            <th width="34%" align="left">Message</th>
            <th width="21%" align="left">Date</th>
            <th width="15%" align="left">IP</th>
            <th width="2%" align="left"></th>
          </tr>
        <thead>
        <tbody>
          <?php 
    	$i = 0;
    	foreach ( $data as $data ) { 
		$_date = mysql2date(get_option('date_format'), $data->gCF_date);
    	?>
          <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
            <td align="left"><?php echo(stripslashes($data->gCF_name)); ?></td>
            <td align="left"><?php echo(stripslashes($data->gCF_email)); ?></td>
            <td align="left"><?php echo(stripslashes($data->gCF_message)); ?></td>
            <td align="left"><?php echo($_date); ?></td>
            <td align="left"><?php echo(stripslashes($data->gCF_ip)); ?></td>
            <td align="left"><a title="Delete" onClick="javascript:_dealdelete('<?php echo($data->gCF_id); ?>')" href="javascript:void(0);">X</a> </td>
          </tr>
          <?php $i = $i+1; } ?>
        </tbody>
      </table>
    </form>
    <div align="left" style="padding-top:10px;padding-bottom:5px;"> <a href="options-general.php?page=simple-contact-form/simple-contact-form.php">Manage Page</a> <a href="options-general.php?page=simple-contact-form/setting.php">Setting Page</a> </div>
  </div>
</div>
<?php
}


function gCF_add_to_menu() 
{
	add_options_page('Software', 'Simple contact form', 7, __FILE__, 'gCF_admin' );
	add_options_page('Software', '', 0, "simple-contact-form/setting.php",'' );
}

add_action("plugins_loaded", "gCF_widget_init");
add_action('admin_menu', 'gCF_add_to_menu');
register_activation_hook(__FILE__, 'gCF_install');
register_deactivation_hook(__FILE__, 'gCF_deactivation');
add_action('init', 'gCF_widget_init');
?>
