<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['admin_list'] = array(
    "Home"=>array("url"=>base_url("cms/home"),"class"=>"", "icon"=>"fa-home", "dashboard_icon"=>"ion-ios-home", "bg_color"=>"bg-aqua"),
    "Admin"=>array("url"=>base_url("cms/admin"),"class"=>"admin", "icon"=>"fa-users", "dashboard_icon"=>"ion-ios-people", "bg_color"=>"bg-light-blue"),
    "Member"=>array("url"=>base_url("cms/member"),"class"=>"member", "icon"=>"fa-users", "dashboard_icon"=>"ion-ios-people", "bg_color"=>"bg-blue-active"),
    "News"=>array("url"=>base_url("cms/news"),"class"=>"news", "icon"=>"fa-file", "dashboard_icon"=>"ion-ios-paper", "bg_color"=>"bg-green"),
    "Video"=>array("url"=>base_url("cms/video"),"class"=>"video", "icon"=>"fa-video", "dashboard_icon"=>"ion-ios-videocam", "bg_color"=>"bg-teal"),
    "Photo"=>array("url"=>base_url("cms/picture"),"class"=>"picture", "icon"=>"fa-images", "dashboard_icon"=>"ion-image", "bg_color"=>"bg-olive"),
    "Event"=>array("url"=>base_url("cms/event"),"class"=>"event", "icon"=>"fa-calendar-alt", "dashboard_icon"=>"ion-android-calendar", "bg_color"=>"bg-maroon",
				   "sub"=>array(
				   		"Event"=>array("url"=>base_url("cms/event"),"class"=>"event_content", "icon"=>"fa-users",),
				   		"Event Type"=>array("url"=>base_url("cms/event_type"),"class"=>"event_type", "icon"=>"fa-users",),
				   )),
    "Training"=>array("url"=>base_url("cms/training"),"class"=>"training", "icon"=>"fa-wrench", "dashboard_icon"=>"ion-wrench", "bg_color"=>"bg-orange-active"),
    "Life Book"=>array("url"=>base_url("cms/life"),"class"=>"life", "icon"=>"fa-book", "dashboard_icon"=>"ion-ios-book", "bg_color"=>"bg-aqua-active"),
    "About Us"=>array("url"=>base_url("cms/about"),"class"=>"about", "icon"=>"fa-info-circle", "dashboard_icon"=>"ion-information-circled", "bg_color"=>"bg-fuchsia-active"),
    "Email System"=>array("url"=>base_url("cms/email"),"class"=>"email", "icon"=>"fa-envelope", "dashboard_icon"=>"ion-email", "bg_color"=>"bg-light-blue-active"),
);
$config["site_name"] = "Lifetube CMS";