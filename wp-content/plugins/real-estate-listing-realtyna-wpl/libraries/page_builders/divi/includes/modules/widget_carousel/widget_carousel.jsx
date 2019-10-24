import React from 'react'

export default class WidgetCarousel extends React.Component {

  static slug = 'et_pb_wpl_widget_carousel'

  state = {
      properties: [
        {
          title: 'Office For Sale',
          image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/2424/thiStock-587884890_370x220.jpg'
        },
        {
          title: 'Rental For Rent',
          image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/2424/thiStock-587884890_370x220.jpg'
        },{
          title: 'Rental For Rent',
          image: 'https://wpl28.realtyna.com/divi/wp-content/uploads/WPL/2424/thiStock-587884890_370x220.jpg'
        }
      ]
  }

  render () {
    return (
      <div
        id="wpl-multi-images-2"
        class="wpl-plugin-owl wpl-carousel-multi-images container owl-responsive-1024 owl-theme owl-loaded owl-carousel"
      >
        <div classNanem='owl-stage-outer' style={{position: 'relative', overflow: 'hidden'}}>
          <div className='owl-stage' style={{ width: '4106px' }}>
            {
              this.state.properties.map((item, index) => {
                return (
                  <div className='owl-item active' style={{ width: '293.333px' }}>
                    <div className='wpl-carousel-item'>
                      <img src={ item.image } height='220' className='owl-lazy' />
                      <div className='title'>
                        <h3>{ item.title }</h3>
                        <a className='more_info'>More</a>
                      </div>
                    </div>
                  </div>
                )
              })
            }

          </div>
        </div>
      </div>
    )
  }
}
