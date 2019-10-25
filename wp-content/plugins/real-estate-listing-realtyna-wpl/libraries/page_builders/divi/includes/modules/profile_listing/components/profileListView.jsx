import React from 'react'
import ProfileListing from './profileListing'

export default class ProfileListView extends React.Component {

  state = {

    listings: [
      {
        name: 'Sharon N.',
        email: 'info@realtyna.com',
        about: 'about Sharon',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/users/19/profile.png',
        moreInformation: [
          {
            className: 'website',
            linkClassName: '',
            title: 'any Website',
            href: '/website'
          },
          {
            className: 'phone',
            linkClassName: 'phone-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'mobile',
            linkClassName: 'mobile-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'fax',
            linkClassName: '',
            title: 'some Fax',
            href: ''
          },
        ]
      },
      {
        name: 'Nancy S.',
        email: 'info@realtyna.com',
        about: 'about Nancy',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/users/20/profile.png',
        moreInformation: [
          {
            className: 'website',
            linkClassName: '',
            title: 'any Website',
            href: '/website'
          },
          {
            className: 'phone',
            linkClassName: 'phone-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'mobile',
            linkClassName: 'mobile-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'fax',
            linkClassName: '',
            title: 'some Fax',
            href: ''
          }
        ]
      },
      {
        name: 'kate M.',
        email: 'info@realtyna.com',
        about: 'about Kate',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/users/21/profile.png',
        moreInformation: [
          {
            className: 'website',
            linkClassName: '',
            title: 'any Website',
            href: '/website'
          },
          {
            className: 'phone',
            linkClassName: 'phone-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'mobile',
            linkClassName: 'mobile-link',
            title: '5050505050505',
            href: 'tel:5050505050505'
          },
          {
            className: 'fax',
            linkClassName: '',
            title: 'some Fax',
            href: ''
          }
        ]
      }
    ]

  }


  render () {
    console.log(this.state.listings)
    return (
      <div className='wpl_sort_options_container'>
        <div className="wpl_sort_options_container_title">Sort Option</div>
        <ul>
          <li>
            <div className="wpl_plist_sort wpl_plist_sort_active sort_down">Name</div>
          </li>
          <li>
            <div className="wpl_plist_sort">Country</div>
          </li>
        </ul>
        <div className="wpl_list_grid_switcher wpl-list-grid-switcher-icon-text">
          <div id="grid_view" className="wpl-tooltip-top grid_view grid_box active">
              <span>Grid</span>
          </div>
          <div className="wpl-util-hidden">Grid</div>
          <div id="list_view" className="<?php echo wpl-tooltip-top list_view row_box">
              <span>List</span>
          </div>
          <div className="wpl-util-hidden">List</div>
        </div>
        <div class="wpl-row wpl-expanded wpl-small-up-1 wpl-medium-up-2 wpl-large-up-3 wpl_profile_listing_profiles_container clearfix">
          {
            this.state.listings.map((item, index) => <ProfileListing data={item}/>)
          }
        </div>
      </div>
    )
  }
}
