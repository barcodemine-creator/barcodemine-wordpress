<?php 
require_once get_stylesheet_directory().'/dompdf/autoload.inc.php';
require_once get_stylesheet_directory().'/dompdf/src/Options.php';
use Shuchkin\SimpleXLSX;
use Dompdf\Dompdf;
use Dompdf\options;

function mydecor_child_register_scripts(){
    $parent_style = 'mydecor-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array('font-awesome-5', 'mydecor-reset'), mydecor_get_theme_version() );
    wp_enqueue_style( 'mydecor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        '1.0.2'
    );

    // Register Styles 
    wp_register_script( 
        'barcode-main', 
        get_stylesheet_directory_uri() . '/assets/js/main.js', 
        array( 'jquery' ),
        '1.1.9', 
        true
    );

    // Enqueue Styles 
    wp_enqueue_script( 'barcode-main' );

    // Localize Scripts 
    wp_localize_script(
        'barcode-main',
        'barcodemain',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) 
    );
}
add_action( 'wp_enqueue_scripts', 'mydecor_child_register_scripts', 99 );

function barcodemine_barcode_excel_upload_meta_box($object) {
    $_excel_file_data = get_post_meta( $object->ID, '_excel_file_data', true );
    $_excel_file = get_post_meta( $object->ID, '_excel_file', true );
    $_certificate_file = get_post_meta( $object->ID, '_certificate_file', true );
    $disabled = ! empty ( $_excel_file_data ) ? 'disabled' : '';
    
    wp_nonce_field(basename(__FILE__), "excel-upload-box-nonce");
    ?>
    <style>
        .excel-upload-meta-wrap .excel-upload-label{
            display: block;
            padding-bottom: 0.5em;
            font-weight: 600;
            color: #000;
        }
        .excel-upload-item{
            padding: 1.6em;
            background: #f8f8f8;
            margin-bottom: 15px;
        }
        .excel-upload-inner-label{
            width: 100%;
            display: block;
            padding: 10px 0;
        }

        .excel-upload-meta-wrap .button{
            margin-top: 10px;
        }

        .excel-upload-meta-wrap input, .excel-upload-meta-wrap textarea{
            width: 100%;
        }
        .excel-upload-meta-wrap .currency-symbol, .excel-upload-meta-wrap .price-input {
            display: inline-block;
        }
        .excel-upload-meta-wrap .currency-symbol {
            width: 12%;
            margin: 0;
            border: none;
            float: left;
        }
        .excel-upload-meta-wrap .price-input {
            width: 88%;
            margin: 0;
        }
    </style>
    <?php
    if( empty ( $_excel_file_data ) ){
        ?>
            <div class="excel-upload-meta-wrap">
                <h4>Upload a Barcode Excel File</h4>
                <div class="excel-upload-item">
                    <label class="excel-upload-inner-label">Excel File</label>
                    <input type="file" name="excel_file" value="" <?php echo $disabled; ?> />
                    <button type="submit" class="button save_order button-primary" name="excel_upload_order" value="" <?php echo $disabled; ?>>Upload</button>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="excel-upload-meta-wrap">
                <a href="<?php echo $_excel_file['url']; ?>" class="button button-primary">Download Excel File</a>
                <a href="<?php echo $_certificate_file['url']; ?>" class="button button-primary" download>Download Certificate</a>
            </div>
        <?php
    }
}

function barcodemine_barcode_zip_upload_meta_box( $object ){
    $_zip_file = get_post_meta( $object->ID, '_zip_file', true );
    $zip_disabled = ! empty ( $_zip_file ) ? 'disabled' : '';
    if( empty ( $_zip_file ) ){
        ?>
            <div class="excel-upload-meta-wrap">
                <h4>Upload a Zip File</h4>
                <div class="excel-upload-item">
                    <label class="excel-upload-inner-label">Zip File</label>
                    <input type="file" name="zip_file" value="" <?php echo $zip_disabled; ?> />
                    <button type="submit" class="button save_order button-primary" name="zip_upload_order" value="" <?php echo $zip_disabled; ?>>Upload</button>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="excel-upload-meta-wrap">
                <a href="<?php echo $_zip_file['url']; ?>" class="button button-primary">Download Zip File</a>
            </div>
        <?php
    }
}
function barcodemine_add_order_meta_box(){   
    add_meta_box("excel-upload-meta-box", "Barcode File Upload / Certification Download", "barcodemine_barcode_excel_upload_meta_box", "shop_order", "side", "default", null);
    add_meta_box("zip-upload-meta-box", "Zip File Upload / Zip Download", "barcodemine_barcode_zip_upload_meta_box", "shop_order", "side", "default", null);
}

