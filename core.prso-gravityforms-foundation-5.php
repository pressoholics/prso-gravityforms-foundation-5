<?php
/**
* Prso Theme Gravity Forms Customization
*
* This include calls a number of Gravity Forms filters and actions
* to change how form elements are rendered to better fit with the 
* Zurb Foundation template.
*
* Hooks:
* 	Filter:: 'prso_theme_gform_validation_wrapper_class' 	- change class for div around field validation message
*	Filter:: 'prso_theme_gform_form_confirmation_class' 	- change class for div around form confirmation message
*	Filter:: 'prso_theme_gform_form_validation_class' 		- change class for div around form validation message
*	Filter:: 'prso_theme_gforms_address_city_class' 		- change class for div around city field
*	Filter:: 'prso_theme_gforms_address_state_class' 		- change class for div around state field
*	Filter:: 'prso_theme_gforms_address_zip_class' 			- change class for div around zip field
*	Filter:: 'prso_theme_gforms_website_class' 				- change class for div around website field
*	Filter:: 'prso_theme_gforms_website_placeholder' 		- change the placeholder value for website input field
*	Filter:: 'prso_theme_gforms_website_field_class' 		- change class for website field input
*	Filter:: 'prso_theme_gforms_name_class' 				- change class for div around name fields
*	Filter:: 'prso_theme_gforms_name_field_class' 			- change class for name field inputs
*
*
*/

/**
* prso_theme_gform_dequeue_scripts
* 
* Called by Gravity Forms 'gform_enqueue_scripts' filter
* Dequeue any gforms scripts or styles here 
*
* @access 	public
* @author	Ben Moody
*/
add_action( 'init', 'prso_theme_gform_dequeue_scripts' );
function prso_theme_gform_dequeue_scripts() {
	
	if( !get_option('rg_gforms_disable_css') ) {
		update_option( 'rg_gforms_disable_css', TRUE );
	}
	
}

/**
* prso_theme_gform_form_validation_message
* 
* Called by Gravity Forms 'gform_validation_message' filter
* Allows changes to the form validation message div. 
*
* Hooks:
*	Filter:: 'prso_theme_gform_form_validation_class' - change class for div around form validation message
*
* @param	string		validation_message
* @param	array		form
* @return	string		validation_message
* @access 	public
* @author	Ben Moody
*/
add_filter( 'gform_validation_message', 'prso_theme_gform_form_validation_message', 10, 2 );
function prso_theme_gform_form_validation_message( $validation_message, $form ) {
	
	//Init vars
	$form_validation_msg_classes = 'alert-box alert';
	
	if( !empty($validation_message) ) {
		
		//Filter classes for element
		$form_validation_msg_classes = apply_filters( 'prso_theme_gform_form_validation_class', $form_validation_msg_classes, $form );
		
		//Add zurb foundation alert class to validation error div
		$validation_message = preg_replace("/validation_error/", "{$form_validation_msg_classes} validation_error", $validation_message);
		
	}
	
	return $validation_message;
}

/**
* prso_theme_gform_form_confirmation_message
* 
* Called by Gravity Forms 'gform_confirmation' filter
* Allows changes to the form confirmation message div. 
*
* Hooks:
*	Filter:: 'prso_theme_gform_form_confirmation_class' - change class for div around form confirmation message
*
* @param	string		confirmation
* @param	array		form
* @param	array		lead
* @param	bool		ajax
* @return	string		confirmation_message
* @access 	public
* @author	Ben Moody
*/
add_filter( 'gform_confirmation', 'prso_theme_gform_form_confirmation_message', 10, 4 );
function prso_theme_gform_form_confirmation_message( $confirmation, $form, $lead, $ajax ) {
	
	//Init vars
	$form_confirmation_msg_classes = 'alert-box success';
	
	if( !empty($confirmation) && isset($form['id']) ) {
		
		//Filter classes for element
		$form_confirmation_msg_classes = apply_filters( 'prso_theme_gform_form_confirmation_class', $form_confirmation_msg_classes, $form );
		
		//Add zurb foundation alert class to validation error div
		$confirmation = preg_replace("/gform_confirmation_message_{$form['id']}/", "{$form_confirmation_msg_classes} gform_confirmation_message_{$form['id']}", $confirmation);
		
	}
	
	return $confirmation;
}

