import React from 'react'

const PropertyListing = props => (
  <div className='wpl-column'>
    <div className='wpl_prp_cont wpl_prp_cont_old grid_box'>
      <div className="wpl_prp_top">
        <div className='wpl_prp_top_boxes front'>
          <div className='wpl_gallery_container' id='wpl_gallery_container2'>
            {
              props.data.image ? (
                <a className='noHover'>
                  <img src={props.data.image} className='wpl_gallery_image' width='365' height='240' />
                </a>
              ) : <a className='no_image_box' href='#propertyshow'></a>
            }
            {
              props.data.tag ? (
                <div className='wpl-listing-tags-wp'>
                  <div className='wpl-listing-tags-cnt'>
                    <div className='wpl-listing-tag sp_featured'>Featured</div>
                  </div>
                </div>
              ) : ''
            }
          </div>
        </div>
        <div className='wpl_prp_top_boxes back'>
          <a className='view_detail'>More Details</a>
        </div>
      </div>
      <div className='wpl_prp_bot'>
        <a className='view_detail'>
          <h3 className='wpl_prp_title'>{ props.data.title }</h3>
        </a>
        {
          props.data.location ? (
            <h4 className='wpl_prp_listing_location'>
              <span>{ props.data.location }</span>
            </h4>
          ) : ''
        }

        <div className='wpl_prp_listing_icon_box'>
          {
            props.data.iconBox.map((item, index) => (
              <div className={ item.className }>
                <span className='name'>
                  { item.name }
                </span>
                <span className='value'>{ item.value }</span>
              </div>
            ))
          }
        </div>

        {
          props.data.description ? (
            <div className='wpl_prp_desc'>
                { props.data.description }
            </div>
          ) : ''
        }
      </div>
      <div className="price_box">
        <span>{ props.data.price }</span>
      </div>
      {
        props.data.wplPro ? (
          <div className='wpl_prp_listing_like'>
            <div className='wpl_listing_links_container'>
              <ul>
                <li className='favorite_link added'>
                  <a href='#' id='wpl_favorite_remove_91'></a>
                </li>
              </ul>
            </div>
          </div>
        ) : ''
      }
    </div>
  </div>
)

export default PropertyListing