add_action("add_meta_boxes", "barcodemine_add_order_meta_box");

function barcodemine_save_order_meta_box($post_id, $post, $update){
    // Check for charge item and call charge api if availble charge items 
    if ( isset( $_POST["excel_upload_order"] ) && ! empty( $_FILES["excel_file"] ) ){
        $supported_types = array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/xlsb' );
		$arr_file_type = wp_check_filetype( basename( $_FILES['excel_file']['name'] ) );
		$uploaded_type = $arr_file_type['type'];

		if ( in_array( $uploaded_type, $supported_types ) ) {
            $order = wc_get_order( $post_id );
			$upload = wp_upload_bits($_FILES['excel_file']['name'], null, file_get_contents($_FILES['excel_file']['tmp_name']));
			if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
				wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
			} else {
				update_post_meta( $post_id, '_excel_file', $upload );
                require_once get_stylesheet_directory().'/SimpleXLSX.php';
                // require_once get_stylesheet_directory().'dompdf/autoload.inc.php';

                if ($xlsx = SimpleXLSX::parse($upload['file'])) {

                    $excel_values = $xlsx->rows();
                    array_shift( $excel_values );
                    $excel_values = wp_list_pluck( $excel_values, '0' );
                    update_post_meta( $post_id, '_excel_file_data', $excel_values );
                    // If you don't have the WC_Order object (from a dynamic $order_id)
                    $user_id = get_current_user_id();

                    // Get user data by user id
                    $user = get_userdata( $user_id );

                    $display_name = $user->display_name;

                    // The text for the note
                    $note = __("".$display_name." have uploaded barcode excel file sucessfully!!!");

                    // Add the note
                    $order->add_order_note( $note );
                } else {
                    update_post_meta( $post_id, '_excel_file_data', SimpleXLSX::parseError() );
                }

                $billing_address= $order->get_address('billing');
                $excel_data = get_post_meta( $post_id, '_excel_file_data', true );
                
                $filename = $post_id.'_qr_code.png';
                $uploaddir = wp_upload_dir();
                $uploadfile = $uploaddir['path'] . '/' . $filename;

                $contents= file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='.get_site_url().'/barcode-information-search/?barcode_number='.$excel_data[0].'');
                $savefile = fopen($uploadfile, 'w');
                fwrite($savefile, $contents);
                fclose($savefile);

                $wp_filetype = wp_check_filetype(basename($filename), null );

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => $filename,
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment( $attachment, $uploadfile );

                $imagenew = get_post( $attach_id );
                $fullsizepath = get_attached_file( $imagenew->ID );
                // $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
                // wp_update_attachment_metadata( $attach_id, $attach_data );

                ob_start();
        
                    ?>  
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <link rel="preconnect" href="https://fonts.googleapis.com">
                            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
                            <style type="text/css">
                                * {
                                    box-sizing: border-box;
                                }

                                .cert-container {
                                    width:100%;
                                    display: flex; 
                                    justify-content: center;
                                }

                                body, html, .cert-container {
                                    margin: 0;
                                    padding: 0;
                                }

                                .cert {
                                    width: 100%;
                                    /* padding:5px 30px; */
                                    position: relative; 
                                    z-index: -1;
                                }

                                .cert-bg {
                                    position: absolute; 
                                    left: 0px; 
                                    top: 0; 
                                    z-index: -1;
                                    width: 100%;
                                }

                                .cert-content {
                                    font-family: 'Open Sans', sans-serif;
                                    width:800px;
                                    height:300px; 
                                    padding:85px 60px 20px 60px; 
                                    text-align:center;
                                }

                                .bottom-content {
                                    font-family: 'Open Sans', sans-serif;
                                    width:200px;
                                    height:200px;
                                    margin-left: 610px;
                                    font-size: 19px;
                                    line-height: 26px;
                                    /* background: White; */
                                }

                                h1 {
                                    font-size:44px;
                                }

                                p {
                                    font-size:25px;
                                }

                                small {
                                    font-size: 14px;
                                    line-height: 12px;
                                }

                                .bottom-txt {
                                padding: 12px 5px; 
                                display: flex; 
                                justify-content: space-between;
                                font-size: 16px;
                                }

                                .bottom-txt * {
                                white-space: nowrap !important;
                                }

                                .other-font {
                                    margin-top: 8px;
                                    color: #1d6498;
                                    display: inline-block;
                                    margin-right: 70px;
                                    font-size: 24px;
                                    margin-left: 25px;
                                    line-height: 28px;
                                }
                                .other-font-to {
                                    margin-top: 8px;
                                    color: #1d6498;
                                    display: inline-block;
                                    font-size: 24px;
                                    line-height: 28px;
                                }

                                .other-font-qty{
                                    margin-top: 10px;
                                    display: inline-block;
                                    font-size: 20px;
                                    margin-left: 170px;
                                    line-height: 20px;
                                }
                                .one-line-content{
                                    margin-top: 50px;
                                    display: block;
                                }
                                .ml-215 {
                                margin-left: 215px;
                                }
                                .company-title{
                                    margin-top: 240px;
                                    margin-left: 40px;
                                }
                                .account-name{
                                    margin-top: 80px;
                                }
                                .qr-content {
                                    width: 10%;
                                    display: inline-block;
                                    margin-top: 35px;
                                    margin-left: 25px;
                                }
                                .header-content {
                                    width: 90%;
                                    display: inline-block;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="main">
                            <div class="cert-container print-m-0">
                                <div id="content2" class="cert">
                                    <img
                                    src="<?php echo get_stylesheet_directory_uri(). '/assets/images/certificate.png' ;?>"
                                    class="cert-bg"
                                    alt=""
                                    />
                                    <div class="cert-content">
                                        <div class="qr-content">
                                            <img
                                            src="<?php echo wp_get_attachment_image_url( $attach_id, 'full' ); ?>"
                                            class="qr-code"
                                            alt=""
                                            />
                                        </div>
                                        <div class="header-content">
                                            <h1 class="company-title"><?php echo $billing_address['company']; ?></h1>
                                            <div class="one-line-content">
                                                <div class="other-font-qty">(<?php echo count($excel_data); ?>)</div>
                                                <div class="other-font"><?php echo $excel_data[0]; ?></div>
                                                <div class="other-font-to"><?php echo end( $excel_data ); ?></div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bottom-content">
                                        <div class="multi-line-content">
                                            <div class="account-name">789850</div>
                                            <div class=""><?php echo $billing_address['first_name']; ?> <?php echo $billing_address['last_name']; ?></div>
                                            <div class="">BM<?php echo $post_id; ?></div>
                                            <?php
                                                $date_modified = $order->get_date_created();
                                            ?>
                                            <div class=""><?php echo date('d/m/Y'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </body>
                    </html>

                    <?php
                $content = ob_get_contents();
                ob_end_clean();

                // instantiate and use the dompdf class
                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $dompdf = new Dompdf($options);
                // $dompdf = new Dompdf();
                $dompdf->loadHtml($content);

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'landscape');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                $output = $dompdf->output();
                $certificate_upload = wp_upload_bits( $post_id.'_certificate.pdf', null, $output );
                if ( isset( $certificate_upload['error'] ) && $certificate_upload['error'] != 0 ) {
                    wp_die( 'There was an error uploading your file. The error is: ' . $certificate_upload['error'] );
                } else {
                    update_post_meta( $post_id, '_certificate_file', $certificate_upload );
                }
                
			}
		}
		else {
			wp_die( "The file type that you've uploaded is not a Excel." );
		}
    }

    if ( isset( $_POST["zip_upload_order"] ) && ! empty( $_FILES["zip_file"] ) ){
        $supported_types = array( 'application/zip' );
		$arr_file_type = wp_check_filetype( basename( $_FILES['zip_file']['name'] ) );
        // print_r( $arr_file_type );die;
		$uploaded_type = $arr_file_type['type'];

		if ( in_array( $uploaded_type, $supported_types ) ) {
            $order = wc_get_order( $post_id );
			$upload = wp_upload_bits($_FILES['zip_file']['name'], null, file_get_contents($_FILES['zip_file']['tmp_name']));
			if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
				wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
			} else {
                update_post_meta( $post_id, '_zip_file', $upload );
                // If you don't have the WC_Order object (from a dynamic $order_id)
                $user_id = get_current_user_id();

                // Get user data by user id
                $user = get_userdata( $user_id );

                $display_name = $user->display_name;

                // The text for the note
                $note = __("".$display_name." have uploaded zip file sucessfully!!!");

                // Add the note
                $order->add_order_note( $note );
			}
		}
		else {
			wp_die( "The file type that you've uploaded is not a Excel." );
		}
    }
}

