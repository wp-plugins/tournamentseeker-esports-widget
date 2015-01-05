<?php
/*
Plugin Name: Tournament Seeker eSports Widget
Plugin URI: http://www.tournamentseeker.com/api
Description: Display details about your favorite upcoming eSports events, straight from TournamentSeeker.com! 
Author: tournamentseeker
Author URI: http://www.tournamentseeker.com
Version: 1.0.1
License: GPL-2	

*/
// Creating the widget

require_once 'TS_eSports_API.class.php';

class tournament_seeker_esports_event_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
        // Base ID of your widget
        'tournament_seeker_esports_event_widget',
        // Widget name will appear in UI
        __('TS eSports Events', 'tournament_seeker_esports_event_domain'),
        // Widget description
        array('description' => __('Displays the best video game events from TournamentSeeker.com!', 'tournament_seeker_esports_event_domain'),));
    }
    // Creating widget front-end
    // This is where the action happens
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)) echo $args['before_title'] . $title . $args['after_title'];
        // This is where you run the code and display the output
        $this->display_events($args, $instance);
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance)
    {
        $title = (isset($instance['title'])) ? $instance['title'] : __('New title', 'tournament_seeker_esports_event_domain');
        $api_key = (isset($instance['api_key'])) ? $instance['api_key'] : __('', 'tournament_seeker_esports_event_domain');
        $secret_key = (isset($instance['secret_key'])) ? $instance['secret_key'] : __('', 'tournament_seeker_esports_event_domain');
        $max_events = (isset($instance['max_events'])) ? $instance['max_events'] : 3;
        $list_type = (isset($instance['list_type'])) ? $instance['list_type'] : "my_events";
        $search_term = (isset($instance['search_term'])) ? $instance['search_term'] : "";


        // Widget admin form

?>
<h4>General</h4>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>
<h4>API</h4>
<p>
<label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e('API Key:'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>" />
<label for="<?php echo $this->get_field_id('secret_key'); ?>"><?php _e('Secret Key:'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('secret_key'); ?>" name="<?php echo $this->get_field_name('secret_key'); ?>" type="text" value="<?php echo esc_attr($secret_key); ?>" />
</p>
<h4>Event Config</h4>
<p>
<label for="<?php echo $this->get_field_id('max_events'); ?>"><?php _e('Max events:'); ?></label>
<select id="<?php echo $this->get_field_id('max_events'); ?>" name="<?php echo $this->get_field_name('max_events'); ?>">
<?php for($i = 1; $i <= 5; $i++ ) : ?>
	<option <?= ($max_events == $i ? 'selected="selected"' : ""); ?>><?=$i?></option>
<?php endfor; ?>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('list_type'); ?>"><?php _e('Events to List:'); ?></label>
<select id="<?php echo $this->get_field_id('list_type'); ?>" name="<?php echo $this->get_field_name('list_type'); ?>">
	<?php $val="my_events";?><option <?= ($list_type == $val ? 'selected="selected"' : ""); ?> value="<?=$val?>">My Events</option>
	<?php $val="my_favs";?><option <?= ($list_type == $val ? 'selected="selected"' : ""); ?> value="<?=$val?>">My Favorites</option>
	<?php $val="search";?><option <?= ($list_type == $val ? 'selected="selected"' : ""); ?> value="<?=$val?>">Custom Search</option>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('search_term'); ?>"><?php _e('Search Term:'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('search_term'); ?>" name="<?php echo $this->get_field_name('search_term'); ?>" type="text" value="<?php echo esc_attr($search_term); ?>" />
</p>
<?php
    }
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['api_key'] = (!empty($new_instance['api_key'])) ? strip_tags($new_instance['api_key']) : '';
        $instance['secret_key'] = (!empty($new_instance['secret_key'])) ? strip_tags($new_instance['secret_key']) : '';
        $instance['max_events'] = (!empty($new_instance['max_events'])) ? strip_tags($new_instance['max_events']) : 3;
        $instance['list_type'] = (!empty($new_instance['list_type'])) ? strip_tags($new_instance['list_type']) : 'my_events';
        $instance['search_term'] = (!empty($new_instance['search_term'])) ? strip_tags($new_instance['search_term']) : '';
        return $instance;
    }

    private function css() {
?>

<style type="text/css">
.ts-esports-event-container {
	-webkit-border-radius: 20px;
	-moz-border-radius: 20px;
	border-radius: 20px;
	border: 2px #3fb0da solid;
	padding: 10px;

	background: rgba(255,255,255,1);
	background: -moz-linear-gradient(top, rgba(255,255,255,1) 0%, rgba(250,250,250,1) 60%, rgba(245,245,245,1) 100%);
	background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(255,255,255,1)), color-stop(60%, rgba(250,250,250,1)), color-stop(100%, rgba(245,245,245,1)));
	background: -webkit-linear-gradient(top, rgba(255,255,255,1) 0%, rgba(250,250,250,1) 60%, rgba(245,245,245,1) 100%);
	background: -o-linear-gradient(top, rgba(255,255,255,1) 0%, rgba(250,250,250,1) 60%, rgba(245,245,245,1) 100%);
	background: -ms-linear-gradient(top, rgba(255,255,255,1) 0%, rgba(250,250,250,1) 60%, rgba(245,245,245,1) 100%);
	background: linear-gradient(to bottom, rgba(255,255,255,1) 0%, rgba(250,250,250,1) 60%, rgba(245,245,245,1) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed', GradientType=0 );

}

.ts-esports-event-container .header-logo {
	width: 60%;
	margin: 0 auto;
}

.ts-esports-event-container .header-container {
	text-align: center;
}

.ts-esports-event-container h4 {
	color: #3fb0da;
	font-weight: bold;
	text-align: center;
	margin: 5px 0 0 0;
}
.ts-esports-event-container h6 {
	color: #3fb0da;
	font-weight: bold;
	text-align: center;
	margin: 0 0 5px 0;
}

.ts-esports-event {
	padding: 5px 0;
	border-bottom: 1px #eee solid;
}
.ts-esports-event:last-child {
	border-bottom: none;
}

.ts-esports-event .event-logo img {
	-webkit-border-radius: 10px;
	-moz-border-radius:10px;
	border-radius: 10px;
	border: 2px #fff solid;
}
.ts-esports-event .event-logo {
	max-width: 20%;
	display: inline-block;
}
.ts-esports-event .event-logo img.free {
	border-color: #3fb0da;
}
.ts-esports-event .event-logo img.featured {
	border-color: #22bb22;
}
.ts-esports-event .event-logo img.charity {
	border-color: #98006a;
}
.ts-esports-event .event-logo img.premium {
	border-color: #fdce01;
}

.ts-esports-event .event-title {
	display: inline-block;
	width: 70%;
	padding-left:5%;
	font-weight: bold;
	text-align: center;
	vertical-align: top;
}

.ts-esports-event a {
	color:#3fb0da !important;
	text-overflow: ellipsis !important;
}

.ts-esports-event-date {
	display: inline-block;
	padding-top: 10px;
	font-weight: normal;

}

.esports-event-details {
	list-style-type: none;
	margin: 10px 10px 10px 5px !important;
	padding: 0px;

}

.esports-event-details li {

}

.esports-event-details li .header {
	display: inline-block;
	width: 70px;
	font-weight: bold;
	color: #3fb0da;
}

.esports-event-details li span{
	text-overflow: ellipsis;
}

.ts-esports-events-none {
	text-align: center;
	font-weight: bold;
	color: #3fb0da;
}


</style>

<?php
	}

    private function display_events($args, $instance)
    {
    	$apiKey = $instance['api_key'];
    	$secret_key = $instance['secret_key'];

		$listTitle = "";
		$tsapi = new TS_eSports_API($apiKey, $secret_key);

		if($instance['list_type'] == "search")
		{
			$eventList = TS_eSports_Event::SearchEventsBySearchTerm($tsapi, $instance['search_term']);
			$listTitle = "Upcoming Events";
		} else if($instance['list_type'] == "my_favs")
		{
			$eventList = TS_eSports_Event::GetMyFavorites($tsapi);
			$listTitle = "My Favorites";
		} else
		{
			$eventList = TS_eSports_Event::GetMyEvents($tsapi);
			$listTitle = "My Events";
		}

		$this->css();
	?>

	<div class="ts-esports-event-container">
		<div class="header-container"><a href="htt://www.tournamentseeker.com/" target="_blank"><img src="<?= (plugin_dir_url( __FILE__ ) . "tslogo.png") ?>" class="header-logo"></a></div>
		<h6><?= $listTitle?></h6>
	<?php $idx = 0;
		if(is_array($eventList)) :
			shuffle($eventList);
		 foreach($eventList as $event) :
		 	$fl = $event->featureLevel;
		 	$featureLevel = ($fl == 3) ? "premium" : ($fl == 2) ? "charity" : ($fl == 1) ? "featured" : "free";

		 	?>
		<div class="ts-esports-event">
			<div class="event-logo">
				<a href="http://www.tournamentseeker.com/events/<?=$event->eventID?>/" target="_blank"><img class="<?=$featureLevel?>" src="<?=$event->eventLogoThumb?>"></a>
			</div>
			<div class="event-title">
				<a href="http://www.tournamentseeker.com/events/<?=$event->eventID?>/" target="_blank"><span><?=$event->eventName;?></span></a><br>
				<span class="ts-esports-event-date"><?=date('M jS, Y', strtotime($event->eventStartTime))?> &#149; <?=date('g:ia', strtotime($event->eventStartTime))?></span>
			</div>
			<ul class="esports-event-details">
				<!-- <li><span class="header">Date: </span></li> -->
				<?php if($event->isOnlineEvent) : ?>
					<li><span class="header">Location: </span><span>Online</span></li>
				<?php else : ?>
					<li><span class="header">Location: </span><span><?=$event->venueName?></span></li>
					<li><span class="header">Address: </span><span><?=$event->addressStreet?></span></li>
					<li><span class="header"> </span><span><?=$event->addressCity?>, <?=$event->addressState_Abbr;?> <?=$event->addressZip?></span></li>
				<?php endif; ?>

				<?php if(strlen($event->webAddress) > 0) : ?>
					<li><span class="header">Web: </span><a href="<?=$event->webAddress?>" target="_blank"><?=$event->webAddress?></a></li>
				<?php endif; ?>
				<?php if(strlen($event->facebookLink) > 0) : ?>
					<li><span class="header">Facebook: </span><a href="<?=$event->facebookLink?>" target="_blank">Facebook Page</a></li>
				<?php endif; ?>
				<?php if(strlen($event->twitterHash) > 0) : ?>
					<li><span class="header">Twitter: </span><a href="https://twitter.com/search?q=<?=$event->twitterHash?>" target="_blank"><?=$event->twitterHash?></a></li>
				<?php endif; ?>
				<?php if(strlen($event->streamLink) > 0) : ?>
					<li><span class="header">Stream: </span><a href="<?=$event->streamLink?>" target="_blank"><?=$event->streamLink?></a></li>
				<?php endif; ?>
				<?php if(strlen($event->regAddress) > 0) : ?>
					<li><span class="header">Register: </span><a href="<?=$event->regAddress?>" target="_blank">Registration Page</a></li>
				<?php endif; ?>


				<?php $firstEvent = true; ?>
				<?php foreach($event->divisions as $division) : ?>
					<li><span class="header"><?= ($firstEvent ? "Divisions: " : "")?></span><span><img src="<?=$division->game_PlatformIcon?>"> <?= ($division->game_TeamSize > 0 ? ($division->game_TeamSize . "v" . $division->game_TeamSize) : "FFA")?> - <?=$division->game_Name?></span></li>
					<?php $firstEvent = false; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php if(++$idx >= $instance['max_events']) break; ?>
	<?php endforeach; ?>
	<?php else : ?>
		<div class="ts-esports-events-none"><span>No events found. </span></div>
	<?php endif; ?>
	</div>

	<?php
    }

} // Class tournament_seeker_esports_event_widget ends here

// Register and load the widget
function tournament_seeker_esports_event_load_widget()
{
    register_widget('tournament_seeker_esports_event_widget');
}
add_action('widgets_init', 'tournament_seeker_esports_event_load_widget');
?>