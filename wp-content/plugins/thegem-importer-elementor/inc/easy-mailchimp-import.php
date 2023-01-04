<?php

function thegem_mailchimp_import_forms( $csv_file ) {

    class Thegem_Easy_MailChimp_Extender_Option_Forms extends Yikes_Inc_Easy_MailChimp_Extender_Option_Forms {
        public function create_form( $form_data ) {
            $form_data = yikes_deep_parse_args( $form_data, $this->get_form_defaults() );
            ksort( $form_data );
            $all_forms  = $this->get_all_forms();
            $all_forms[  $form_data['id'] ] = $form_data;
            $result = update_option( $this->option, $all_forms );
            if ( false === $result ) {
                return $result;
            }

            return  $form_data['id'];
        }
    }


    class Thegem_Easy_MailChimp_Extender_Forms extends Yikes_Inc_Easy_MailChimp_Extender_Forms {
        public function create_form( $form_data ) {
            $form_data = yikes_deep_parse_args( $form_data, $this->get_form_defaults() );
            $save_data = $this->prepare_data_for_db( $form_data );
            $formats   = $this->get_format_array( $save_data );

            $result = $this->wpdb->insert(
                $this->prefixed_table_name,
                $save_data,
                $formats
            );

            if ( false === $result ) {
                return $result;
            }

            return $this->wpdb->insert_id;
        }
    }



    // Ensure we're actually able to open the file.
    $file = fopen( $csv_file, 'r' );
    if ( false === $file ) {
        return array(
            'status'=>false,
            'message'=>'There was a problem opening the file after it was uploaded.'
        );
    }

    $row       = 1;
    $titles    = array();
    $interface = tehgem_mailchimp_extender_get_form_interface();
    while ( false !== ( $line = fgetcsv( $file, 10000, ',' ) ) ) {
        // Ensure we have more than one item in the current row, or else look for a new row
        if ( count( $line ) <= 1 ) {
            $row++;
            continue;
        }

        // Check if this is a settings import by confirming the first option is 'yikes-mc-api-key'
        if( $line[0] == 'yikes-mc-api-key' ) {
            $options = fgetcsv($file, 10000, ',');
            $new_settings = array();
            $x = 0;
            // build our new array $key => $value pair
            foreach( $line as $option_name ) {
                $new_settings[$option_name] = $options[$x];
                $x++;
            }
            // update the options in the databse
            foreach( $new_settings as $option_name => $option_value ) {
                update_option( $option_name, $option_value );
            }
        } else { // if it's not, then it's an opt-in forms import
            // If this is the first row, then it should be title data.
            if ( 1 === $row ) {
                $titles = $line;
                $row++;
                continue;
            }

            // Combine the titles with the data from the row.
            $data = array_combine( $titles, $line );

            // Attempt to json_decode the rows that need it.
            foreach ( $data as $key => &$value ) {
                $_value = json_decode( $value, true );
                if ( JSON_ERROR_NONE === json_last_error() ) {
                    $value = $_value;
                }
            }

            // Now store the data.
            $interface->create_form( $data );
        }

        $row++;
    }

    fclose($file);
    return array('status'=>true);
}


function tehgem_mailchimp_extender_get_form_interface() {
    static $interface = null;

    if ( null === $interface ) {
        if ( yikes_inc_easy_mailchimp_extender_use_custom_db() ) {
            global $wpdb;
            $interface = new Thegem_Easy_MailChimp_Extender_Forms( $wpdb );
        } else {
            $interface = new Thegem_Easy_MailChimp_Extender_Option_Forms();
        }
    }

    return $interface;
}