add_action("save_post", "barcodemine_save_order_meta_box", 10, 3);

add_action( 'post_edit_form_tag', 'update_edit_form' );
function update_edit_form() {
    echo ' enctype="multipart/form-data"';
}

add_action( 'woocommerce_after_order_details', 'barcodemine_add_barcode_certification_download' );
function barcodemine_add_barcode_certification_download( $order ){
    $_excel_file = ! empty( $order->get_meta('_excel_file') ) ? $order->get_meta('_excel_file') : array();
    $_certificate_file = ! empty( $order->get_meta('_certificate_file') ) ? $order->get_meta('_certificate_file') : array();
    $_zip_file = ! empty( $order->get_meta('_zip_file') ) ? $order->get_meta('_zip_file') : array();
    ?>
        <section class="woocommerce-customer-details">
	        <h2 class="woocommerce-barcode-details__title">Barcode Certification Download</h2>
            <?php
                if( ! empty( $_excel_file ) ){
                    ?>
                        <div class="download-client-area">
                            <a href="<?php echo $_excel_file['url'];?>" class="button">Download Barcode</a>
                            <a href="<?php echo $_certificate_file['url'];?>" download class="button">Download Barcode Certificate</a>
                            <?php
                                if( ! empty( $_zip_file ) ){
                                    ?>
                                        <a href="<?php echo $_zip_file['url'];?>" download class="button">Download Zip</a>
                                    <?php
                                }
                            ?>
                        </div>
                    <?php
                } else {
                    ?>
                        <div class="download-client-area">
                            <h5>Note: Please wait for 24 hours after placed a order!!!</h5>
                        </div>
                    <?php
                }
            ?>
        </section>
    <?php
}

