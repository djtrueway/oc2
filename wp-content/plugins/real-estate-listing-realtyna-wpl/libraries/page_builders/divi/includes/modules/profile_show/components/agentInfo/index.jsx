import React from 'react'

const AgenInfo = props => (
  <div className='wpl_agent_info_activity' id='wpl_agent_info'>
    <div className='wpl_agent_info clearfix'>
      <div className='wpl_agent_details clearfix'>
        <div className='wpl_agent_info_l wpl_agent_info_pic'>
          {
            props.data.image ? <img src={props.data.image} /> : <div className='no_image' />
          }
        </div> {/* Agent Picture */}
        <div className='wpl_agent_info_detail'>
          <div className='wpl_agent_info_c wpl-large-8 wpl-medium-8 wpl-small-12 wpl-column clearfix'>
            <div className='wpl_profile_container_title'>
              { props.data.name }
            </div> { /* Agent Name */ }
            <ul className='wpl-agent-info-main-field'>
              {
                props.data.moreInformation.map((item, index) => {
                  return (
                    <li key={index} className={ item.className }>
                      <label>{ item.label }: </label>
                      <a
                        href={item.href}
                        className={`${item.linkClassName}`}
                        target="_blank"
                      >
                        { item.title }
                      </a>
                    </li>
                  )
                })
              }
            </ul> {/* More Information */}
            <ul className='wpl-agent-info-other-fields'>
              {
                props.data.otherFields.map((item, index) => {
                  return (
                    <li key={index}>
                      <label>{item.label}:</label>
                      <span>{item.title}</span>
                    </li>
                  )
                })
              }
            </ul> { /* Other Fields */ }
          </div> { /* agent details left */ }
          <div className='wpl_agent_info_r wpl-large-4 wpl-medium-4 wpl-small-12  wpl-column'>
            <img src={props.data.companyLogo} />
            <div className='company'>{ props.data.company }</div>
            <div className='location'>
              <span>{ props.data.location }</span>
            </div>
          </div> { /* agent detail right */ }
        </div> { /* agent info detauls */}
      </div> { /* agent detatils wrapper */}
      <div className='wpl_agent_about'>
        { props.data.about }
      </div>
    </div>
  </div>
)

export default AgenInfo