/**
* prso_theme_gform_field_content
* 
* Called by Gravity Forms 'gform_field_content' filter
* If NOT admin area, detects field type and then conducts required actions
* to re-create the field's html content.
*
* NOTES: This functions uses almost the same code as Gravity Forms to create
* the field content. This approach was required as Gravity Forms does not yet
* have sufficient filters to allow changes to how fields and their labels are
* rendered. To ensure that the fields meet Zurb Foundation layout we needed to
* tweak the specific order of elements in the DOM.
*
* Although this avoids having to edit the Gravity Forms code base directly
* this still may break in the future if gforms change how their fields are rendered. 
*
* Hooks:
*	Filter:: 'prso_theme_gform_validation_wrapper_class' - change class for div around field validation message
*
* @param	string		content
* @param	array		field
* @param	string		value
* @param	lead_id		int
* @param	form_id		int
* @return	string		content
* @access 	public
* @author	Ben Moody
*/
add_filter( 'gform_field_content', 'prso_theme_gform_field_content', 10, 5 );
function prso_theme_gform_field_content( $content, $field, $value, $lead_id, $form_id ) {
	
	$force_frontend_label = false;
	
	if( !is_admin() ) {
		
		//Change html content for text input and address fields
		if( ($field['type'] === 'text') || ($field['type'] === 'address') || ($field['type'] === 'name') || ($field['type'] === 'website') ) {
			
			$id = $field["id"];
			
			//Cache validation message html
			ob_start();
			?>
			<div class='row'>
				<div class='<?php echo apply_filters( 'prso_theme_gform_validation_wrapper_class', 'large-12 columns', $field, $form_id ); ?>'>
					<small class='error gfield_description validation_message'>%s</small>
				</div>
			</div>
			<?php
			$validation_message = ob_get_contents();
			ob_end_clean();
			
			$validation_message = (rgget("failed_validation", $field) && !empty($field["validation_message"])) ? sprintf($validation_message, $field["validation_message"]) : "";
			
			
			$field_label = $force_frontend_label ? $field["label"] : GFCommon::get_label($field);
	        if(rgar($field, "inputType") == "singleproduct" && !rgempty($field["id"] . ".1", $value))
	            $field_label = rgar($value, $field["id"] . ".1");
			
			$field_id = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";
			
			$target_input_id = "";
			
			$required_div = IS_ADMIN || rgar($field, "isRequired") ? sprintf("<span class='gfield_required'>%s</span>", $field["isRequired"] ? "*" : "") : "";
			
			$is_description_above = rgar($field, "descriptionPlacement") == "above";
			
			
			$admin_buttons = IS_ADMIN ? "<div class='gfield_admin_icons'><div class='gfield_admin_header_title'>{$field_type_title} : " . __("Field ID", "gravityforms") . " {$field["id"]}</div>" . $delete_field_link . $duplicate_field_link . "<a class='field_edit_icon edit_icon_collapsed' title='" . __("click to edit this field", "gravityforms") . "'>" . __("Edit", "gravityforms") . "</a></div>" : "";
			
			if(empty($target_input_id))
	        	$target_input_id = $field_id;
	        	
	        
	        //Field Description
	        $description = '';
	        
	        if($is_description_above)
	            $field_content = sprintf("%s<label class='gfield_label' for='%s'>%s%s</label>%s{FIELD}%s", $admin_buttons, $target_input_id, esc_html($field_label), $required_div , $description, $validation_message);
	        else
	            $field_content = sprintf("%s<label class='gfield_label' for='%s'>%s%s</label>{FIELD}%s%s", $admin_buttons, $target_input_id, esc_html($field_label), $required_div , $description, $validation_message);
	            
			
			//Detect if field type is text or address and call the required function to get field content
			if( $field['type'] === 'address' ) {
			
				$content = str_replace("{FIELD}", prso_theme_gform_get_address_field($field, $value, 0, $form_id), $field_content);
				
			} elseif ( $field['type'] === 'name' ) {
				
				$content = str_replace("{FIELD}", prso_theme_gform_get_name_field($field, $value, 0, $form_id), $field_content);
				
			} elseif ( $field['type'] === 'website' ) {
				
				$content = str_replace("{FIELD}", prso_theme_gform_get_website_field($field, $value, 0, $form_id), $field_content);
				
			} else {
				$content = str_replace("{FIELD}", GFCommon::get_field_input($field, $value, 0, $form_id), $field_content);
			}
	       
		}
		
	}
	
	return $content;
}

