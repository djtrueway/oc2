import React from 'react'

export default class widgetAgent extends React.Component {

  static slug = 'et_pb_wpl_widget_agents'

  state = {
    agents: [
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

  render() {
    return (
      <div className='wpl_agents_widget_container'>
        {
          this.state.agents.map((item, index) => {
            return (
              <div className='wpl_profile_container' id={`wpl_profile_container${index}`}>
                <div className='wpl_profile_picture'>
                  <div className='front'>
                    {
                      item.image ? <img src={item.image} height='250' /> : <div className='no_image' />
                    }
                  </div>
                  <div className='back'>
                    <a href='#' className='view_properties'>View Properties</a>
                  </div>
                </div> { /* Agents Picture */ }
                <div className='wpl_profile_container_title'>
                  <a href='#'><h2 class='title'>{ item.name }</h2></a>
                  <span>{ item.email }</span>
                </div>
                <ul>
                  {
                    item.moreInformation.map((info, index) => (
                      <li className={ `wpl-tooltip-top ${info.className}` }>
                        <a href={info.href} className={info.linkClassName}>{ info.title }</a>
                      </li>
                    ))
                  }
                </ul>
              </div>
            )
          })
        }
      </div>
    )
  }
}
