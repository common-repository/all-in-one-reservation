; 'use strict';

var aiorFBuilder = {
	init: function () {
		this.drag();
		this.render();
		this.event();
		this.json_heartbeat();
		this.preview();
		this.readonly();
		jQuery( '.wp_color_picker' ).wpColorPicker( {defaultColor:false,palettes:true} );

		$( document ).on(
			'click',
			'.radio_image',
			function(){
				$( this ).parents( 'ul' ).find( 'li' ).removeClass( 'active' );
				$( this ).find( 'input' ).prop( "checked","checked" );
				$( this ).parents( 'li' ).addClass( 'active' );
			}
		);
	},
	uid:function() {return Math.floor( Math.random() * (100000 - 1 + 1) + 57 )},
	drag:function(){
		$ = jQuery;
		$( ".sol_fb_row" ).draggable( {helper: function () {return aiorFBuilder.get_row_field()},connectToSortable: ".sol_fbuilder_area"} );

		$( ".sol_fb_text" ).draggable( {helper: function () {return aiorFBuilder.get_text_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_textarea" ).draggable( {helper: function () {return aiorFBuilder.get_textarea_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_number" ).draggable( {helper: function () {return aiorFBuilder.get_number_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_email" ).draggable( {helper: function () {return aiorFBuilder.get_email_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_password" ).draggable( {helper: function () {return aiorFBuilder.get_password_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_date" ).draggable( {helper: function () {return aiorFBuilder.get_date_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_button" ).draggable( {helper: function () {return aiorFBuilder.get_button_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_select" ).draggable( {helper: function () {return aiorFBuilder.get_select_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_radio" ).draggable( {helper: function () {return aiorFBuilder.get_radio_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_checkbox" ).draggable( {helper: function () {return aiorFBuilder.get_checkbox_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_calendar" ).draggable( {helper: function () {return aiorFBuilder.get_calendar_field()},connectToSortable: ".sol_fbuilder_column"} );
		$( ".sol_fb_time" ).draggable( {helper: function () {return aiorFBuilder.get_time_field()},connectToSortable: ".sol_fbuilder_column"} );

		ba = $( ".sol_fbuilder_area" );
		if (ba.length > 0) {
			ba.sortable(
				{
					cursor:'move',placeholder:'placeholder',start: function (e, ui) {ui.placeholder.height( ui.helper.outerHeight() )},
					stop: function (ev, ui) {aiorFBuilder.preview();
						aiorFBuilder.init_row()
					}
				}
			);
			$( ".sol_fbuilder_area,.sol_fbuilder_column" ).disableSelection();
		}
	},
	init_row:function(){
		sbc = $( ".sol_fbuilder_column" );
		if (sbc.length > 0) {
			sbc.sortable( {cursor:'move',placeholder:'placeholder',start: function (e, ui) {ui.placeholder.height( ui.helper.outerHeight() )},stop: function (ev, ui) {aiorFBuilder.preview()}} );
		}
	},
	render:function(){
		$ = jQuery;
		a = $( '#rf_droped' ).val();
		if (a) {
			$( '.sol_fbuilder_area' ).append( this.get_row( JSON.parse( a ) ) )};
		aiorFBuilder.init_row()
	},
	event:function(){
		$( document ).on( "click", ".sol_field_el", function(){$( this ).parent( ".form_builder_field" ).css( 'height','auto' );$( this ).toggleClass( 'active' );$( this ).next( ".row.li_row" ).toggle()} );
		$( document ).on(
			'click',
			'.add_more_select',
			function () {
				$( this ).closest( '.form_builder_field' ).css( 'height','auto' );
				var field  = $( this ).attr( 'data-field' );
				var option = aiorFBuilder.uid();
				$( '.field_extra_info_' + field ).append( '<div data-field="' + field + '" class="solrow select_row_' + field + '" data-opt="' + option + '"><div class="solcol"><div class="form-group"><input type="text" value="Option" class="s_opt form-control"/></div></div><div class="solcol"><div class="form-group"><input type="text" value="Value" class="s_val form-control"/></div></div><div class="solcol"><i class="dashicons dashicons-insert  add_more_select" data-field="' + field + '"></i><i class="dashicons dashicons-no remove_more_select" data-field="' + field + '"></i></div></div>' );
				var options = '';
				$( '.select_row_' + field ).each(
					function () {
						var opt   = $( this ).find( '.s_opt' ).val();
						var val   = $( this ).find( '.s_val' ).val();
						var s_opt = $( this ).attr( 'data-opt' );
						options  += '<option data-opt="' + s_opt + '" value="' + val + '">' + opt + '</option>';
					}
				);
				$( 'select[name=select_' + field + ']' ).html( options );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'click',
			'.add_more_radio',
			function () {
				$( this ).closest( '.form_builder_field' ).css( 'height', 'auto' );
				var field  = $( this ).attr( 'data-field' );
				var option = aiorFBuilder.uid();
				$( '.field_extra_info_' + field ).append( '<div data-opt="' + option + '" data-field="' + field + '" class="solrow radio_row_' + field + '"><div class="solcol"><div class="form-group"><input type="text" value="Option" class="r_opt form-control"/></div></div><div class="solcol"><div class="form-group"><input type="text" value="Value" class="r_val form-control"/></div></div><div class="solcol"><i class="dashicons dashicons-insert  add_more_radio" data-field="' + field + '"></i><i class="dashicons dashicons-no remove_more_radio" data-field="' + field + '"></i></div></div>' );
				var options = '';
				$( '.radio_row_' + field ).each(
					function () {
						var opt   = $( this ).find( '.r_opt' ).val();
						var val   = $( this ).find( '.r_val' ).val();
						var s_opt = $( this ).attr( 'data-opt' );
						options  += '<label class="mt-radio mt-radio-outline"><input data-opt="' + s_opt + '" type="radio" name="radio_' + field + '" value="' + val + '"> <p class="r_opt_name_' + s_opt + '">' + opt + '</p><span></span></label>';
					}
				);
				$( '.radio_list_' + field ).html( options );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'click',
			'.add_more_checkbox',
			function () {
				$( this ).closest( '.form_builder_field' ).css( 'height', 'auto' );
				var field  = $( this ).attr( 'data-field' );
				var option = aiorFBuilder.uid();
				$( '.field_extra_info_' + field ).append( '<div data-opt="' + option + '" data-field="' + field + '" class="solrow checkbox_row_' + field + '"><div class="solcol"><div class="form-group"><input type="text" value="Option" class="c_opt form-control"/></div></div><div class="solcol"><div class="form-group"><input type="text" value="Value" class="c_val form-control"/></div></div><div class="solcol"><i class="dashicons dashicons-insert  add_more_checkbox" data-field="' + field + '"></i><i class="dashicons dashicons-no remove_more_checkbox" data-field="' + field + '"></i></div></div>' );
				var options = '';
				$( '.checkbox_row_' + field ).each(
					function () {
						var opt   = $( this ).find( '.c_opt' ).val();
						var val   = $( this ).find( '.c_val' ).val();
						var s_opt = $( this ).attr( 'data-opt' );
						options  += '<label class="mt-checkbox mt-checkbox-outline"><input data-opt="' + s_opt + '" name="checkbox_' + field + '" type="checkbox" value="' + val + '"> <p class="c_opt_name_' + s_opt + '">' + opt + '</p><span></span></label>';
					}
				);
				$( '.checkbox_list_' + field ).html( options );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.s_opt',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( 'select[name=select_' + field + ']' ).find( 'option[data-opt=' + option + ']' ).html( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.s_val',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( 'select[name=select_' + field + ']' ).find( 'option[data-opt=' + option + ']' ).val( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.r_opt',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( '.radio_list_' + field ).find( '.r_opt_name_' + option ).html( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.r_val',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( '.radio_list_' + field ).find( 'input[data-opt=' + option + ']' ).val( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.c_opt',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( '.checkbox_list_' + field ).find( '.c_opt_name_' + option ).html( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'keyup',
			'.c_val',
			function () {
				var op_val = $( this ).val();
				var field  = $( this ).closest( '.row' ).attr( 'data-field' );
				var option = $( this ).closest( '.row' ).attr( 'data-opt' );
				$( '.checkbox_list_' + field ).find( 'input[data-opt=' + option + ']' ).val( op_val );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'click',
			'.edit_bal_textfield',
			function () {
				var field = $( this ).attr( 'data-field' );
				var el    = $( '.field_extra_info_' + field );
				el.html( '<div class="form-group"><input type="text" name="label_' + field + '" class="form-control" placeholder="Enter Text Field Label"/></div><div class="mt-checkbox-list"><label class="mt-checkbox mt-checkbox-outline"><input name="req_' + field + '" type="checkbox" value="1"> Required<span></span></label></div>' );
				aiorFBuilder.preview();
			}
		);
		$( document ).on(
			'click',
			'.remove_bal_field',
			function (e) {
				e.preventDefault();
				var field = $( this ).attr( 'data-field' );
				$( this ).closest( '.li_' + field ).hide(
					'400',
					function () {
						$( this ).remove();
						aiorFBuilder.preview();
					}
				);
			}
		);
		$( document ).on(
			'click',
			'.remove_more_select',
			function () {
				var field = $( this ).attr( 'data-field' );
				$( this ).closest( '.select_row_' + field ).hide(
					'400',
					function () {
						$( this ).remove();
						var options = '';
						$( '.select_row_' + field ).each(
							function () {
								var opt   = $( this ).find( '.s_opt' ).val();
								var val   = $( this ).find( '.s_val' ).val();
								var s_opt = $( this ).attr( 'data-opt' );
								options  += '<option data-opt="' + s_opt + '" value="' + val + '">' + opt + '</option>';
							}
						);
						$( 'select[name=select_' + field + ']' ).html( options );
						aiorFBuilder.preview();
					}
				);
			}
		);
		$( document ).on(
			'click',
			'.remove_more_radio',
			function () {
				var field = $( this ).attr( 'data-field' );
				$( this ).closest( '.radio_row_' + field ).hide(
					'400',
					function () {
						$( this ).remove();
						var options = '';
						$( '.radio_row_' + field ).each(
							function () {
								var opt   = $( this ).find( '.r_opt' ).val();
								var val   = $( this ).find( '.r_val' ).val();
								var s_opt = $( this ).attr( 'data-opt' );
								options  += '<label class="mt-radio mt-radio-outline"><input data-opt="' + s_opt + '" type="radio" name="radio_' + field + '" value="' + val + '"> <p class="r_opt_name_' + s_opt + '">' + opt + '</p><span></span></label>';
							}
						);
						$( '.radio_list_' + field ).html( options );
						aiorFBuilder.preview();
					}
				);
			}
		);
		$( document ).on(
			'click',
			'.remove_more_checkbox',
			function () {
				var field = $( this ).attr( 'data-field' );
				$( this ).closest( '.checkbox_row_' + field ).hide(
					'400',
					function () {
						$( this ).remove();
						var options = '';
						$( '.checkbox_row_' + field ).each(
							function () {
								var opt   = $( this ).find( '.c_opt' ).val();
								var val   = $( this ).find( '.c_val' ).val();
								var s_opt = $( this ).attr( 'data-opt' );
								options  += '<label class="mt-checkbox mt-checkbox-outline"><input data-opt="' + s_opt + '" name="checkbox_' + field + '" type="checkbox" value="' + val + '"> <p class="r_opt_name_' + s_opt + '">' + opt + '</p><span></span></label>';
							}
						);
						$( '.checkbox_list_' + field ).html( options );
						aiorFBuilder.preview();
					}
				);
			}
		);
		/* Row/Column Inisilization */
		$( document ).on(
			'change',
			'.form_input_row',
			function () {
				fld               = $( this ).data( 'field' );
				max               = $( this ).attr( 'max' );
				li                = '.li_' + fld
				row               = $( li + ' .input_type_row' );
				col               = $( li + ' .input_type_row .solcol3' );
				n                 = col.length;
				var direction     = this.defaultValue < this.value
				this.defaultValue = this.value;
				if (direction) {
					if (n < max) {
						row.append( '<div class="col-md-6 solcol3"><div class="sol_fbuilder_column"></div></div>' );
						aiorFBuilder.init_row();
					}
				} else {
					col.children().last().parent( '.solcol3' ).remove()}

			}
		);

		/* Render HTML/JSON on other event */
		$( document ).on( 'keyup', '.form_input_button_class', function () {aiorFBuilder.preview()} );
		$( document ).on( 'keyup', '.form_input_button_value', function () {aiorFBuilder.preview()} );
		$( document ).on( 'change', '.form_input_req', function () {aiorFBuilder.preview()} );
		$( document ).on( 'keyup', '.form_input_placeholder', function () {aiorFBuilder.preview()} );
		$( document ).on( 'keyup', '.form_input_msg', function () {aiorFBuilder.preview()} );
		$( document ).on( 'keyup', '.form_input_label', function () {aiorFBuilder.preview()} );
		$( document ).on( 'keyup', '.form_input_name', function () {aiorFBuilder.preview()} );
		$( document ).on( 'click', '.export_html', function () {aiorFBuilder.preview( 'html' )} );
		$( document ).on( 'click', '.rfb_export_json', function () {aiorFBuilder.preview( 'json' );return false} );

	},
	get_row:function(arr=''){
		z = '';
		x = '2';
		for ( var d in arr ) {
			var f = aiorFBuilder.uid();
			x     = Object.keys( arr[d] ).length;
			/* Get Rows */
			z        += '<div class="li_' + f + ' form_builder_field form_builder_field_row">';
				z    += this.field_head( f,'Row','' );
				z    += '<div class="row li_row form_output_row" data-type="row" data-field="' + f + '">';
				   z += '<div class="col-md-12"><div class="form-group"><input type="number" name="label_' + f + '" class="form-control form_input_row" value="' + x + '" data-field="' + f + '" max="4" min="1"></div></div>';
				   z += '<div class="input_type_row solrow3">';
				   /* Columns */
				   z += this.get_column( arr[d] );
				   /* Columns End */
				   z += '</div>';
				z    += '</div>';
			z        += '</div>';
		}
		return z;
	},
	get_column:function(c=''){
		z = '';
		for ( var d in c ) {
			g      = c[d];
			z     += '<div class="col-md-6 solcol3"><div class="sol_fbuilder_column" data-type="column">';
				z += this.get_field( g ).get( 0 ).outerHTML;
			z     += '</div></div>';
		}
		return z;
	},
	get_field:function(c=''){
		z = '';
		for (var d in c) {
			if (c.hasOwnProperty( d )) {
				f = c[d];
				t = f['t'];
				if ( t == 'button') {
					z = this.get_button_field( f );}
				if ( t == 'text') {
					z = this.get_text_field( f ); }
				if ( t == 'number') {
					z = this.get_number_field( f )}
				if ( t == 'email') {
					z = this.get_email_field( f )}
				if ( t == 'password') {
					z = this.get_password_field( f )}
				if ( t == 'date') {
					z = this.get_date_field( f )}
				if ( t == 'textarea') {
					z = this.get_textarea_field( f )}
				if ( t == 'select') {
					z = this.get_select_field( f )}
				if ( t == 'radio') {
					z = this.get_radio_field( f )}
				if ( t == 'checkbox') {
					this.get_checkbox_field( f )}
				if ( t == 'calendar') {
					this.get_calendar_field( f )}
				if ( t == 'time') {
					this.get_time_field( f )}

			}
		}
		return z;
	},
	field_container:function(f,n,h){
		return $( '<div>' ).addClass( 'li_' + f + ' form_builder_field p_' + n ).html( h );
	},
	get_button_field:function(a='') {
		n     = a['n'] ? a['n'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,n,'Button' );
		h    += '<div class="row li_row form_output" data-type="button" data-field="' + f + '"><div class="col-md-12"><div class="form-group"><input type="text" name="class_' + f + '" class="form-control form_input_button_class" placeholder="Class" value="btn btn-primary" data-field="' + f + '"/></div></div><div class="col-md-12"><div class="form-group"><input type="text" name="value_' + f + '" data-field="' + f + '" class="form-control form_input_button_value" value="Submit" placeholder="Value"/></div></div>';
		h    += this.attr_name( f,n );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_text_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Text' );
		h    += '<div class="row li_row form_output" data-type="text" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_number_field:function(a='') {
		l      = a['l'] ? a['l'] : 'Label';
		n      = a['n'] ? a['n'] : '';
		c      = a['c'] ? a['c'] : '';
		p      = a['p'] ? a['p'] : '';
		r      = a['r'] ? 'checked' : '';
		m      = a['m'] ? a['m'] : '';
		x      = a['x'] ? a['x'] : '';
		y      = a['y'] ? a['y'] : '';
		var f  = aiorFBuilder.uid();
		var h  = this.field_head( f,l,'Number' );
		h     += '<div class="row li_row form_output" data-type="number" data-field="' + f + '">';
		h     += this.attr_label( f,l );
		h     += this.attr_placeholder( f,p );
		h     += this.attr_name( f,n );
		h     += this.attr_class( f,c );
		h     += '<div class="col-md-12">';
			h += '<div class="solrow2">';
			h += '<div classs="solcol2">Min:<input type="number" name="text_' + f + '" class="form-control form_input_min" value="' + x + '"></div>';
			h += '<div classs="solcol2">Max:<input type="number" name="text_' + f + '" class="form-control form_input_max" value="' + y + '"></div>';
			h += '</div>';
		h     += '</div>';
		h     += this.attr_required( f,r,m );
		h     += '</div>';
		return this.field_container( f,n,h );
	},
	get_email_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Email' );
		h    += '<div class="row li_row form_output" data-type="email" data-field="' + f + '">'
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_password_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Password' );
		h    += '<div class="row li_row form_output" data-type="password" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_date_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Date' );
		h    += '<div class="row li_row form_output" data-type="date" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_textarea_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Textarea' );
		h    += '<div class="row li_row form_output" data-type="textarea" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_select_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		o     = a['o'] ? a['o'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var g = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Select' );
		h    += '<div class="row li_row form_output" data-type="select" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '<div class="col-md-12"><div class="form-group">';
		h    += '<select name="select_' + f + '" class="form-control"><option data-opt="' + g + '" value="Value">Option</option></select>';
		h    += '</div></div>';
		h    += '<div class="row li_row"><div class="col-md-12"><div class="field_extra_info_' + f + '">';
		if ( o ) {
			for ( i in o ) {
				if (o.hasOwnProperty( i )) {
					d  = o[i];
					k  = d['k'];
					v  = d['v'];
					h += '<div data-field="' + f + '" class="solrow select_row_' + f + '" data-opt="' + g + '">';
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + k + '" class="s_opt form-control"/></div></div>'
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + v + '" class="s_val form-control"/></div></div>'
					h += '<div class="solcol"><i class="dashicons dashicons-insert  add_more_select" data-field="' + f + '"></i>';
					if (i != 0) {
						h += '<i class="dashicons dashicons-no remove_more_select" data-field="' + f + '"></i>'
					}
					h += '</div></div>'
				}
			}
		} else {
			h += '<div data-field="' + f + '" class="solrow select_row_' + f + '" data-opt="' + g + '"><div class="solcol"><div class="form-group"><input type="text" value="Option" class="s_opt form-control"/></div></div><div class="solcol"><div class="form-group"><input type="text" value="Value" class="s_val form-control"/></div></div><div class="solcol"><i class="dashicons dashicons-insert  add_more_select" data-field="' + f + '"></i></div></div>'
		}
		h += '</div></div></div></div>';
		return this.field_container( f,n,h );
	},
	get_radio_field:function(a=''){
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		o     = a['o'] ? a['o'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var g = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Radio' );
		h    += '<div class="row li_row form_output" data-type="radio" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '<div class="col-md-12"><div class="form-group"><div class="mt-radio-list radio_list_' + f + '"><label class="mt-radio mt-radio-outline"><input data-opt="' + g + '" type="radio" name="radio_' + f + '" value="Value"> <p class="r_opt_name_' + g + '">Option</p><span></span></label></div></div></div>';
		h    += '<div class="row li_row"><div class="col-md-12"><div class="field_extra_info_' + f + '">';
		if ( o ) {
			for ( i in o ) {
				if (o.hasOwnProperty( i )) {
					d  = o[i];
					k  = d['k'];
					v  = d['v'];
					h += '<div data-field="' + f + '" class="solrow radio_row_' + f + '" data-opt="' + g + '">';
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + k + '" class="r_opt form-control"></div></div>';
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + v + '" class="r_val form-control"></div></div>';
					h += '<div class="solcol"><i class="dashicons dashicons-insert  add_more_radio" data-field="' + f + '"></i>';
					if (i != 0) {
						h += '<i class="dashicons dashicons-no remove_more_radio" data-field="' + f + '"></i>'
					}
					h += '</div></div>';
				}
			}

		} else {
			h += '<div data-field="' + f + '" class="solrow radio_row_' + f + '" data-opt="' + g + '"><div class="solcol"><div class="form-group"><input type="text" value="Option" class="r_opt form-control"/></div></div><div class="solcol"><div class="form-group"><input type="text" value="Value" class="r_val form-control"/></div></div><div class="solcol"><i class="dashicons dashicons-insert add_more_radio" data-field="' + f + '"></i></div></div>';
		}
		h += '</div></div></div></div>';
		return this.field_container( f,n,h );
	},
	get_checkbox_field:function(a=''){
		l     = a['l'] ? a['l'] : 'Label';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		o     = a['o'] ? a['o'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var g = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Checkbox' );
		h    += '<div class="row li_row form_output" data-type="checkbox" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '<div class="col-md-12"><div class="form-group"><div class="mt-checkbox-list checkbox_list_' + f + '"><label class="mt-checkbox mt-checkbox-outline"><input data-opt="' + g + '" type="checkbox" name="checkbox_' + f + '" value="Value"> <p class="c_opt_name_' + g + '">Option</p><span></span></label></div></div></div>';
		h    += '<div class="row li_row"><div class="col-md-12"><div class="field_extra_info_' + f + '">';
		if ( o ) {
			for ( i in o ) {
				if (o.hasOwnProperty( i )) {
					d  = o[i];
					k  = d['k'];
					v  = d['v'];
					h += '<div data-field="' + f + '" class="solrow checkbox_row_' + f + '" data-opt="' + g + '">';
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + k + '" class="c_opt form-control"/></div></div>';
					h += '<div class="solcol"><div class="form-group"><input type="text" value="' + v + '" class="c_val form-control"/></div></div>';
					h += '<div class="solcol"><i class="dashicons dashicons-insert  add_more_checkbox" data-field="' + f + '"></i>';
					h += '<i class="dashicons dashicons-no remove_more_checkbox" data-field="' + f + '"></i>';
					h += '</div></div>';
				}
			}
		} else {
			h += '<div data-field="' + f + '" class="solrow checkbox_row_' + f + '" data-opt="' + g + '">';
			h += '<div class="solcol"><div class="form-group"><input type="text" value="Option" class="c_opt form-control"/></div></div>';
			h += '<div class="solcol"><div class="form-group"><input type="text" value="Value" class="c_val form-control"/></div></div>';
			h += '<div class="solcol"><i class="dashicons dashicons-insert  add_more_checkbox" data-field="' + f + '"></i></div></div>';
		}
		h += '</div></div></div></div>';
		return this.field_container( f,n,h );
	},
	get_calendar_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Calendar';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Calendar' );
		h    += '<div class="row li_row form_output" data-type="calendar" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_time_field:function(a='') {
		l     = a['l'] ? a['l'] : 'Time';
		n     = a['n'] ? a['n'] : '';
		c     = a['c'] ? a['c'] : '';
		p     = a['p'] ? a['p'] : '';
		r     = a['r'] ? 'checked' : '';
		m     = a['m'] ? a['m'] : '';
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'Time' );
		h    += '<div class="row li_row form_output" data-type="time" data-field="' + f + '">';
		h    += this.attr_label( f,l );
		h    += this.attr_placeholder( f,p );
		h    += this.attr_name( f,n );
		h    += this.attr_class( f,c );
		h    += this.attr_required( f,r,m );
		h    += '</div>';
		return this.field_container( f,n,h );
	},
	get_row_field:function(a=''){
		l     = a['l'] ? a['l'] : 'Row';
		x     = a['x'] ? a['x'] : 2;
		var f = aiorFBuilder.uid();
		var h = this.field_head( f,l,'' );
		h    += '<div class="row li_row form_output_row" data-type="row" data-field="' + f + '">';
		h    += '<div class="col-md-12"><div class="form-group"><input type="number" name="label_' + f + '" class="form-control form_input_row" value="' + x + '" data-field="' + f + '" max="4" min="1"></div></div>';
		h    += '<div class="input_type_row solrow3">';
		h    += '<div class="col-md-6 solcol3"><div class="sol_fbuilder_column" data-type="column"></div></div>';
		h    += '<div class="col-md-6 solcol3"><div class="sol_fbuilder_column" data-type="column"></div></div>';
		h    += '</div></div>';
		return $( '<div>' ).addClass( 'li_' + f + ' form_builder_field form_builder_field_row' ).html( h );
	},
	field_head:function(f,l,t){
		h = '<div class="sol_field_el"><div class="row li_row"><label><i class="dashicons dashicons-arrow-down"></i> ' + l;
		if (t) {
			h += ' <b class="ftype_' + t + '">[<small>' + t + '</small>]</b>';
		}
		h += '</label>';
		h += '<button type="button" class="btn btn-primary btn-sm remove_bal_field pull-right" data-field="' + f + '"><i class="dashicons dashicons-no"></i></button>';
		h += '</div></div>'
		return h;
	},
	attr_label:function(f,l){
		return '<div class="col-md-12"><div class="form-group"><input type="text" name="label_' + f + '" class="form-control form_input_label" value="' + l + '" data-field="' + f + '" placeholder="Label"></div></div>';
	},
	attr_placeholder:function(f,p){
		return '<div class="col-md-12"><div class="form-group"><input type="text" name="placeholder_' + f + '" data-field="' + f + '" class="form-control form_input_placeholder" placeholder="Placeholder" value="' + p + '"></div></div>';
	},
	attr_required:function(f,r,m){
		h  = '<div class="col-md-12"><div class="form-group"><input type="text" name="msg_' + f + '" class="form-control form_input_msg" value="' + m + '" data-field="' + f + '" placeholder="Error Message on Validation"></div></div>';
		h += '<div class="col-md-12"><div class="form-check"><label class="form-check-label"><input data-field="' + f + '" type="checkbox" class="form-check-input form_input_req" ' + r + '>Required</label></div></div>';
		return h;
	},
	attr_name:function(f,n){
		n = n ? n : 'rf_' + f;
		h = '<input type="hidden" name="text_' + f + '" class="form-control form_input_name" placeholder="Name" value="' + n + '">';
		return h;
	},
	attr_class:function(f,c){
		return '<div class="col-md-12"><div class="form-group"><input type="text" name="class_' + f + '" class="form-control form_input_class" placeholder="Class" value="' + c + '"></div></div>';
	},
	preview:function(p=''){
		h = '';
		if (p === 'html') {
			$( '.fbpreview' ).hide();
			$( '.plain_html' ).show().find( 'textarea' ).val( this.get_html() );
		} else if (p === 'json') {
			$( '#rf_form_json' ).val( this.get_json() )
		} else {
			if (p != 'jsno') {
				$( '.plain_html' ).hide();
				$( '.fbpreview' ).html( aiorFBuilder.get_html() ).show();
			}
			$( '#rf_form_json' ).val( this.get_json() )
		}
	},
	get_html:function(){
		var row = $( '.sol_fbuilder_area .form_output_row' );
		row.each(
			function(){
				var rwn = $( this ).attr( 'data-field' );
				h      += '<div class="solrow2">';
				var col = $( '.li_' + rwn + ' .sol_fbuilder_column' );
				col.each(
					function(){
						h    += '<div class="solcol2">';
						var z = $( this ).find( '.form_output' );
						if (z.length > 0) {
							z.each(
								function(){
									var data_type = $( this ).attr( 'data-type' );
									var label     = $( this ).find( '.form_input_label' ).val();
									var name      = $( this ).find( '.form_input_name' ).val();
									var required  = '';
									var checkbox  = $( this ).find( '.form_input_req' );
									if (checkbox.is( ':checked' )) {
										  required = ' <b style="color:red">*</b>';
									}
									if (data_type === 'text') {
										   var placeholder = $( this ).find( '.form_input_placeholder' ).val();
										   h              += '<div class="form-group"><label class="control-label">' + label + required + '</label><input type="text" name="' + name + '" placeholder="' + placeholder + '" class="form-control"></div>';
									}
									if (data_type === 'number') {
										var placeholder = $( this ).find( '.form_input_placeholder' ).val();
										h              += '<div class="form-group"><label class="control-label">' + label + required + '</label><input type="number" name="' + name + '" placeholder="' + placeholder + '" class="form-control"></div>';
									}
									if (data_type === 'email') {
										var placeholder = $( this ).find( '.form_input_placeholder' ).val();
										h              += '<div class="form-group"><label class="control-label">' + label + required + '</label><input type="email" name="' + name + '" placeholder="' + placeholder + '" class="form-control"></div>';
									}
									if (data_type === 'password') {
										var placeholder = $( this ).find( '.form_input_placeholder' ).val();
										h              += '<div class="form-group"><label class="control-label">' + label + required + '</label><input type="password" name="' + name + '" placeholder="' + placeholder + '" class="form-control"></div>';
									}
									if (data_type === 'textarea') {
										var placeholder = $( this ).find( '.form_input_placeholder' ).val();
										h              += '<div class="form-group"><label class="control-label">' + label + required + '</label><textarea rows="5" name="' + name + '" placeholder="' + placeholder + '" class="form-control"></textarea></div>';
									}
									if (data_type === 'date') {
										h += '<div class="form-group"><label class="control-label">' + label + required + '</label><input type="date" name="' + name + '" class="form-control"></div>';
									}
									if (data_type === 'button') {
										var btn_class = $( this ).find( '.form_input_button_class' ).val();
										var btn_value = $( this ).find( '.form_input_button_value' ).val();
										h            += '<button name="' + name + '" type="submit" class="' + btn_class + '">' + btn_value + '</button>';
									}
									if (data_type === 'select') {
										var option_html = '';
										$( this ).find( 'select option' ).each(
											function () {
												var option   = $( this ).html();
												var value    = $( this ).val();
												option_html += '<option value="' + value + '">' + option + '</option>';
											}
										);
										h += '<div class="form-group"><label class="control-label">' + label + required + '</label><select class="form-control" name="' + name + '">' + option_html + '</select></div>';
									}
									if (data_type === 'radio') {
										var option_html = '';
										$( this ).find( '.mt-radio' ).each(
											function () {
												var option   = $( this ).find( 'p' ).html();
												var value    = $( this ).find( 'input[type=radio]' ).val();
												option_html += '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input" name="' + name + '" value="' + value + '">' + option + '</label></div>';
											}
										);
										h += '<div class="form-group"><label class="control-label">' + label + required + '</label>' + option_html + '</div>';
									}
									if (data_type === 'checkbox') {
										var option_html = '';
										$( this ).find( '.mt-checkbox' ).each(
											function () {
												var option   = $( this ).find( 'p' ).html();
												var value    = $( this ).find( 'input[type=checkbox]' ).val();
												option_html += '<div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="' + name + '[]" value="' + value + '">' + option + '</label></div>';
											}
										);
										h += '<div class="form-group"><label class="control-label">' + label + required + '</label>' + option_html + '</div>';
									}
								}
							);
						} else {
							 h += '<smal><i>Add item here</i></smal>';
						}
						h += '</div>';
					}
				);
				h += '</div>';
			}
		);
		return h;
	},
	get_json:function(){
		var ro  = {};
		var row = $( '.sol_fbuilder_area .form_output_row' );
		row.each(
			function(ri){
				var rwn = $( this ).attr( 'data-field' );
				var col = $( '.li_' + rwn + ' .sol_fbuilder_column' );
				var co  = {};
				col.each(
					function(ci){
						var z = $( this ).find( '.form_output' );
						if (z.length > 0) {
							var h = {};
							z.each(
								function (i){
									var t  = $( this ).attr( 'data-type' );
									var l  = $( this ).find( '.form_input_label' ).val();
									var n  = $( this ).find( '.form_input_name' ).val();
									var c  = $( this ).find( '.form_input_class' ).val();
									var ph = '.form_input_placeholder';
									var mg = '.form_input_msg';
									var rq = '.form_input_req';
									if (t === 'text') {
										var p  = $( this ).find( ph ).val();
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'text','n':n,'l':l,'p':p,'r':r,'c':c,'m':m};
									}
									if (t === 'number') {
										var p  = $( this ).find( ph ).val();
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var x  = $( this ).find( '.form_input_min' ).val();
										var y  = $( this ).find( '.form_input_max' ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'number','n':n,'l':l,'p':p,'r':r,'c':c,'m':m,'x':x,'y':y};
									}
									if (t === 'email') {
										var p  = $( this ).find( ph ).val();
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'email','n':n,'l':l,'p':p,'r':r,'c':c,'m':m};
									}
									if (t === 'password') {
										var p  = $( this ).find( ph ).val();
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'password','n':n,'l':l,'p':p,'r':r,'c':c,'m':m};
									}
									if (t === 'textarea') {
										var p  = $( this ).find( ph ).val();
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'textarea','n':n,'l':l,'p':p,'r':r,'c':c,'m':m};
									}
									if (t === 'date') {
										var cb = $( this ).find( rq );
										var m  = $( this ).find( mg ).val();
										var r  = '';
										if (cb.is( ':checked' )) {
											r = '1'}
										h[i] = {'t':'date','n':n,'l':l,'p':p,'r':r,'c':c,'m':m};
									}
									if (t === 'button') {
										var btn_class = $( this ).find( '.form_input_button_class' ).val();
										var btn_value = $( this ).find( '.form_input_button_value' ).val();
										h[i]          = {'t':'button','n':n,'c':btn_class,'v':btn_value};
									}
									if (t === 'select') {
										var o = {};
										$( this ).find( 'select option' ).each(
											function (j) {
												var k = $( this ).html(),v = $( this ).val();
												o[j]  = {'k':k,'v':v}
											}
										);
										h[i] = {'t':'select','n':n,'l':l,'c':c};
										Object.assign( h[i],{'o':o} )
									}
									if (t === 'radio') {
										var o = {};
										$( this ).find( '.mt-radio' ).each(
											function (j) {
												var k = $( this ).find( 'p' ).html(),v = $( this ).find( 'input[type=radio]' ).val();
												o[j]  = {'k':k,'v':v};
											}
										);
										h[i] = {'t':'radio','n':n,'l':l,'c':c};
										Object.assign( h[i],{'o':o} )
									}
									if (t === 'checkbox') {
										var o = {};
										$( this ).find( '.mt-checkbox' ).each(
											function (j) {
												var k = $( this ).find( 'p' ).html(),v = $( this ).find( 'input[type=checkbox]' ).val();
												o[j]  = {'k':k,'v':v};
											}
										);
										h[i] = {'t':'checkbox','n':n,'l':l,'c':c};
										Object.assign( h[i],{'o':o} )
									}

								}
							);
						}
						if (h) {
							co[ci] = h};
					}
				);
				ro[ri] = co;
			}
		);
		return JSON.stringify( ro );
	},
	json_heartbeat:function(){setInterval( function(){jQuery( '.rfb_export_json' ).trigger( 'click' )},3000 )},
	readonly:function(){
		f = ['rf_slot','rf_booking_date','rf_booking_time_from','rf_booking_time_to','rf_slot','rf_first_name','rf_last_name','rf_email','rf_phone_no','rf_note'];
		for (i = 0;i < f.length;i++) {
			c = '.p_' + f[i];
			$( c + ' .remove_bal_field' ).remove();
			$( c ).parents( '.form_builder_field_row' ).find( '.remove_bal_field' ).remove();
			$( c ).parents( '.form_builder_field_row' ).find( '.form_input_row' ).hide()
			$( c ).draggable( { disabled: true } );
			$( c ).parents( '.form_builder_field_row' ).draggable( { disabled: true } );
			// $(c).parents('.form_builder_field_row').sortable( "disable" )
			$( c ).parents( '.form_builder_field_row' ).disableSelection();
		}
	}
};
jQuery( document ).ready(
	function(){
		(function($){
			aiorFBuilder.init();
		}(jQuery))
	}
);
