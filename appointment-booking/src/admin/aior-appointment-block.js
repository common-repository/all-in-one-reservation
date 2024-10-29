; 'use strict';
/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */
// aior/appointment-booking
// brad/border-box
wp.blocks.registerBlockType(
	'aior/appointment-booking',
	{
		title      : 'Appointment',
		icon       : 'calendar-alt',
		category   : 'common',
		description: 'Display Appointment Booking Calendar',
		keywords   : ['booking', 'appointment','all-in-one-reservation'],
		attributes : {
			scode: {type:'string'},
			content: {type:'string'},
			color  : {type:'string'}
		},
		example: {
			attributes: {
				cover: 'preview.png',
				backgroundColor: '#ddd',
				opacity: 0.8,
				padding: 30,
				textColor: '#333',
				radius: 10,
				title: 'Booking Calendar display on front' ,
			},

			viewportWidth: 800
		},
		image:'preview.png',
		/* This configures how the content and color fields will work, and sets up the necessary elements */
		edit: (props) => {
			var blockProps = wp.blockEditor.useBlockProps()
			// const { attributes, setAttributes } = props;
			function updateContent(event) {
				props.setAttributes( {content: event.target.value} )
			}
			function updateColor(value) {
				props.setAttributes( {color: value.hex} )
			}
			function updateShortcode( event){
				props.setAttributes( {scode: event.target.value} );

			}
			var scode_ids = [];

			var rfObj = aior_reservation_form_obj;
			i         = 0;
			rfObj.forEach(
				function(item) {
					Object.keys( item ).forEach(
						function(key) {
							scode_ids[i] = React.createElement( "option",{value:key},item[key] );
							i++;
						}
					);
				}
			);


		return React.createElement(
			"div",
			blockProps,
			React.createElement( "h4",null,"Select Appintment Booking" ),
			React.createElement( "select",{value: props.attributes.scode,onChange: updateShortcode},scode_ids ),
			React.createElement( "div",{ class:"airo_render_scode"},"" ),
		);

		},
		save: function(props) {
			return wp.element.createElement(
				"div",
				{
				},
				'[reservation_booking id="' + props.attributes.scode + '"]'
			);
		}
	}
);
