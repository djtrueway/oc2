<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$main_user_id = isset($params['user_id']) ? $params['user_id'] : '';
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : NULL;
$picture_width = isset($params['picture_width']) ? $params['picture_width'] : '90';
$picture_height = isset($params['picture_height']) ? $params['picture_height'] : '100';
$mailto = isset($params['mailto']) ? $params['mailto'] : 0;

/** getting user id from current property (used in property_show and property_listing) **/
if(!trim($main_user_id)) $main_user_id = $wpl_properties['current']['data']['user_id'];

$user_ids = array();
$user_ids[] = $main_user_id;

// Add additional agents to the agent contact information
if(wpl_global::check_addon('multi_agents'))
{
    _wpl_import('libraries.addon_multi_agents');

    $multi = new wpl_addon_multi_agents($wpl_properties['current']['data']['id']);
    $additional_agents = $multi->get_agents();

    foreach($additional_agents as $additional_agent) $user_ids[] = $additional_agent;
    $user_ids = array_unique($user_ids);
}

// Apply Filters
@extract(wpl_filters::apply('wpl_property_agent_user_ids', array('user_ids'=>$user_ids, 'property_id'=>$wpl_properties['current']['data']['id'], 'pdf'=>false)));

$pshow_fields = wpl_users::get_pshow_fields();

$users_data = array();
foreach($user_ids as $user_id)
{
    // User is not exists
	if(!wpl_users::is_wpl_user($user_id)) continue;
	
    $wpl_user = wpl_users::full_render($user_id, $pshow_fields, NULL, array(), true);
	
    // Resizing profile image
    $params                   = array();
    $params['image_parentid'] = $user_id;
    $params['image_name']     = isset($wpl_user['profile_picture']['name']) ? $wpl_user['profile_picture']['name'] : '';
    $profile_path             = isset($wpl_user['profile_picture']['path']) ? $wpl_user['profile_picture']['path'] : '';
    $profile_image            = wpl_images::create_profile_images($profile_path, $picture_width, $picture_height, $params);

    // Resizing company logo
    $params                   = array();
    $params['image_parentid'] = $user_id;
    $params['image_name']     = isset($wpl_user['company_logo']['name']) ? $wpl_user['company_logo']['name'] : '';
    $logo_path                = isset($wpl_user['company_logo']['path']) ? $wpl_user['company_logo']['path'] : '';
    $logo_image               = wpl_images::create_profile_images($logo_path, $picture_width, $picture_height, $params);

    $agent_name               = isset($wpl_user['materials']['first_name']['value']) ? $wpl_user['materials']['first_name']['value'] : '';
    $agent_l_name             = isset($wpl_user['materials']['last_name']['value']) ? $wpl_user['materials']['last_name']['value'] : '';
    $company_name             = isset($wpl_user['materials']['company_name']['value']) ? $wpl_user['materials']['company_name']['value'] : '';
    $profile_url              = wpl_users::get_profile_link($user_id);

	// Preparing website URL
	$website = '';
	if(isset($wpl_user['materials']['website']['value']))
	{
		$website = $wpl_user['materials']['website']['value'];
		if(stripos($website, 'http://') === false and stripos($website, 'https://') === false)
		{
			$website = 'http://'.$website;
		}
        
		$wpl_user['materials']['website']['value'] = $website;
	}

    $users_data[] = array('wpl_user'=>$wpl_user, 'profile_image'=>$profile_image, 'logo_image'=>$logo_image, 'agent_name'=>$agent_name, 'agent_l_name'=>$agent_l_name, 'company_name'=>$company_name, 'profile_url'=>$profile_url);
}

