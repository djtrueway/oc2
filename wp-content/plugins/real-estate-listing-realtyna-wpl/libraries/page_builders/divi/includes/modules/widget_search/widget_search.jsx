import React from 'react'

export default class WidgetSearch extends React.Component {

  static slug = 'et_pb_wpl_widget_search'

  render() {
    return (
      <div id='wpl_default_search_2'>
        <form
          action='http://localhost/wordpress/properties/'
          id='wpl_search_form_2'
          method='GET'
          className='wpl_search_from_box clearfix wpl_search_kind0 wpl-search-default'
        >
          <div id='wpl_searchwidget_2' className='clearfix'>
            <div className='wpl_search_from_box_top'>
              <div className='wpl_search_field_container wpl_search_field_text wpl_search_field_container_312 text_type'>
                <label htmlFor='sf2_text_field_312'>Property Page Title</label>
                <input name='sf2_text_field_312' type='text' id='sf2_text_field_312' placeholder='Property Page Title' />
              </div> {/* field 1 */}
              <div
                id='wpl2_search_field_container_10'
                className='wpl_search_field_container wpl_search_field_area wpl_search_field_container_10 minmax_selectbox_plus_type'
              >
                <label>Square Footage</label>
                <select
                  className='wpl_search_widget_field_unit'
                  name='sf2_unit_living_area'
                  id='sf2_unit_living_area'
                  style={{ display: 'none' }}
                >
                  <option value='1' selected='selected'>Sqft</option>
                  <option value='2'>m²</option>
                </select>
                <div className='chosen-container chosen-container-single' id='sf2_unit_living_area_chosen'>
                    <a className='chosen-single' tabIndex='-1'>
                      <span>Sqft</span>
                      <div> <b /> </div>
                    </a>
                    <div className='chosen-drop'>
                      <div className='chosen-search'>
                        input type='text' autoComplete='off' />
                      </div>
                      <ul className='chosen-results'>
                        <li className='active-result result-selected'>Sqft</li>
                        <li className='active-result'>m²</li>
                      </ul>
                    </div>
                </div>
              </div>{/* field 2 */}
              <div className='wpl_search_field_container wpl_search_field_price wpl_search_field_container_6 minmax_selectbox_plus_type'>
                <div className='chosen-container chosen-container-single' id='sf2_unit_living_area_chosen'>
                    <a className='chosen-single' tabIndex='-1'>
                      <span>$</span>
                      <div> <b /> </div>
                    </a>
                    <div className='chosen-drop'>
                      <div className='chosen-search'>
                        input type='text' autoComplete='off' />
                      </div>
                      <ul className='chosen-results'>
                        <li className='active-result result-selected'>$</li>
                        <li className='active-result'>€</li>
                      </ul>
                    </div>
                </div>
                <span className='wpl_search_slider_container wpl_listing_price_sale'>
                  <div className='chosen-container chosen-container-single' id='sf2_unit_living_area_chosen'>
                      <a className='chosen-single' tabIndex='-1'>
                        <span>Price</span>
                        <div> <b /> </div>
                      </a>
                      <div className='chosen-drop'>
                        <div className='chosen-search'>
                          input type='text' autoComplete='off' />
                        </div>
                        <ul className='chosen-results'>
                          <li className='active-result result-selected'>1000+</li>
                          <li className='active-result'>2000+</li>
                          <li className='active-result'>3000+</li>
                          <li className='active-result'>4000+</li>
                          <li className='active-result'>5000+</li>
                        </ul>
                      </div>
                  </div>
                </span>
              </div> { /* Field 3 */ }

              <div className='wpl_search_field_container wpl_search_field_container_3 select_type'>
                <label>Bedrooms</label>
                <select name='sf9_tmin_bedrooms' id='sf9_tmin_bedrooms' style={{ display: 'none' }}>
                  <option value='1'>1</option>
                </select>
                <div className='chosen-container chosen-container-single'>
                  <a className='chosen-single'>
                    <span>Bedrooms</span>
                    <div>
                      <b />
                    </div>
                  </a>
                  <div className='chosen-drop'>
                    <div className='chosen-search'>
                      <input type='text' autoComplete='off' />
                    </div>
                    <ul className='chosen-results'>
                    </ul>
                  </div>
                </div>
              </div> { /* Field 4 */}
              <div className='wpl_search_field_container wpl_search_field_container_3 select_type'>
                <label>Bedrooms</label>
                <select name='sf9_tmin_bedrooms' id='sf9_tmin_bedrooms' style={{ display: 'none' }}>
                  <option value='1'>1</option>
                </select>
                <div className='chosen-container chosen-container-single'>
                  <a className='chosen-single'>
                    <span>Bathrooms</span>
                    <div>
                      <b />
                    </div>
                  </a>
                  <div className='chosen-drop'>
                    <div className='chosen-search'>
                      <input type='text' autoComplete='off' />
                    </div>
                    <ul className='chosen-results'>
                    </ul>
                  </div>
                </div>
              </div> { /* Field 5 */}
              <div className='wpl_search_field_container wpl_search_field_container_3 select_type'>
                <label>Bedrooms</label>
                <select name='sf9_tmin_bedrooms' id='sf9_tmin_bedrooms' style={{ display: 'none' }}>
                  <option value='1'>1</option>
                </select>
                <div className='chosen-container chosen-container-single'>
                  <a className='chosen-single'>
                    <span>Listing Type</span>
                    <div>
                      <b />
                    </div>
                  </a>
                  <div className='chosen-drop'>
                    <div className='chosen-search'>
                      <input type='text' autoComplete='off' />
                    </div>
                    <ul className='chosen-results'>
                    </ul>
                  </div>
                </div>
              </div> { /* Field 6 */}
              <div className='search_submit_box'>
                <input
                  id='wpl_search_widget_submit2'
                  className='wpl_search_widget_submit'
                  type='submit'
                  value='Search'
                />
              </div> {/* Search submit */}
            </div>
            <div className='wpl_search_from_box_bot' id='wpl_search_from_box_bot2'></div>
          </div>

          <div  className='more_search_option' data-widget-id='2' id='more_search_option2'>
            More options
          </div> { /* more search options */}

        </form>
      </div>
    )
  }
}