/**
* prso_theme_gform_get_website_field
* 
* Called by 'prso_theme_gform_get_website_field' in prso_theme_gform_field_content()
*
* Simply rebuilds name field to use a two column layout for first and last name so inputs sit sideby side. 
*
* Hooks:
*	Filter:: 'prso_theme_gforms_website_class' 			- change class for div around website field
*	Filter:: 'prso_theme_gforms_website_placeholder' 	- change the placeholder value for website input field
*	Filter:: 'prso_theme_gforms_website_field_class' 	- change class for website field input
*
* @param	array		field
* @param	string		value
* @param	lead_id		int
* @param	form_id		int
* @return	string		name fields html
* @access 	public
* @author	Ben Moody
*/
function prso_theme_gform_get_website_field( $field, $value, $lead_id, $form_id ) {
	
	//Init vars
	$output = NULL;
	
	//Cache css id
	$input_id = str_replace('.', '_', $field['id']);
	
	ob_start();
	?>
	<div id="input_<?php esc_attr_e( $input_id ); ?>_container" class="<?php echo apply_filters( 'prso_theme_gforms_website_class', 'row collapse', $field, $form_id ); ?>">
		<div class="small-3 large-2 columns">
			<span class="prefix">http://</span>
		</div>
		<div class="small-9 large-10 columns">
			<input id="input_<?php esc_attr_e( $input_id ); ?>" type="text" placeholder="<?php echo apply_filters( 'prso_theme_gforms_website_placeholder', 'Enter your URL...', $field, $form_id ); ?>" tabindex="<?php esc_attr_e( $field['id'] ); ?>" name="input_<?php esc_attr_e($input['id']); ?>" class="<?php echo apply_filters( 'prso_theme_gforms_website_field_class', 'placeholder', $field, $form_id ); ?>">
		</div>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	
	return $output;
	
}

/**
* prso_theme_gform_get_name_field
* 
* Called by 'prso_theme_gform_get_name_field' in prso_theme_gform_field_content()
*
* Simply rebuilds name field to use a two column layout for first and last name so inputs sit sideby side. 
*
* Hooks:
*	Filter:: 'prso_theme_gforms_name_class' 		- change class for div around name fields
*	Filter:: 'prso_theme_gforms_name_field_class' 	- change class for name field inputs
*
* @param	array		field
* @param	string		value
* @param	lead_id		int
* @param	form_id		int
* @return	string		name fields html
* @access 	public
* @author	Ben Moody
*/
function prso_theme_gform_get_name_field( $field, $value, $lead_id, $form_id ) {
	
	//Init vars
	$output = NULL;
	
	ob_start();
	?>
	<div class="row">
		<?php foreach( $field['inputs'] as $key => $input ):
			//Cache css id
			$input_id = str_replace('.', '_', $input['id']);
		?>
		<div id="input_<?php esc_attr_e( $input_id ); ?>_container" class="<?php echo apply_filters( 'prso_theme_gforms_name_class', 'large-6 columns', $field, $form_id, $input ); ?>">
			<input id="input_<?php esc_attr_e( $input_id ); ?>" type="text" tabindex="<?php esc_attr_e( $field['id'] ); ?>" name="input_<?php esc_attr_e($input['id']); ?>" placeholder="<?php esc_attr_e( $input['label'] ); ?>" class="<?php echo apply_filters( 'prso_theme_gforms_name_field_class', 'placeholder', $field, $form_id, $input ); ?>" />
		</div>
		<?php endforeach; ?>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	
	return $output;
}

