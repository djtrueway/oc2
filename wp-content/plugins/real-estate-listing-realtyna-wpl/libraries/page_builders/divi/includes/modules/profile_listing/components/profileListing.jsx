import React from 'react'
import ProfileImage from '../assets/img/profile.png'

export default class ProfileListing extends React.Component {
  render () {
    return (
      <div className="wpl-column">
        <div className="wpl_profile_container grid_box" id="wpl_profile_container">
          <div className="wpl_profile_picture">
            <div className="front">
              <img src={this.props.data.image} />
            </div>
            <div className="back">
              <a className="view_properties" href="/properties">View properties</a>
            </div>
          </div> {/* profile picture content */}
          <div className=" wpl_profile_container_title">
            <div className="title">
              <a href="/listing">{ this.props.data.name }</a>
              <a className="view_properties" href="/properties">View properties</a>
            </div>
            {
              this.props.data.email ? (
                <a href={`mailto: ${this.props.data.email}`}>
                  { this.props.data.email }
                </a>
              ) : ''
            }
            <div className="about">
              { this.props.data.about }
            </div>
          </div> {/* title content */}
          <ul>
            {
              this.props.data.moreInformation.map((item, index) => {
                return (
                  <li className={ item.className }>
                    <a
                      href={item.href}
                      className={`wpl-tooltip-top ${item.linkClassName}`}
                      target="_blank"
                    >
                      { item.title }
                    </a>
                  </li>
                )
              })
            }
          </ul> {/* profile information */}
        </div>
      </div>
    )
  }
}
