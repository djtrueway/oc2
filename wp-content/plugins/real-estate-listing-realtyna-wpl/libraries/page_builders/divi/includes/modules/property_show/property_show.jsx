import React from 'react'
import PropertyTabs from './components/PropertyTabs'

export default class PropertyShow extends React.Component {
  static slug = 'et_pb_wpl_property_show'
  render () {
    return (
      <div className='wpl_prp_show_container' id='wpl_prp_show_container'>
        <div className='wpl_prp_container' id='wpl_prp_container1'>
          <PropertyTabs />
          <div className='wpl_prp_container_content'>
            <div className='wpl-row wpl-expanded wpl_prp_container_content_title'>
              <div className='wpl-large-10 wpl-medium-10 wpl-small-12 wpl-columns'>
                <h1 className='title_text'>
                  Test1
                </h1>
                <h2 className='location_build_up'>
                  <span className='wpl-build-up-area'>450 sqft - </span>
                  <span className='wpl-location'>Alabama</span>
                </h2>
              </div>
              <div className='wpl-large-2 wpl-medium-2 wpl-small-12 wpl-columns'>
                <div className='wpl_qrcode_container' id='wpl_qrcode_container1'>
                  <img
                    src='http://localhost/wordpress/wp-content/uploads/WPL/qrcode/qr_3be25ca2b1db07a8d4ad3f1f7b7a9340.png'
                    width='90'
                    height='90'
                    alt='QR Code'
                  />
                </div>

              </div>
            </div>
            <div className='wpl-row wpl-expanded'>
              <div className='wpl-large-8 wpl-medium-7 wpl-small-12 wpl_prp_container_content_left wpl-column'>
                <div className='wpl_prp_show_detail_boxes wpl_category_1'>
                  <div className='wpl_prp_show_detail_boxes_title'>
                    <span>Basic Details</span>
                  </div>
                  <div className='wpl-small-up-1 wpl-medium-up-1 wpl-large-up-3 wpl_prp_show_detail_boxes_cont'>
                    <div id='wpl-dbst-show3' className='wpl-column rows other'>
                      Property Type :
                      <span>Office</span>
                    </div>
                    <div id='wpl-dbst-show3' className='wpl-column rows other'>
                      Listing Type :
                      <span>For Sale</span>
                    </div>
                    <div id='wpl-dbst-show3' className='wpl-column rows other'>
                      Listing ID :
                      <span>1000</span>
                    </div>
                  </div>
                </div>
                <div className='wpl_prp_show_detail_boxes wpl_category_2'>

                  <div className='wpl_prp_show_detail_boxes_title'>
                    <span>Address Map</span>
                  </div>
                  <div className='wpl-small-up-1 wpl-medium-up-1 wpl-large-up-3 wpl_prp_show_detail_boxes_cont'>
                    <div id='wpl-dbst-show3' className='wpl-column rows location Country'>
                      Country :
                      <span>US</span>
                    </div>
                    <div id='wpl-dbst-show3' className='wpl-column rows location State'>
                      State :
                      <span>Al</span>
                    </div>
                  </div>
                </div>
              </div>
              <div className='wpl-large-4 wpl-medium-5 wpl-small-12 wpl_prp_container_content_right wpl-column'>
                <div className='wpl_prp_right_boxes details'>
                  <div className='wpl_prp_right_boxes_title'>
                    <span>Office</span> For Sale
                  </div>
                  <div className='wpl_prp_right_boxes_content'>

                    <div className='wpl_right_boxe_details_top clearfix'>
                      <div className='wpl_prp_right_boxe_details_left'>
                        <ul>
                          <li>Listing ID : <span className='value'>1000</span></li>
                          <li>Bathrooms : <span className='value'>10</span></li>
                        </ul>
                      </div>
                      <div className='wpl_prp_right_boxe_details_right'></div>
                    </div>
                    <div className='wpl_prp_right_boxe_details_bot'>
                      <div className='price_box'>$1,220</div>
                    </div>
                  </div>
                </div>
                <div className='wpl_prp_show_position2'>

                  <div className='wpl_prp_right_boxes listing_links'>
                    <div className='wpl_prp_right_boxes_content clearfix'>
                      <div className='wpl_listing_links_container' id='wpl_listing_links_container1'>
                        <ul>
                          <li className='facebook_link'></li>
                          <li className='google_plus_link'></li>
                          <li className='twitter_link'></li>
                          <li className='pinterest_link'></li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <div className='wpl_prp_right_boxes agent_info'>
                    <div className='wpl_prp_right_boxes_title'>
                      <span>Agent</span> Info
                    </div>
                    <div className='wpl_prp_right_boxes_content clearfix'>

                      <div className='wpl_agent_info_activity' id='wpl_agent_info1'>
                        <div className='wpl_single_agent_info wpl_agent_info clearfix'>
                          <div className='wpl_agent_info_l'>
                            <div className='image_container'>
                              <div className='front'>
                                <div className='no_image'></div>
                              </div>
                            </div>
                            <div className='company_details'>
                              <div className='company_name'></div>
                            </div>
                          </div>
                          <div className='wpl_agent_info_r'>
                            <ul>
                              <li className='name'></li>
                              <li className='email'>
                                something@gmail.com
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className='wpl_prp_right_boxes listing_contact'>
                    <div className='wpl_prp_right_boxes_title'>
                      <span>Contact</span> Agent
                    </div>
                    <div className='wpl_prp_right_boxes_content clearfix'>
                      <div className='wpl_contact_container wpl-contact-listing-wp' id='wpl_contact_container231'>
                        <form id='wpl_contact_form231'>
                          <div className='form-field'>
                            <input className='text-box' type='text' name='fullname' placeholder='Full Name' />
                          </div>
                          <div className='form-field'>
                            <input className='text-box' type='text' name='phone' placeholder='Phone' />
                          </div>
                          <div className='form-field'>
                            <input className='text-box' type='text' name='email' placeholder='Email' />
                          </div>
                          <div className='form-field wpl-contact-listing-msg'>
                            <textarea className='text-box' type='text' name='fullname' placeholder='Message'></textarea>
                          </div>

                          <div className='form-field wpl-contact-listing-btn'>
                            <input class="btn btn-primary" type="submit" value="Send" />
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
