<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Caches{

    private $api_endpoint = 'https://ultp.wpxpo.com/wp-json/restapi/v2/';
    
    public function __construct(){
        add_action('admin_init', array($this, 'get_source_data_callback'), 10, 1);
        add_action('rest_api_init', array($this, 'get_template_data'));
    }

    public function get_source_data_callback(){
        $this->get_source_data('all');
    }

    // API Routes for save CSS
	public function get_template_data(){
		register_rest_route(
			'ultp/v2', 
			'/get_all_templates/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'get_all_templates_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
        );
        register_rest_route(
			'ultp/v2', 
			'/get_design/',
			array(
				array(
					'methods'  => 'POST', 
					'callback' => array( $this, 'get_design_callback'),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args' => array()
				)
			)
        );
    }

    // Get Design data callback
	public function  get_design_callback($request){
		try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
            
			$upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'ultp/';
            $file_path      = $dir . "design.json";
            
            if (file_exists( $file_path )) {
                return array( 'success' => true, 'data' => file_get_contents($file_path) );
            } else {
                $this->get_source_data('design');
            }
			
		}catch(Exception $e){
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }
    

    // Get All Template callback
	public function  get_all_templates_callback($request){
		try{
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$upload_dir_url = wp_upload_dir();
			$dir 			= trailingslashit($upload_dir_url['basedir']) . 'ultp/';
            $file_path      = $dir . "template_nd_design.json";
            
            if (file_exists( $file_path )) {
                return array( 'success' => true, 'data' => file_get_contents($file_path) );
            } else {
                $this->get_source_data('templates');
            }
			
		}catch(Exception $e){
			return [ 'success'=> false, 'message'=> $e->getMessage() ];
        }
    }
    


    public function create_directory() {
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/ultp';
        if ( !is_dir($upload_dir) ) {
           mkdir( $upload_dir, 0700 );
           fopen( $upload_dir . '/template_nd_design.json', "w" );
           fopen( $upload_dir . '/design.json', "w" );
        }
        return $upload_dir;
    }

    public function get_source_data($type = 'all') {
        $caching_date = 86400*2; // 2 days cache
        $caches = get_transient( 'ulpt_caches_'.ULTP_VER );
        if($caches != 'updated' && $type == 'all') {
            $this->download_source($type);
            set_transient( 'ulpt_caches_'.ULTP_VER, 'updated', $caching_date );
        }else{
            if($type == 'templates' || $type == 'design'){
                $this->download_source($type);
            }
            if($type == 'all'){
                try{
                    global $wp_filesystem;
                    if ( ! $wp_filesystem ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }
                    $upload_dir_url = wp_upload_dir();
                    $dir 			= trailingslashit($upload_dir_url['basedir']) . 'ultp/';
                    if (!file_exists( $dir . "template_nd_design.json" )) {
                        $this->download_source($type);
                    }else{
                        if (!file_exists( $dir . "design.json" )) {
                            $this->download_source($type);
                        }
                    }
                }catch(Exception $e){
                    return [ 'success'=> false, 'message'=> $e->getMessage() ];
                }
            }
        }
    }

    public function download_source($type) {
        if($type == 'all' || $type == 'templates'){
            $response = wp_remote_post( $this->api_endpoint.'templates', array(
                'method' => 'POST',
                'timeout' => 120,
                'body' => array( 'type' => 'layouts', 'design' => 'all' )
                )
            );
    
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                if($type != 'all'){
                    return array( 'success' => false, 'data' => "Something went wrong: $error_message" );
                }
            } else {
                $path_url = $this->create_directory();
                file_put_contents($path_url.'/template_nd_design.json', $response['body']);
                if($type != 'all'){
                    return array( 'success' => true, 'data' => $response['body'] );    
                }
            }
        }
        if($type == 'all' || $type == 'design'){
            $response = wp_remote_post( $this->api_endpoint.'design', array(
                'method' => 'POST',
                'timeout' => 120
                )
            );
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                if($type != 'all'){
                    return array( 'success' => false, 'data' => "Something went wrong: $error_message" );
                }
            } else {
                $path_url = $this->create_directory();
                file_put_contents($path_url.'/design.json', $response['body']);
                if($type != 'all'){
                    return array( 'success' => true, 'data' => $response['body'] );    
                }
            }
        }
        
    }

    public function download_images($data, $path_url) {
        if($data){
            $data = json_decode($data);
            foreach ($data as $val) {
                $response = wp_remote_get($val->image);
                if( !is_wp_error( $response ) ) {
                    $file_name = $path_url.'/'.wp_basename($val->image);
                    if(!file_exists($file_name)){
                        $responseBody = wp_remote_retrieve_body( $response );
                        $fp = fopen($path_url.'/'.wp_basename($val->image), "w");
                        fwrite($fp, $responseBody);
                        fclose($fp);
                    }
                }
            }
        }
    }
    

}   