/**
* prso_theme_gform_get_address_field
* 
* Called by 'prso_theme_gform_get_address_field' function in this document
* Essentially a copy and past of core code from Gravity Forms, allowing us
* to alter how each address field is rendered and laid out.
*
* NOTES: This functions uses almost the same code as Gravity Forms to create
* the field content. This approach was required as Gravity Forms does not yet
* have sufficient filters to allow changes to how fields and their labels are
* rendered. To ensure that the fields meet Zurb Foundation layout we needed to
* tweak the specific order of elements in the DOM.
*
* Although this avoids having to edit the Gravity Forms code base directly
* this still may break in the future if gforms change how their fields are rendered. 
*
* Hooks:
*	Filter:: 'prso_theme_gforms_address_city_class' 	- change class for div around city field
*	Filter:: 'prso_theme_gforms_address_state_class' 	- change class for div around state field
*	Filter:: 'prso_theme_gforms_address_zip_class' 		- change class for div around zip field
*
* @param	array		field
* @param	string		value
* @param	lead_id		int
* @param	form_id		int
* @return	string		address fields html
* @access 	public
* @author	Ben Moody
*/
function prso_theme_gform_get_address_field( $field, $value, $lead_id, $form_id ) {
	
	//Cache foudantion div to start a row
	$div_row = "<div class='row'>";
	
	$id = $field["id"];
    $field_id = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";
    $form_id = IS_ADMIN && empty($form_id) ? rgget("id") : $form_id;

    $size = rgar($field, "size");
    $disabled_text = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
    $class_suffix = RG_CURRENT_VIEW == "entry" ? "_admin" : "";
    $class = $size . $class_suffix;

    $currency = "";
    if(RG_CURRENT_VIEW == "entry"){
        $lead = RGFormsModel::get_lead($lead_id);
        $post_id = $lead["post_id"];
        $post_link = "";
        if(is_numeric($post_id) && self::is_post_field($field)){
            $post_link = "You can <a href='post.php?action=edit&post=$post_id'>edit this post</a> from the post page.";
        }
        $currency = $lead["currency"];
    }
	
	$street_value ="";
    $street2_value ="";
    $city_value ="";
    $state_value ="";
    $zip_value ="";
    $country_value ="";
    
    $class_suffix = '';
    
    if(is_array($value)){
        $street_value = esc_attr(rgget($field["id"] . ".1",$value));
        $street2_value = esc_attr(rgget($field["id"] . ".2",$value));
        $city_value = esc_attr(rgget($field["id"] . ".3",$value));
        $state_value = esc_attr(rgget($field["id"] . ".4",$value));
        $zip_value = esc_attr(rgget($field["id"] . ".5",$value));
        $country_value = esc_attr(rgget($field["id"] . ".6",$value));
    }

    $address_types = GFCommon::get_address_types($form_id);
    $addr_type = empty($field["addressType"]) ? "international" : $field["addressType"];
    $address_type = $address_types[$addr_type];

    $state_label = empty($address_type["state_label"]) ? __("State", "gravityforms") : $address_type["state_label"];
    $zip_label = empty($address_type["zip_label"]) ? __("Zip Code", "gravityforms") : $address_type["zip_label"];
    $hide_country = !empty($address_type["country"]) || rgget("hideCountry", $field);

    if(empty($country_value))
        $country_value = rgget("defaultCountry", $field);

    if(empty($state_value))
        $state_value = rgget("defaultState", $field);

    $country_list = GFCommon::get_country_dropdown($country_value);

    //changing css classes based on field format to ensure proper display
    $address_display_format = apply_filters("gform_address_display_format", "default");
    $city_location = $address_display_format == "zip_before_city" ? "right" : "left";
    $zip_location = $address_display_format != "zip_before_city" && rgar($field,"hideState") ? "right" : "left";
    $state_location = $address_display_format == "zip_before_city" ? "left" : "right";
    $country_location = rgar($field,"hideState") ? "left" : "right";

    //address field
    $tabindex = GFCommon::get_tabindex();  
    $street_address = sprintf("<span class='ginput_full$class_suffix' id='" . $field_id . "_1_container'><input type='text' name='input_%d.1' id='%s_1' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_street_{$form_id}", apply_filters("gform_address_street",__("Street Address", "gravityforms"), $form_id), $form_id) ."'/></span>", $id, $field_id, $street_value, $disabled_text, $field_id);

    //address line 2 field
    $street_address2 = "";
    $style = (IS_ADMIN && rgget("hideAddress2", $field)) ? "style='display:none;'" : "";
    if(IS_ADMIN || !rgget("hideAddress2", $field)){
        $tabindex = GFCommon::get_tabindex();
        $street_address2 = sprintf("<span class='ginput_full$class_suffix' id='" . $field_id . "_2_container' $style><input type='text' name='input_%d.2' id='%s_2' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_street2_{$form_id}",apply_filters("gform_address_street2",__("Address Line 2", "gravityforms"), $form_id), $form_id) ."'/></span>", $id, $field_id, $street2_value, $disabled_text, $field_id);
    }

    if($address_display_format == "zip_before_city"){
        //zip field
        $tabindex = GFCommon::get_tabindex();
        $zip = sprintf("<span class='ginput_{$zip_location}$class_suffix' id='" . $field_id . "_5_container'><input type='text' name='input_%d.5' id='%s_5' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_zip_{$form_id}", apply_filters("gform_address_zip", $zip_label, $form_id), $form_id) ."'/></span>", $id, $field_id, $zip_value, $disabled_text, $field_id);

        //city field
        $tabindex = GFCommon::get_tabindex();
        $city = sprintf("<span class='ginput_{$city_location}$class_suffix' id='" . $field_id . "_3_container'><input type='text' name='input_%d.3' id='%s_3' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_city_{$form_id}", apply_filters("gform_address_city",__("City", "gravityforms"), $form_id), $form_id) ."'/></span>", $id, $field_id, $city_value, $disabled_text, $field_id);

        //state field
        $style = (IS_ADMIN && rgget("hideState", $field)) ? "style='display:none;'" : "";
        if(IS_ADMIN || !rgget("hideState", $field)){
            $state_field = GFCommon::get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id);
            $state = sprintf("<span class='ginput_{$state_location}$class_suffix' id='" . $field_id . "_4_container' $style>$state_field<label for='%s_4' id='" . $field_id . "_4_label'>" . apply_filters("gform_address_state_{$form_id}", apply_filters("gform_address_state", $state_label, $form_id), $form_id) . "</label></span>", $field_id);
        }
        else{
            $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
        }
    }
    else{

        //city field
        $tabindex = GFCommon::get_tabindex();
        $city = sprintf("<span class='ginput_{$city_location}$class_suffix' id='" . $field_id . "_3_container'><input type='text' name='input_%d.3' id='%s_3' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_city_{$form_id}", apply_filters("gform_address_city",__("City", "gravityforms"), $form_id), $form_id) ."'/></span>", $id, $field_id, $city_value, $disabled_text, $field_id);

        //state field
        $style = (IS_ADMIN && rgget("hideState", $field)) ? "style='display:none;'" : "";
        if(IS_ADMIN || !rgget("hideState", $field)){
            $state_field = prso_theme_gform_get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id, $state_label);
           
            $state = sprintf("<span class='ginput_{$state_location}$class_suffix' id='" . $field_id . "_4_container' $style>$state_field</span>", $field_id);
        }
        else{
            $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
        }

        //zip field
        $tabindex = GFCommon::get_tabindex();
        $zip = sprintf("<span class='ginput_{$zip_location}$class_suffix' id='" . $field_id . "_5_container'><input type='text' name='input_%d.5' id='%s_5' value='%s' $tabindex %s placeholder='". apply_filters("gform_address_zip_{$form_id}", apply_filters("gform_address_zip", $zip_label, $form_id), $form_id) ."'/></span>", $id, $field_id, $zip_value, $disabled_text, $field_id);

    }

    if(IS_ADMIN || !$hide_country){
        $style = $hide_country ? "style='display:none;'" : "";
        $tabindex = GFCommon::get_tabindex();
        $country = sprintf("<span class='ginput_{$country_location}$class_suffix' id='" . $field_id . "_6_container' $style><label for='%s_6' id='" . $field_id . "_6_label'>" . apply_filters("gform_address_country_{$form_id}", apply_filters("gform_address_country",__("Country", "gravityforms"), $form_id), $form_id) . "</label><select name='input_%d.6' id='%s_6' $tabindex %s>%s</select></span>", $id, $field_id, $disabled_text, $country_list, $field_id);
    }
    else{
        $country = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.6' id='%s_6' value='%s'/>", $id, $field_id, $country_value);
    }
    
    //Wrap city in foundation divs
    $city = "<div class='". apply_filters( 'prso_theme_gforms_address_city_class', 'large-5 columns', $field, $form_id ) ."'>{$city}</div>";
    
    //Wrap state in foundation divs
    $state = "<div class='". apply_filters( 'prso_theme_gforms_address_state_class', 'large-4 columns', $field, $form_id ) ."'>{$state}</div>";
    
    //Wrap ZIP in foundation divs
    $zip = "<div class='". apply_filters( 'prso_theme_gforms_address_zip_class', 'large-3 columns', $field, $form_id ) ."'>{$zip}</div>";
    
    $inputs = $address_display_format == "zip_before_city" ? $street_address . $street_address2 . $div_row . $zip . $city . $state . "</div>" . $country : $street_address . $street_address2 . $div_row . $city . $state . $zip . "</div>" . $country;

    return "<div class='ginput_complex$class_suffix ginput_container' id='$field_id'>" . $inputs . "</div>";
	
}

