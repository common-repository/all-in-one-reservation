<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aior
 * @subpackage Aior/admin
 * @author     Solwin Infotech <support@solwininfotech.com>
 */
class Aior_Builder {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}
	/**
	 * Add the Fields in the form.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the type, name, etc as arguments.
	 */
	public function add_field( $args ) {
		$args = self::fixed_empty_array( $args ); //phpcs:ignore
		if ( array_key_exists( 'type', $args ) ) {
			$type = $args ['type'];
			if ( 'text' === $type ) {
				echo self::field_type_text( $args ); //phpcs:ignore
			}
			if ( 'date' === $type ) {
				echo self::field_type_date( $args ); //phpcs:ignore
			}
			if ( 'email' === $type ) {
				echo self::field_type_email( $args ); //phpcs:ignore
			}
			if ( 'textarea' === $type ) {
				echo self::field_type_textarea( $args ); //phpcs:ignore
			}
			if ( 'hidden' === $type ) {
				echo self::field_type_hidden( $args ); //phpcs:ignore
			}
			if ( 'number' === $type ) {
				echo self::field_type_number( $args ); //phpcs:ignore
			}
			if ( 'select' === $type ) {
				echo self::field_type_select( $args ); //phpcs:ignore
			}
			if ( 'radio' === $type ) {
				echo self::field_type_radio( $args ); //phpcs:ignore
			}
			if ( 'checkbox' === $type ) {
				echo self::field_type_checkbox( $args ); //phpcs:ignore
			}
			if ( 'wp_editor' === $type ) {
				wp_editor( $args['value'], $args['id'], $args['settings'] );
			}
			if ( 'color_picker' === $type ) {
				echo self::field_type_color_picker( $args ); //phpcs:ignore
			}
			if ( 'radio_image' === $type ) {
				echo self::field_type_radio_image( $args ); //phpcs:ignore
			}
		}
	}
	/**
	 * Fix the empty array.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the type, name, etc as arguments.
	 */
	public static function fixed_empty_array( $args ) {
		$default = array(
			'type'        => '',
			'id'          => '',
			'class'       => '',
			'name'        => '',
			'value'       => '',
			'option'      => '',
			'default'     => '',
			'placeholder' => '',
			'required'    => '',
			'min'         => '',
			'max'         => '',
			'selected'    => '',
			'rows'        => '',
			'cols'        => '',
			'style'       => '',
			'settings'    => '',
			'attr'        => '',
			'width'       => '',
			'height'      => '',
		);
		return array_merge( $default, $args );
	}
	/**
	 * Retrieve the atributs.
	 *
	 * @since    1.0.0
	 * @param    array $args Stores the key-value pair.
	 */
	public static function get_attr( $args ) {
		if ( ! empty( $args ['attr'] ) ) {
			$html = '';
			foreach ( $args ['attr']  as $key => $value ) {
				$html .= ' ' . $key . '="' . $value . '"';
			}
			return $html;
		}
	}
	/**
	 * Field Type is Color Picker.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_color_picker( $args ) {
		$class = '';
		$html  = '<input type="text"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$class = $args ['class'];
		}
			$html .= ' class="wp_color_picker ' . $class . '"';
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Text.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_text( $args ) {
		$html = '<input type="text"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Date.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_date( $args ) {
		$html = '<input type="date"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="field_type_date ' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Time.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_time( $args ) {
		$html = '<input type="text"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="field_type_time ' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Email.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_email( $args ) {
		$html = '<input type="email"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Textarea.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_textarea( $args ) {
		$html = '<textarea ';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['style'] ) ) {
			$html .= ' style="' . $args ['style'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['rows'] ) ) {
			$html .= ' rows="' . $args ['rows'] . '"';
		}
		if ( ! empty( $args ['cols'] ) ) {
			$html .= ' cols="' . $args ['cols'] . '"';
		}
		if ( ! empty( $args ['max'] ) ) {
			$html .= ' maxlength="' . $args ['max'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		if ( ! empty( $args ['value'] ) ) {
			$html .= $args ['value'];
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= $args ['default'];
			}
		}
		$html .= '</textarea>';
		return $html;
	}
	/**
	 * Field Type is Hidden.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_hidden( $args ) {
		$html = '<input type="hidden"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Number.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_number( $args ) {
		$html = '<input type="number"';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'] . '"';
		}
		if ( ! empty( $args ['value'] ) ) {
			$html .= ' value="' . $args ['value'] . '"';
		} else {
			if ( ! empty( $args ['default'] ) ) {
				$html .= ' value="' . $args ['default'] . '"';
			}
		}
		if ( ! empty( $args ['placeholder'] ) ) {
			$html .= ' placeholder="' . $args ['placeholder'] . '"';
		}
		if ( ! empty( $args ['min'] ) ) {
			$html .= ' min="' . $args ['min'] . '"';
		}
		if ( ! empty( $args ['max'] ) ) {
			$html .= ' max="' . $args ['max'] . '"';
		}
		$html .= self::get_attr( $args ); //phpcs:ignore
		if ( ! empty( $args ['required'] ) ) {
			$html .= ' required';
		}
		$html .= '>';
		return $html;
	}
	/**
	 * Field Type is Checkbox.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_checkbox( $args ) {
		$html = '';
		if ( ! empty( $args['option'] ) ) {
			$svalue = ! empty( $args['value'] ) ? maybe_unserialize( $args['value'] ) : '';
			$html  .= '<span class="aior_checkbox">';
			foreach ( $args['option'] as $key => $value ) {
				$html .= '<label><input type="checkbox" ';
				if ( ! empty( $args ['id'] ) ) {
					$html .= ' id="' . $args ['id'] . '"';
				}
				if ( ! empty( $args ['class'] ) ) {
					$html .= ' class="' . $args ['class'] . '"';
				}
				if ( ! empty( $args ['name'] ) ) {
					$html .= ' name="' . $args ['name'] . '[]"';
				}
				$fvalue = array_key_exists( 'v', $args['option'] ) ? $value['v'] : $value;
				$html  .= 'value="' . $fvalue . '" ';
				if ( ! empty( $svalue ) ) {
					foreach ( $svalue as $k => $v ) {
						if ( $v == $value ) {
							$html .= 'checked';
						}
					}
				} else {
					if ( $value == $args['selected'] ) {
						$html .= 'checked';
					} elseif ( $value == $args['default'] ) {
						$html .= 'checked';
					}
				}
				$fkey  = array_key_exists( 'k', $args['option'] ) ? $value['k'] : $key;
				$html .= '>' . $fkey . '</label>';
			}
			$html .= '</span>';
		} else {
			$html .= '<label><input type="checkbox" ';
			if ( ! empty( $args ['id'] ) ) {
				$html .= ' id="' . $args ['id'] . '"';
			}
			if ( ! empty( $args ['class'] ) ) {
				$html .= ' class="' . $args ['class'] . '"';
			}
			if ( ! empty( $args ['name'] ) ) {
				$html .= ' name="' . $args ['name'] . '"';
			}
			if ( ! empty( $args ['value'] ) ) {
				$html .= ' value="' . $args ['value'] . '" ';
			} else {
				if ( ! empty( $args ['default'] ) ) {
					$html .= ' value="' . $args ['default'] . '" ';
				}
			}
			if ( ! empty( $args['selected'] ) ) {
				$html .= 'checked';
			}
			$html .= '></label>';

		}
		return $html;
	}
	/**
	 * Field Type is Radio.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_radio( $args ) {
		$html = '';
		if ( ! empty( $args['option'] ) ) {
			foreach ( $args['option'] as $key => $value ) {
				$html .= '<label><input type="radio" ';
				if ( ! empty( $args ['id'] ) ) {
					$html .= ' id="' . $args ['id'] . '"';
				}
				if ( ! empty( $args ['class'] ) ) {
					$html .= ' class="' . $args ['class'] . '"';
				}
				if ( ! empty( $args ['name'] ) ) {
					$html .= ' name="' . $args ['name'] . '"';
				}
				$html  .= 'value="' . $value . '" ';
				$svalue = ! empty( $args['value'] ) ? $args['value'] : '';
				if ( ! empty( $svalue ) ) {
					if ( $svalue == $value ) {
						$html .= 'checked';
					}
				} else {
					if ( $value == $args['selected'] ) {
						$html .= 'checked';
					} elseif ( $value == $args['default'] ) {
						$html .= 'checked';
					}
				}

				$html .= '>' . $key . ' </label>';
			}
		} else {
			$html .= '<label><input type="radio" ';
			if ( ! empty( $args ['id'] ) ) {
				$html .= ' id="' . $args ['id'] . '"';
			}
			if ( ! empty( $args ['class'] ) ) {
				$html .= ' class="' . $args ['class'] . '"';
			}
			if ( ! empty( $args ['name'] ) ) {
				$html .= ' name="' . $args ['name'] . '"';
			}
			if ( ! empty( $args ['value'] ) ) {
				$html .= ' value="' . $args ['value'] . '" ';
			} else {
				if ( ! empty( $args ['default'] ) ) {
					$html .= ' value="' . $args ['default'] . '" ';
				}
			}
			if ( ! empty( $args['selected'] ) ) {
				$html .= 'checked';
			}
			$html .= '></label>';
		}
		return $html;
	}
	/**
	 * Field Type is Select.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_select( $args ) {
		$html = '<select';
		if ( ! empty( $args ['id'] ) ) {
			$html .= ' id="' . $args ['id'] . '"';
		}
		if ( ! empty( $args ['class'] ) ) {
			$html .= ' class="' . $args ['class'] . '"';
		}
		if ( ! empty( $args ['name'] ) ) {
			$html .= ' name="' . $args ['name'];
		}
		$html .= '">';
		if ( ! empty( $args['option'] ) ) {
			foreach ( $args['option'] as $key => $value ) {
				$fvalue = array_key_exists( 'v', $args['option'] ) ? $value['v'] : $value;
				$html  .= '<option value="' . $fvalue . '" ';
				if ( $value == $args['value'] ) {
					$html .= 'selected';
				}
				$fkey  = array_key_exists( 'k', $args['option'] ) ? $value['k'] : $key;
				$html .= '>' . $fkey . '</option>';
			}
		}
		$html .= '</select>';
		return $html;

	}
	/**
	 * Field Type is Radio with image.
	 *
	 * @since    1.0.0
	 * @param    array $args       Passes the Whole HTML with attributes.
	 */
	public static function field_type_radio_image( $args ) {
		$html = '';
		if ( ! empty( $args['option'] ) ) {
			$html .= '<ul ';
			if ( ! empty( $args ['id'] ) ) {
				$html .= ' id="' . $args ['id'] . '"';
			}
			$html .= ' class="field_radio_image">';
			foreach ( $args['option'] as $key => $value ) {
				$html .= '<li ';
				if ( $key == $args['value'] ) {
					$html .= ' class="active"';
				}
				$html .= '>';
				$html .= '<a class="radio_image">';
				$html .= '<img src="' . esc_url( $value['thumb'] ) . '" ';
				if ( ! empty( $args ['wdith'] ) ) {
					$html .= ' wdith="' . $args ['wdith'] . '" ';
				}
				if ( ! empty( $args ['height'] ) ) {
					$html .= ' height="' . $args ['height'] . '" ';
				}
				$html .= '>';

				$html .= '<input type="radio" ';
				$html .= ' class="' . $args ['class'] . '"';
				if ( ! empty( $args ['name'] ) ) {
					$html .= ' name="' . $args ['name'] . '"';
				}
				$html  .= 'value="' . $key . '" ';
				$svalue = ! empty( $args['value'] ) ? $args['value'] : '';
				if ( ! empty( $svalue ) ) {
					if ( $svalue == $key ) {
						$html .= 'checked';
					}
				} else {
					if ( $value == $args['selected'] ) {
						$html .= 'checked';
					} elseif ( $value == $args['default'] ) {
						$html .= 'checked';
					}
				}
				$html .= '>';

				$html .= '</a>';
				$html .= '<b>' . esc_html( $value['title'] ) . '</b>';

				$html .= '</li>';
			}
			$html .= '</ul>';
		}
		return $html;
	}

}