$is_multi_agent = (count($users_data) > 1 ? true : false);
?>
<div class="wpl_agent_info_activity" id="wpl_agent_info<?php echo $main_user_id; ?>">
	<?php foreach($users_data as $user_data): ?>
	<div <?php echo $this->itemscope.' '.$this->itemtype_RealEstateAgent; ?> class="<?php echo ($is_multi_agent) ? 'wpl_multi_agent_info' : 'wpl_single_agent_info'; ?> wpl_agent_info clearfix">
		<div class="wpl_agent_info_l">
			<div class="image_container">
				<div class="front <?php if($user_data['logo_image']) echo 'has_logo'; ?>">
					<?php if($user_data['profile_image']): ?>
						<img <?php echo $this->itemprop_image; ?> src="<?php echo $user_data['profile_image']; ?>" class="profile_image" alt="<?php echo $user_data['agent_name']. ' '.$user_data['agent_l_name']; ?>" />
					<?php else: ?>
						<div class="no_image"></div>
					<?php endif; ?>
				</div>
				<?php if($user_data['logo_image']): ?>
				<div class="back">
					<img <?php echo $this->itemprop_logo; ?> src="<?php echo $user_data['logo_image']; ?>" class="logo" alt="<?php echo $user_data['company_name']; ?>" />
				</div>
				<?php endif; ?>
			</div>
			<div class="company_details">
				<div <?php echo $this->itemprop_name; ?> class="company_name"><?php echo $user_data['company_name']; ?></div>
				<?php if(isset($user_data['wpl_user']['materials']['company_address'])): ?>
				<div class="company_address" <?php echo $this->itemprop_address.''.$this->itemscope.' '.$this->itemtype_PostalAddress;?>><span <?php echo $this->itemprop_addressLocality; ?>><?php echo $user_data['wpl_user']['materials']['company_address']['value']; ?></span></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="wpl_agent_info_r">
			<ul>
				<li class="name" <?php echo $this->itemprop_name; ?>><a href="<?php echo $user_data['profile_url']; ?>"><?php echo $user_data['agent_name'].' '.$user_data['agent_l_name']; ?></a></li>

				<?php if(isset($user_data['wpl_user']['materials']['website']['value'])): ?>
				<li class="website"><a <?php echo $this->itemprop_url; ?>  href="<?php echo $user_data['wpl_user']['materials']['website']['value']; ?>" target="_blank"><?php echo __('View website', 'real-estate-listing-realtyna-wpl') ?></a></li>
				<?php endif; ?>

				<?php if(isset($user_data['wpl_user']['materials']['tel']['value'])): ?>
				<li <?php echo $this->itemprop_telephone; ?> class="tel"><a href="tel:<?php echo $user_data['wpl_user']['materials']['tel']['value']; ?>"><?php echo $user_data['wpl_user']['materials']['tel']['value']; ?></a></li>
				<?php endif; ?>

				<?php if(isset($user_data['wpl_user']['materials']['mobile']['value'])): ?>
				<li <?php echo $this->itemprop_telephone; ?> class="mobile"><a href="tel:<?php echo $user_data['wpl_user']['materials']['mobile']['value']; ?>"><?php echo $user_data['wpl_user']['materials']['mobile']['value']; ?></a></li>
				<?php endif; ?>

				<?php if(isset($user_data['wpl_user']['materials']['fax']['value'])): ?>
				<li <?php echo $this->itemprop_faxNumber; ?> class="fax"><?php echo $user_data['wpl_user']['materials']['fax']['value']; ?></li>
				<?php endif; ?>

				<?php if(isset($user_data['wpl_user']['main_email_url']) and wpl_global::get_setting('profile_email_type') == '0'): ?>
				<li class="email">
					<?php if($mailto): ?>
					<a <?php echo $this->itemprop_email; ?> href="mailto:<?php echo $user_data['wpl_user']['materials']['main_email']['value']; ?>"><img src="<?php echo $user_data['wpl_user']['main_email_url']; ?>" alt="<?php echo $user_data['agent_name']. ' '.$user_data['agent_l_name']; ?>" /></a>
					<?php else: ?>
					<img src="<?php echo $user_data['wpl_user']['main_email_url']; ?>" alt="<?php echo $user_data['agent_name']. ' '.$user_data['agent_l_name']; ?>" />
					<?php endif; ?>
				</li>
				<?php endif; ?>
				<?php if(isset($user_data['wpl_user']['main_email_url']) and wpl_global::get_setting('profile_email_type') == '1'): ?>
					<li class="email">
						<?php if($mailto): ?>
							<a <?php echo $this->itemprop_email; ?> href="mailto:<?php echo $user_data['wpl_user']['materials']['main_email']['value']; ?>"><?php echo $user_data['wpl_user']['materials']['main_email']['value']; ?></a>
						<?php else: ?>
							<p><?php echo $user_data['wpl_user']['materials']['main_email']['value']; ?></p>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<?php if(isset($user_data['wpl_user']['second_email_url']) and wpl_global::get_setting('profile_email_type') == '0'): ?>
				<li class="second_email">
					<?php if($mailto): ?>
					<a <?php echo $this->itemprop_email; ?> href="mailto:<?php echo $user_data['wpl_user']['materials']['secondary_email']['value']; ?>"><img src="<?php echo $user_data['wpl_user']['second_email_url']; ?>" alt="<?php echo $user_data['agent_name']. ' '.$user_data['agent_l_name']; ?>" /></a>
					<?php else: ?>
					<img src="<?php echo $user_data['wpl_user']['second_email_url']; ?>" alt="<?php echo $user_data['agent_name']. ' '.$user_data['agent_l_name']; ?>" />
					<?php endif; ?>
				</li>
				<?php endif; ?>
				<?php if(isset($user_data['wpl_user']['second_email_url']) and wpl_global::get_setting('profile_email_type') == '1'): ?>
					<li class="second_email">
						<?php if($mailto): ?>
							<a <?php echo $this->itemprop_email; ?> href="mailto:<?php echo $user_data['wpl_user']['materials']['secondary_email']['value']; ?>"><?php echo $user_data['wpl_user']['materials']['secondary_email']['value']; ?></a>
						<?php else: ?>
							<p><?php echo $user_data['wpl_user']['materials']['secondary_email']['value']; ?></p>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<?php endforeach; ?>
</div>