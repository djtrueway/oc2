<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$description_column = 'about';
if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, 2)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);

foreach($this->wpl_profiles as $key=>$profile)
{
    if($key == 'current') continue;

    /** unset previous property **/
    unset($this->wpl_profiles['current']);

    /** set current property **/
    $this->wpl_profiles['current'] = $profile;

    $agent_name   = (isset($profile['materials']['first_name']['value']) ? $profile['materials']['first_name']['value'] : '') ;
    $agent_l_name = (isset($profile['materials']['last_name']['value']) ? $profile['materials']['last_name']['value'] : '');

    $description = stripslashes(strip_tags($profile['raw'][$description_column]));
    ?>
    <div class="wpl-column">
      <div <?php echo $this->itemscope.' '.$this->itemtype_RealEstateAgent; ?> class="wpl_profile_container <?php echo (isset($this->property_css_class) ? $this->property_css_class : ''); ?>" id="wpl_profile_container<?php echo $profile['data']['id']; ?>">
          <div class="wpl_profile_picture">
              <div class="front">
                  <?php
                      if(isset($profile['profile_picture']['url'])) echo '<img '.$this->itemprop_image.' src="'.$profile['profile_picture']['url'].'" alt="'.$agent_name.' '.$agent_l_name.'" />';
                      elseif(isset($profile['company_logo']['url'])) echo '<img '.$this->itemprop_image.' src="'.$profile['company_logo']['url'].'" alt="'.$agent_name.' '.$agent_l_name.'" />';
                      else echo '<div '.$this->itemprop_image.' class="no_image"></div>';
                  ?>
              </div>
              <div class="back">
                  <a <?php echo $this->itemprop_url; ?> href="<?php echo $profile['profile_link']; ?>" class="view_properties"><?php echo __('View properties', 'real-estate-listing-realtyna-wpl'); ?></a>
              </div>
          </div>

          <div class="wpl_profile_container_title">
              <?php
                  echo '<div class="title">
                          <a '.$this->itemprop_name.' href="'.$profile['profile_link'].'" >'.$agent_name.' '.$agent_l_name.'</a>
                          <a '.$this->itemprop_url.' href="'.$profile['profile_link'].'>" class="view_properties">'. __('View properties', 'real-estate-listing-realtyna-wpl').'</a>
                        </div>';

                  if(isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '0') echo '<a href="mailto:'.$profile['data']['main_email'].'"><img src="'.$profile["main_email_url"].'" alt="'.$agent_name.' '.$agent_l_name.'" /></a>';
                  if(isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '1') echo '<a class="email" href="mailto:'.$profile['data']['main_email'].'">'.$profile['data']['main_email'].'</a>';
                  $cut_position = strrpos(substr($description, 0, 400), '.', -1);
                  if(!$cut_position) $cut_position = 399;
                  echo '<div class="about" '.$this->itemprop_description.'>'.substr($description, 0, $cut_position + 1).'</div>';
              ?>
          </div>
          <ul>
              <?php if(isset($profile['materials']['website']['value'])): ?>
              <li class="website">
                <a class="wpl-tooltip-top" <?php echo $this->itemprop_url; ?> href="<?php
                  $urlStr = $profile['materials']['website']['value'];
                  $parsed = parse_url($urlStr);

                  if(empty($parsed['scheme'])) $urlStr = 'http://' . ltrim($urlStr, '/');
                  echo $urlStr;
                  ?>" target="_blank"><?php echo $urlStr; ?></a>
                <div class="wpl-util-hidden"><?php echo $profile['materials']['website']['value']; ?></div>
              </li>
              <?php endif; ?>

              <?php if(isset($profile['materials']['tel']['value'])): ?>
              <li <?php echo $this->itemprop_telephone; ?> class="phone">
                  <span><?php echo $profile['materials']['tel']['value']; ?></span>
                <a class="wpl-tooltip-top phone-link" href="tel:<?php echo $profile['materials']['tel']['value']; ?>"><?php echo $profile['materials']['tel']['value']; ?></a>
                <div class="wpl-util-hidden"><?php echo $profile['materials']['tel']['value']; ?></div>
              </li>
              <?php endif; ?>

              <?php if(isset($profile['materials']['mobile']['value'])): ?>
              <li <?php echo $this->itemprop_telephone; ?> class="mobile">
                  <span><?php echo $profile['materials']['mobile']['value']; ?></span>
  				<a class="wpl-tooltip-top mobile-link" href="tel:<?php echo $profile['materials']['mobile']['value']; ?>"><?php echo $profile['materials']['mobile']['value']; ?></a>
                <div class="wpl-util-hidden"><?php echo $profile['materials']['mobile']['value']; ?></div>
  			  </li>
              <?php endif; ?>

              <?php if(isset($profile['materials']['fax']['value'])): ?>
              <li <?php echo $this->itemprop_faxNumber; ?> class="fax wpl-tooltip-top">
                  <span><?php echo $profile['materials']['fax']['value']; ?></span>
              </li>
              <div class="wpl-util-hidden"><?php echo $profile['materials']['fax']['value']; ?></div>
              <?php endif ;?>
          </ul>
      </div>
    </div>
    <?php
}
