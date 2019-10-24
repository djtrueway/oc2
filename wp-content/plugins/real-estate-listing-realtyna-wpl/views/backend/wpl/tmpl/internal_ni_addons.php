<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$addons = array();
$affiliate_url = 'ref/311';

if(!wpl_global::check_addon('pro')) $affiliate_url = 'ref/312';  

$addons[0] = array('name'=>'WPL PRO', 'id'=>'3', 'addon_name'=>'pro', 'description'=>'Professional features such as Multilingual, PDF Flyer, Radius Search etc.', 'readmore_link'=>'https://realtyna.com/wpl-platform/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend' , 'button_text'=>'Upgrade', 'addon_tag'=>'Recommended');
$addons[1] = array('name'=>'MLS Add On', 'id'=>'1', 'addon_name'=>'mls', 'description'=>'MLS/IDX/RETS Integration', 'readmore_link'=>'https://realtyna.com/mls-idx-integration/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'Recommended');
$addons[2] = array('name'=>'Franchise Add On', 'id'=>'4', 'addon_name'=>'franchise', 'description'=>'Franchise/Multi Site support for WPL', 'readmore_link'=>'https://realtyna.com/franchise-multisite-solution/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[3] = array('name'=>'Importer Add On', 'id'=>'5', 'addon_name'=>'importer', 'description'=>'Import listings from CSV/XML files', 'readmore_link'=>'https://realtyna.com/data-importer-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[4] = array('name'=>'Complex Add On', 'id'=>'7', 'addon_name'=>'complex', 'description'=>'Adding Complexes/Condos and assign listings to a certain Complex/Condo', 'readmore_link'=>'https://realtyna.com/building-complex-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[5] = array('name'=>'Exporter Add On', 'id'=>'8', 'addon_name'=>'exporter', 'description'=>'Export Properties to XML/CSV files', 'readmore_link'=>'https://realtyna.com/exporter-add-on-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[6] = array('name'=>'Mortgage Calculator', 'id'=>'11', 'addon_name'=>'mortgage_calculator', 'description'=>'Mortgage Calculator', 'readmore_link'=>'https://realtyna.com/wpl-more-add-ons/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[7] = array('name'=>'Membership', 'id'=>'9', 'addon_name'=>'membership', 'description'=>'Empower your WordPress Real Estate website with an advanced Membership System for WPL.', 'readmore_link'=>'https://realtyna.com/membership-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[8] = array('name'=>'Availability Calendar', 'id'=>'13', 'addon_name'=>'calendar', 'description'=>'Availability info on calendar for vacation rental listings.', 'readmore_link'=>'https://realtyna.com/wpl-calendar-addon/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[9] = array('name'=>'Demographic Info', 'id'=>'12', 'addon_name'=>'demographic', 'description'=>'WPL Add-on for drawing and defining regions on the map for different demographic status.', 'readmore_link'=>'https://realtyna.com/demographic-info-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[10] = array('name'=>'Optimizer', 'id'=>'17', 'addon_name'=>'optimizer', 'description'=>'Optimize property images and speed up your website.', 'readmore_link'=>'https://realtyna.com/wpl-optimizer-add-on/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[11] = array('name'=>'Advanced Portal Search', 'id'=>'19', 'addon_name'=>'aps', 'description'=>'Advanced Search functionalities such as map search, map view, AJAX search, save search alerts, etc.', 'readmore_link'=>'https://realtyna.com/advanced-portal-search-aps/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'Recommended');
$addons[12] = array('name'=>'CRM', 'id'=>'14', 'addon_name'=>'crm', 'description'=>'Lead Generation & Management. Supports Unlimited Agents', 'readmore_link'=>'http://realtyna.com/crm/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'Recommended');
$addons[13] = array('name'=>'Tags Add On', 'id'=>'23', 'addon_name'=>'tags', 'description'=>'This add-on enables the admin to add new set of tags, choose the style and set them on properties.', 'readmore_link'=>'http://realtyna.com/wpl-more-add-ons/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[14] = array('name'=>'Mobile App', 'id'=>'6', 'addon_name'=>'mobile_application', 'description'=>'A professional Real Estate app, custom branded and totally integrated to your WPL-based website.', 'readmore_link'=>'https://realtyna.com/real-estate-iOS-Android-app/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'Recommended');
$addons[15] = array('name'=>'Booking Add On', 'id'=>'20', 'addon_name'=>'booking', 'description'=>'Enables the customers to book the vacation rental properties directly through the website.', 'readmore_link'=>'https://realtyna.com/booking-system-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[17] = array('name'=>'Neighborhoods Add On', 'id'=>'18', 'addon_name'=>'neighborhoods', 'description'=>'Define hierarchically neighborhoods using this addon and assign certain listings to related neighborhood.', 'readmore_link'=>'https://realtyna.com/neighborhood-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[18] = array('name'=>'Review Add On', 'id'=>'22', 'addon_name'=>'review', 'description'=>'This add-on enables customers to rate the features of properties which can be arranged through backend by admin.', 'readmore_link'=>'https://realtyna.com/review-rating-for-wpl/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[19] = array('name'=>'Yelp Integration', 'id'=>'23', 'addon_name'=>'yelp', 'description'=>'This add-on Show Points of Interest Nearby on Property Details Page.', 'readmore_link'=>'https://realtyna.com/yelp-integration/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[20] = array('name'=>'Market Report', 'id'=>'24', 'addon_name'=>'market_reports', 'description'=>'Identify Market & User Trends with Analytics and Reporting.', 'readmore_link'=>'https://realtyna.com/market-report/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[21] = array('name'=>'Brokerage Add-on', 'id'=>'25', 'addon_name'=>'brokerage', 'description'=>'Manage and oversee your brokerage.', 'readmore_link'=>'https://realtyna.com/brokerage-add-on/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[22] = array('name'=>'SMS Add-on', 'id'=>'26', 'addon_name'=>'SMS', 'description'=>'Using this add-on, automatic notifications like saved searches, new properties, new property inquiries, new visit requests etc. will be sent to the agents and leads mobile phones.', 'readmore_link'=>'https://realtyna.com/sms-addon/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[23] = array('name'=>'Facebook Add-on', 'id'=>'27', 'addon_name'=>'facebook', 'description'=>'Connect your website to Facebook ads, Send your listings and attract more leads from Facebook.', 'readmore_link'=>'https://realtyna.com/facebook-real-estate-add-on/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');
$addons[24] = array('name'=>'School Info Add-on', 'id'=>'28', 'addon_name'=>'school_info', 'description'=>'Display nearby schools on your real estate website, Add value to your content, Improve your website’s visitor experience, Each property page gets its own data', 'readmore_link'=>'https://realtyna.com/school-info-add-on/'.$affiliate_url.'/?utm_source=wpl-backend&utm_medium=link&utm_campaign=WplBackend', 'button_text'=>'More Info', 'addon_tag'=>'');

// Apply Filters
@extract(wpl_filters::apply('wpl_optional_addons', array('addons'=>$addons)));
?>
<div class="side-5 side-ni-addons" id="wpl_dashboard_ni_addons">
    <div class="panel-wp">
        <h3><?php echo __('Optional Add-ons', 'real-estate-listing-realtyna-wpl'); ?></h3>

        <div class="panel-body">
            <div class="wpl-ni-addons-wp wpl_ni_addons_container">
                <?php $i = 0; foreach($addons as $addon): if(wpl_global::check_addon($addon['addon_name'])) continue; $i++; ?>
                    <div class="wpl-ni-addon-row wpl_ni_addon_container" id="wpl_ni_addons_container<?php echo $addon['id']; ?>">
                        <div class="wpl_ni_addon_subject">
                            <span class="wpl_ni_addons_addon_name"><?php echo $addon['name']; ?></span>
                            <?php if(trim($addon['addon_tag']) != '') echo '<span class="wpl_ni_addon_tag">'.__($addon['addon_tag'], 'real-estate-listing-realtyna-wpl').'</span>'; ?>
                        </div>
                        <div class="wpl_ni_addon_description"><?php echo $addon['description']; ?></div>
                        <a class="readmore_link" href="<?php echo $addon['readmore_link']; ?>" target="_blank"><?php echo __($addon['button_text'], 'real-estate-listing-realtyna-wpl'); ?></a>
                    </div>
                <?php endforeach; ?>
                <?php if($i == 0): ?>
                	<div class="wpl-ni-addons-no-optional"><?php echo __('Congratulations! All the optional Add-on are installed on your website!', 'real-estate-listing-realtyna-wpl'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>