/**
* prso_theme_gform_get_state_field
* 
* Called by 'prso_theme_gform_get_address_field' function in this document
* Essentially a copy and past of core code from Gravity Forms.
*
* Main reason this was copied out of gforms was the fact the original method is private
*
* NOTES: This functions uses almost the same code as Gravity Forms to create
* the field content. This approach was required as Gravity Forms does not yet
* have sufficient filters to allow changes to how fields and their labels are
* rendered. To ensure that the fields meet Zurb Foundation layout we needed to
* tweak the specific order of elements in the DOM.
*
* Although this avoids having to edit the Gravity Forms code base directly
* this still may break in the future if gforms change how their fields are rendered. 
*
*
* @access 	public
* @author	Ben Moody
*/
function prso_theme_gform_get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id, $state_label){

    $state_dropdown_class = $state_text_class = $state_style = $text_style = $state_field_id = "";

    if(empty($state_value)){
        $state_value = rgget("defaultState", $field);

        //for backwards compatibility (canadian address type used to store the default state into the defaultProvince property)
        if (rgget("addressType", $field) == "canadian" && !rgempty("defaultProvince", $field))
            $state_value = $field["defaultProvince"];
    }

    $address_type = rgempty("addressType", $field) ? "international" : $field["addressType"];
    $address_types = GFCommon::get_address_types($form_id);
    $has_state_drop_down = isset($address_types[$address_type]["states"]) && is_array($address_types[$address_type]["states"]);

    if(IS_ADMIN && RG_CURRENT_VIEW != "entry"){
        $state_dropdown_class = "class='state_dropdown'";
        $state_text_class = "class='state_text'";
        $state_style = !$has_state_drop_down ? "style='display:none;'" : "";
        $text_style = $has_state_drop_down  ? "style='display:none;'" : "";
        $state_field_id = "";
    }
    else{
        //id only displayed on front end
        $state_field_id = "id='" . $field_id . "_4'";
    }

    $tabindex = GFCommon::get_tabindex();
    $states = empty($address_types[$address_type]["states"]) ? array() : $address_types[$address_type]["states"];
    $state_dropdown = sprintf("<select name='input_%d.4' %s $tabindex %s $state_dropdown_class $state_style>%s</select>", $id, $state_field_id, $disabled_text, GFCommon::get_state_dropdown($states, $state_value));

    $tabindex = GFCommon::get_tabindex();
    $state_text = sprintf("<input type='text' name='input_%d.4' %s value='%s' $tabindex %s $state_text_class $text_style placeholder='". apply_filters("gform_address_state_{$form_id}", apply_filters("gform_address_state", $state_label, $form_id), $form_id) ."'/>", $id, $state_field_id, $state_value, $disabled_text);

    if(IS_ADMIN && RG_CURRENT_VIEW != "entry")
        return $state_dropdown . $state_text;
    else if($has_state_drop_down)
        return $state_dropdown;
    else
        return $state_text;
        
}