// function that runs when shortcode is called
function barcodemine_search_shortcode() { 
    
    ob_start();
    ?>
        <form method="post" class="search-gepir-data">
            <input placeholder="GTIN-13 EAN / GTIN-12 UPC" class="elementor-search-form__input" type="number" name="geiper_name" title="Search" value="">
            <button>Submit <i class="fas fa-spinner fa-spin search-loader"></i></button>
        </form>

        <div class="search-result"></div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
// register shortcode
add_shortcode('barcode_search', 'barcodemine_search_shortcode');

add_action( 'wp_ajax_barcode_search' , 'barcodemine_barcode_search' );
add_action( 'wp_ajax_nopriv_barcode_search' , 'barcodemine_barcode_search' );

function barcodemine_barcode_search(){
    $geiper_name = ! empty( $_POST['geiper_name'] ) ? sanitize_text_field( $_POST['geiper_name'] ) : '';
    
    // Add debugging
    error_log('Barcode Search: Looking for barcode - ' . $geiper_name);

    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page'    =>  -1,
        'post_status'       =>  array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
        'meta_query' => array(
            array(
                'key' => '_excel_file_data',
                'value' => $geiper_name,
                'compare' => 'LIKE',
            )
        )
    );
    
    // The Query
    $query = new WP_Query( $args );
    $order_id = null;
    
    if ( $query->have_posts() ) {
        // Start looping over the query results.
        while ( $query->have_posts() ) {
            $query->the_post();
            $order_id = get_the_ID();
            error_log('Barcode Search: Found order ID - ' . $order_id);
        }
        wp_reset_postdata();
    } else {
        error_log('Barcode Search: No orders found with barcode - ' . $geiper_name);
    }
    
    if ( empty( $order_id ) ) {
        echo '<h2>Search results</h2><p>Barcode not found. Please refine your search.</p>';
        die();
    }
    
    $order = new WC_Order( $order_id );
    $billing_address= $order->get_address('billing');

    $excel_data = get_post_meta( $order_id, '_excel_file_data', true );
    ob_start();
    ?>
        <h2>Search results</h2>
        <?php
            if( ! empty( $order_id ) && ! empty( $geiper_name ) ){
                ?>
                    <table class="table table-striped bct-gepir-results__table">
                        <thead>
                            <tr>
                                <th>GLN</th>
                                <th class="bct-gepir-results__company">Company</th>
                                <th>UPC Coordinator</th>
                                <th>Contact</th>
                                <th>GTIN-12 (Quantity)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo ltrim($geiper_name, '0'); ?></td>
                                <td class="bct-gepir-results__company">
                                    <div class="company-name">
                                        <strong><?php echo $billing_address['company']; ?></strong>
                                    </div>
                                    <p translate="no">
                                        <?php
                                            if( ! empty( $billing_address['first_name'] ) ) {
                                                ?>
                                                    <span class="given-name"><?php echo $billing_address['first_name']; ?></span> 
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['last_name'] ) ) {
                                                ?>
                                                    <span class="family-name"><?php echo $billing_address['last_name']; ?></span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['company'] ) ) {
                                                ?>
                                                    <span class="organization"><?php echo $billing_address['company']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['address_1'] ) ) {
                                                ?>
                                                    <span class="address-line1"><?php echo $billing_address['address_1']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['address_2'] ) ) {
                                                ?>
                                                    <span class="address-line2"><?php echo $billing_address['address_2']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <span class="locality"><?php echo $billing_address['city']; ?></span> <span class="postal-code"><?php echo $billing_address['postcode']; ?></span><br>
                                        <span class="administrative-area"><?php echo WC()->countries->get_states( $billing_address['country'] )[$billing_address['state']]; ?></span><br>
                                        <span class="country"><?php echo WC()->countries->countries[$billing_address['country']]; ?></span>
                                    </p>
                                </td>
                                <td>
                                </td>
                                <td>
                                    <?php echo $billing_address['phone']; ?>
                                </td>
                                <td><?php echo $excel_data[0]; ?> thru <?php echo end( $excel_data ); ?> (<?php echo count($excel_data); ?>)</td>
                            </tr>
                        </tbody>
                    </table>
                <?php
            } else {
                ?>
                    <p>Barcode not found. Please refine your search.</p>
                <?php
            }
        ?>
    <?php
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;
    die();
}

