import React from 'react'
import PropertyListView from './components/propertyListView'

export default class PropertyListing extends React.Component {

  static slug = 'et_pb_wpl_property_listing'

  state = {
    listings: [
      {
        title: ' Villa For sale',
        location: 'Culberson County, Texas',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/91/thiStock-134981880_365x240.jpg',
        tag: true,
        price: '$5,500',
        wplPro: true,
        iconBox: [
          {
            className: 'bedroom',
            name: 'Bedroom(s)',
            value: '3.5'
          },
          {
            className: 'bathroom',
            name: 'Bathroom(s)',
            value: '3.5'
          },
          {
            className: 'pic_count',
            name: 'Picture(s)',
            value: '3.5'
          },
          {
            className: 'built_up_area',
            name: 'Sqft',
            value: '2000'
          }
        ]
      },
      {
        title: ' Villa For sale',
        location: 'Culberson County, Texas',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/91/thiStock-134981880_365x240.jpg',
        tag: true,
        price: '$7,500',
        wplPro: true,
        iconBox: [
          {
            className: 'bedroom',
            name: 'Bedroom(s)',
            value: '5.5'
          },
          {
            className: 'bathroom',
            name: 'Bathroom(s)',
            value: '5.5'
          },
          {
            className: 'pic_count',
            name: 'Picture(s)',
            value: '5.5'
          },
          {
            className: 'built_up_area',
            name: 'Sqft',
            value: '3000'
          }
        ]
      },
      {
        title: ' Villa For sale',
        location: 'Culberson County, Texas',
        image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/91/thiStock-134981880_365x240.jpg',
        tag: true,
        price: '$10,500',
        wplPro: true,
        iconBox: [
          {
            className: 'bedroom',
            name: 'Bedroom(s)',
            value: '7.5'
          },
          {
            className: 'bathroom',
            name: 'Bathroom(s)',
            value: '7.5'
          },
          {
            className: 'pic_count',
            name: 'Picture(s)',
            value: '7.5'
          },
          {
            className: 'built_up_area',
            name: 'Sqft',
            value: '4000'
          }
        ]
      }
    ]
  }

  render () {
    return (
      <div className='wpl_property_listing_container' id='wpl_property_listing_container'>
        <div className="wpl_plisting_top_sidebar_container"></div>
        <PropertyListView data={this.state.listings} rss={true} />
        <div id="wpl_plisting_lightbox_content_container" className="wpl-util-hidden"></div>
      </div>
    )
  }
}
