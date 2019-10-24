import React from 'react'
import PropertyListing from './propertyListing'

const PropertyListView = props => (
  <div className='wpl_property_listing_list_view_container'>
    <div className='wpl_sort_options_container'>
      <div className="wpl_sort_options_container_title">Sort Option</div>
      <span className='wpl-sort-options-list active'>
        <ul>
          <li>
            <div className="wpl_plist_sort wpl_plist_sort_active sort_up">Listing ID</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Built up Area</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Price</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Pictures</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Add date</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Featured</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Rank</div>
          </li>
        </ul>
      </span>
      <div className="wpl_list_grid_switcher">
        <div id="grid_view" className="wpl-tooltip-top grid_view active">
        </div>
        <div id="list_view" className="wpl-tooltip-top list_view">
        </div>
        <div id="map_view" className="wpl-tooltip-top map_view">
        </div>
      </div>
      {
        props.rss ? (
          <div className="wpl-save-rss">
              <div className='wpl-rss-wp'>
                <a href='#' className='wpl-rss-link'><span>RSS</span></a>
              </div>
              <div className='wpl-save-search-wp wpl-plisting-link-btn'>
                <a id='wpl_save_search_link_lightbox' className='wpl-save-search-link'>
                  <span>Save Search</span>
                </a>
              </div>
          </div>
        ) : ''
      }
      {
        props.wplPro ? (
          <div className="wpl-print-rp-wp">
              <a className="wpl-print-rp-link"><span><i class="fa fa-print"></i></span></a>
          </div>
        ) : ''
      }
      {
        props.saveSerches ? (
          <div classname="wpl-save-search-wp wpl-plisting-link-btn">
              <a id="wpl_save_search_link_lightbox" classname="wpl-save-search-link" data-realtyna-href="#wpl_plisting_lightbox_content_container"><span>Save Search</span></a>
          </div>
        ) : ''
      }
      {
        props.aps ? (
          <div className="wpl-landing-page-generator-wp wpl-plisting-link-btn">
              <a id="wpl_landing_page_generator_link_lightbox" className="wpl-landing-page-generator-link" data-realtyna-href="#wpl_plisting_lightbox_content_container"><span>Create Landing Page</span></a>
          </div>
        ) : ''
      }
    </div>
    <div class="wpl-row wpl-expanded wpl-small-up-1 wpl-medium-up-2 wpl-large-up-3  wpl_property_listing_listings_container clearfix">
      { console.log(props.data)}
      {
        props.data ? props.data.map((item, index) => <PropertyListing data={item} key={index} />) : ''
      }
    </div>
  </div>
)

export default PropertyListView
