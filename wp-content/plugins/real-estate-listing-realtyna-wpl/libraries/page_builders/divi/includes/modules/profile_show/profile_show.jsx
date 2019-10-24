import React from 'react'
import AgentInfo from './components/agentInfo'
import PropertyListing from '../propertyListing/property_listing'

export default class ProfileListing extends React.Component {
  static slug = 'et_pb_wpl_profile_show'

  state = {
    agent: {
      name: 'Nancy N.',
      image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/users/20/profile.png',
      companyLogo: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/users/20/logo.png',
      company: 'Realtyna',
      location: '200 Continental Drive, Suite 401',
      about: 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.Ipsum.',
      otherFields: [
        {
          label: 'Gender',
          title: 'Female'
        }
      ],
      moreInformation: [
        {
          className: 'website',
          linkClassName: '',
          label: 'Website',
          title: 'any Website',
          href: '/website'
        },
        {
          className: 'phone',
          label: 'Phone',
          linkClassName: 'phone-link',
          title: '5050505050505',
          href: 'tel:5050505050505'
        },
        {
          className: 'mobile',
          linkClassName: 'mobile-link',
          label: 'Mobile',
          title: '5050505050505',
          href: 'tel:5050505050505'
        },
        {
          className: 'fax',
          label: 'Fax',
          linkClassName: '',
          title: 'some Fax',
          href: ''
        },
        {
          className: 'email',
          label: 'Email',
          linkClassName: '',
          title: 'info@realtyna.com',
          href: ''
        }
      ]
    }
  }

  render () {
    return (
      <div>
        <div className='wpl_profile_show_container' id='wpl_profile_show_container'>
          <div className='wpl_profile_show_container_box'>
            <AgentInfo data={this.state.agent} />
          </div>
        </div>
        <PropertyListing />
      </div>

    )
  }
}
