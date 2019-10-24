import React from 'react'

export default class WidgetGooglemap extends React.Component {

  static slug = 'et_pb_wpl_widget_googlemap'
  
  render () {
    return (
      <div id='wpl_googlemap_widget_cnt' className='wpl-googlemap-widget'>
        <div className='wpl-googlemap-widget-link'>
          <a href='#mapView'>Map View</a>
        </div>
      </div>
    )
  }
}