add_filter( 'manage_edit-shop_order_columns', 'barcodemine_add_new_order_admin_list_column' );
 
function barcodemine_add_new_order_admin_list_column( $columns ) {
    $columns['barcode_numbers'] = 'Barcode Numbers';
    $columns['download_barcode'] = 'Barcode Files';
    return $columns;
}
 
add_action( 'manage_shop_order_posts_custom_column', 'barcodemine_add_new_order_admin_list_column_content' );
 
function barcodemine_add_new_order_admin_list_column_content( $column ) {
   
    global $post;
 
    if ( 'barcode_numbers' === $column ) {
        $excel_data = get_post_meta( $post->ID, '_excel_file_data', true );
        if( ! empty( $excel_data ) ){
            echo '<strong>'.$excel_data[0].'</strong> to <br/><strong>'. end( $excel_data ).'</strong> ( '. count($excel_data) .' )';
        } else {
            echo '-';
        }
    }
    if ( 'download_barcode' === $column ) {
        $_excel_file = get_post_meta( $post->ID, '_excel_file', true );
        $_certificate_file = get_post_meta( $post->ID, '_certificate_file', true );
        if( ! empty( $_excel_file ) && ! empty( $_certificate_file ) ){
            ?>
                <a href="<?php echo $_excel_file['url']; ?>" class="button button-primary">Download Excel File</a>
                <br/><br/>
                <a href="<?php echo $_certificate_file['url']; ?>" class="button button-primary" download>Download Certificate</a>
            <?php
        } else {
            echo '-';
        }
        
    }
}

add_filter( 'woocommerce_admin_order_preview_get_order_details', 'admin_order_preview_add_custom_meta_data', 10, 2 );
function admin_order_preview_add_custom_meta_data( $data, $order ) {
    // Replace '_custom_meta_key' by the correct postmeta key
    if( $custom_value = $order->get_meta('_custom_meta_key') )
        $data['custom_key'] = $custom_value; // <= Store the value in the data array.

    return $data;
}

// Display custom values in Order preview
add_action( 'woocommerce_admin_order_preview_end', 'custom_display_order_data_in_admin' );
function custom_display_order_data_in_admin(){
    global $post;
    $excel_data = get_post_meta( $post->ID, '_excel_file_data', true );
    // Call the stored value and display it
    if( ! empty( $excel_data ) ){
        echo '<div class="wc-order-preview-addresses"><div class="wc-order-preview-address"><h2>Barcode Numbers: </h2>';
        foreach( $excel_data as $value ){
            echo $value.', ';
        }
        echo '</div></div>';
    }
}