/**
* prso_theme_gform_field_choices
* 
* Called by Gravity Forms 'gform_field_choices' filter
* Essentially a copy and past of core code from Gravity Forms, allowing us
* to wrap the field input and span elements within the field's label.
*
* This is required to match with how Zurb Foundation renders checkboxes, radio buttons, ect
* allowing the use of Foudnation's 'custom inputs' js based styling.
*
* NOTES: This functions uses almost the same code as Gravity Forms to create
* the field content. This approach was required as Gravity Forms does not yet
* have sufficient filters to allow changes to how fields and their labels are
* rendered. To ensure that the fields meet Zurb Foundation layout we needed to
* tweak the specific order of elements in the DOM.
*
* Although this avoids having to edit the Gravity Forms code base directly
* this still may break in the future if gforms change how their fields are rendered. 
*
* @param	string		choices	- field html
* @param	field		array
* @access 	public
* @author	Ben Moody
*/
add_filter("gform_field_choices", "prso_theme_gform_field_choices", 10, 2);
function prso_theme_gform_field_choices( $choices, $field ) {
	
	//Init vars
	$type		= 'radio';
	$choices 	= NULL;
	
	if( !is_admin() ) {
		
		//Change html content for text input and address fields
		if( ($field['type'] === 'radio') || ($field['type'] === 'checkbox') ) {
			
			if( $field['type'] === 'checkbox' ) {
				$type = 'checkbox';
			}
			
			if(is_array($field["choices"])){
		        $choice_id = 0;
		
		        // add "other" choice to choices if enabled
		        if(rgar($field, 'enableOtherChoice')) {
		            $other_default_value = GFCommon::get_other_choice_value();
		            $field["choices"][] = array('text' => $other_default_value, 'value' => 'gf_other_choice', 'isSelected' => false, 'isOtherChoice' => true);
		        }
		
		        //$logic_event = GFCommon::get_logic_event($field, "click");
		        $count = 1;
		
		        foreach($field["choices"] as $choice){
		            $id = $field["id"] . '_' . $choice_id++;
		
		            $field_value = !empty($choice["value"]) || rgar($field, "enableChoiceValue") ? $choice["value"] : $choice["text"];
		
		            if(rgget("enablePrice", $field))
		                $field_value .= "|" . GFCommon::to_number(rgar($choice,"price"));
		
		            if(RG_CURRENT_VIEW != "entry"){
		                $checked = rgar($choice,"isSelected") ? "checked='checked'" : "";
		            }
		            else {
		                $checked = RGFormsModel::choice_value_match($field, $choice, $value) ? "checked='checked'" : "";
		            }
		
		            $tabindex = GFCommon::get_tabindex();
		            $label = sprintf("%s</label>", $choice["text"]);
		            $input_focus = '';
		
		            // handle "other" choice
		            if(rgar($choice, 'isOtherChoice')) {
		
		                $onfocus = !IS_ADMIN ? 'jQuery(this).prev("input").attr("checked", true); if(jQuery(this).val() == "' . $other_default_value . '") { jQuery(this).val(""); }' : '';
		                $onblur = !IS_ADMIN ? 'if(jQuery(this).val().replace(" ", "") == "") { jQuery(this).val("' . $other_default_value . '"); }' : '';
		
		                $input_focus = !IS_ADMIN ? "onfocus=\"jQuery(this).next('input').focus();\"" : "";
		                $value_exists = RGFormsModel::choices_value_match($field, $field["choices"], $value);
		
		                if($value == 'gf_other_choice' && rgpost("input_{$field["id"]}_other")){
		                    $other_value = rgpost("input_{$field["id"]}_other");
		                } else if(!$value_exists && !empty($value)){
		                    $other_value = $value;
		                    $value = 'gf_other_choice';
		                    $checked = "checked='checked'";
		                } else {
		                    $other_value = $other_default_value;
		                }
		                $label = "<input name='input_{$field["id"]}_other' type='text' value='" . esc_attr($other_value) . "' onfocus='$onfocus' onblur='$onblur' $tabindex $disabled_text />";
		            }
		            
		            $choices .= sprintf("<li class='gchoice_$id'><label for='choice_%s'><input name='input_%d' type='{$type}' value='%s' %s id='choice_%s' $tabindex %s />&nbsp;%s</li>", $field["id"], $field["id"], esc_attr($field_value), $checked, $id, $input_focus, $label);
		            
		            if(IS_ADMIN && RG_CURRENT_VIEW != "entry" && $count >=5)
		                break;
		
		            $count++;
		        }
		
		        $total = sizeof($field["choices"]);
		        if($count < $total)
		            $choices .= "<li class='gchoice_total'>" . sprintf(__("%d of %d items shown. Edit field to view all", "gravityforms"), $count, $total) . "</li>";
		    }
			
		}		
		
	}
	
	return $choices;
}