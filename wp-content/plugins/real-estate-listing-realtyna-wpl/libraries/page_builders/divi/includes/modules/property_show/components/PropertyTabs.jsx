import React from 'react'
import classNames from 'classnames'

export default class PropertyTabs extends React.Component {

  constructor (props) {
    super(props)

    this.state = {
      type: 'pictures'
    }

    this.onTabChange = this.onTabChange.bind(this)
  }

  onTabChange (type) {
    this.setState({ type })
  }

  render () {
    return (
      <div className='wpl_prp_show_tabs'>
        <div className='tabs_container'>
          {
            this.state.type === 'googleMap' ? (
              <div id='tabs-2' className='tabs_contents'>
                <div className='wpl_googlemap_container wpl_googlemap_pshow' id='wpl_googlemap_container15'>
                  <div className='wpl-map-add-ons'></div>
                  <div className='wpl_map_canvas' id='wpl_map_canvas15'></div>
                </div>
              </div>
            ) : (
              <div id='tabs-1' className='tabs_contents'>
                <div className='wpl_gallery_container' id='wpl_gallery_container1'>
                  <div className='gallery_no_image'></div>
                </div>
              </div>
            )
          }
        </div>

        <div className='tabs_box'>
          <ul className='tabs'>
            <li className={classNames({
              'active': this.state.type === 'pictures'
            })}
              onClick={() => this.onTabChange('pictures')}
            >
              <a>Pictures</a>
            </li>
            <li
              className={classNames({
                'active': this.state.type === 'googleMap'
              })}
              onClick={() => this.onTabChange('googleMap')}
            >
              <a>Google Map</a>
            </li>
          </ul>

        </div> {/* tabs box */}
      </div>
    )
  }
}
