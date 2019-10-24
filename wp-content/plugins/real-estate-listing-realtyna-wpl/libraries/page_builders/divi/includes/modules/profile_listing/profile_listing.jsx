import React from 'react'
import ProfileListView from './components/profileListView'

import './style.scss'

export default class ProfileListing extends React.Component {
  static slug = 'et_pb_wpl_profile_listing'
  render () {
    return (
      <div className="wpl-profile-listing-wp" id="wpl_profile_listing_main_container">
          <div className="wpl_plisting_top_sidebar_container"></div>
          <div className="wpl_profile_listing_container wpl_profile_listing_list_view_container" id="wpl_profile_listing_container">
            <ProfileListView />
          </div>
      </div>
    )
  }
}
