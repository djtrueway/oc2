<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->lazyload = (isset($instance['data']['lazyload']) and trim($instance['data']['lazyload']) != '') ? $instance['data']['lazyload'] : 0;
$lazy_load = $this->lazyload ? 'lazyimg' : '';
$src = $this->lazyload ? 'data-src' : 'src';

/** import js codes **/
$this->_wpl_import('widgets.agents.scripts.js', true, true);
?>
<ul class="wpl_agents_widget_container list <?php echo $this->css_class; ?>" >
    <?php
    foreach($wpl_profiles as $key=>$profile)
    {
        $agent_name   = (isset($profile['materials']['first_name']['value']) ? $profile['materials']['first_name']['value'] : '') ;
        $agent_l_name = (isset($profile['materials']['last_name']['value']) ? $profile['materials']['last_name']['value'] : '');
        ?>
        <li class="wpl_profile_box" id="wpl_profile_container<?php echo $profile['data']['id']; ?>" <?php echo $this->itemscope.' '.$this->itemtype_RealEstateAgent; ?>>
            <div class="profile_left">
                <a class="more_info" href="<?php echo $profile['profile_link']; ?>" class="view_properties">
                    <span
                        <?php
                        echo 'style="'.(isset($profile['profile_picture']['image_width']) ? 'width:'.$profile['profile_picture']['image_width'].'px;' : '').(isset($profile['profile_picture']['image_height']) ? 'height:'.$profile['profile_picture']['image_height'].'px;' : '').'"'; ?>>
                        <?php
                        if(isset($profile['profile_picture']['url']))
                        {
                            echo '<img class="'.$lazy_load.'" '.$this->itemprop_image.' '.$src.'="'.$profile['profile_picture']['url'].'" alt="'.$agent_name.' '.$agent_l_name.'" />';
                        }
                        else
                        {
                            echo '<div class="no_image"></div>';
                        }
                        ?>
                    </span>
                </a>
            </div>
            <div class="profile_right">
                 <ul>
                    <?php
                    echo '<li class="title" '.$this->itemprop_name.'>'.$agent_name.' '.$agent_l_name.'</li>';
                    if(isset($instance['data']['mailto_status']) && $instance['data']['mailto_status'] == 1)
                    {
                        if(isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '0') echo '<a '.$this->itemprop_email.' href="mailto:'.$profile['data']['main_email'].'"><img class="'.$lazy_load.'" '.$src.'="'.$profile["main_email_url"].'" alt="'.$agent_name.' '.$agent_l_name.'" /></a>';
                        if(isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '1') echo '<a class="email" '.$this->itemprop_email.' href="mailto:'.$profile['data']['main_email'].'">'.$profile['data']['main_email'].'</a>';
                    }
                    else
                    {
                        if (isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '0') echo '<img class="'.$lazy_load.'" '.$this->itemprop_email.' '.$src.'="' . $profile["main_email_url"] . '" alt="' . $agent_name . ' ' . $agent_l_name . '" />';
                        if (isset($profile['main_email_url']) and wpl_global::get_setting('profile_email_type') == '1') echo '<p class="email">'.$profile['data']['main_email'].'</p>';
                    }
                    if(isset($profile['materials']['website']['value'])): ?>
                        <li class="website">
                            <a <?php echo $this->itemprop_url; ?> href="<?php
                            $urlStr = $profile['materials']['website']['value'];
                            $parsed = parse_url($urlStr);
                            if (empty($parsed['scheme'])) {
                                $urlStr = 'http://' . ltrim($urlStr, '/');
                            }
                            echo $urlStr;
                            ?>" target="_blank"><?php echo __('View website', 'real-estate-listing-realtyna-wpl') ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if(isset($profile['materials']['tel']['value'])): ?>
                        <li class="phone"><?php echo $profile['materials']['tel']['value']; ?>
                            <a href="tel:<?php echo $profile['materials']['tel']['value']; ?>">
                                <span <?php echo $this->itemprop_telephone; ?>><?php echo $profile['materials']['tel']['value']; ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                     <?php if(isset($profile['materials']['company_address'])): ?>
                         <li style="display:none">
                             <div <?php echo $this->itemprop_address.' '.$this->itemscope.' '.$this->itemtype_PostalAddress; ?>  class="company_address"><span <?php echo $this->itemprop_addressLocality; ?>><?php echo $profile['materials']['company_address']['value']; ?></span></div>
                         </li>
                     <?php endif; ?>
                </ul>
            </div>
        </li>
    <?php
    }
    ?>
</ul>