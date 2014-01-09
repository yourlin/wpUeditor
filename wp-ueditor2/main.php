<?php
/**
 * Plugin Name: UEditor
 * Plugin URI: http://wordpress.org/extend/plugins/ck-and-syntaxhighlighter/
 * Version: 1.3.0
 * Author: SamLiu, taoqili
 * Author URI: http://www.taoqili.com
 * Description: 强大的百度开源富文本编辑器UEditor正式登陆wordpress！
 */
@include_once( dirname( __FILE__ ) . "/ueditor.class.php" );
if ( class_exists( "UEditor" ) ) {
    $ue = new UEditor("postdivrich",array(
        //此处可以配置编辑器的所有配置项，配置方法同editor_config.js
        "focus"=>true,
        "textarea"=>"content",
        "zIndex"=>1
    ));
    register_activation_hook( __FILE__, array(  &$ue, 'ue_closeDefaultEditor' ) );
    register_deactivation_hook( __FILE__, array(  &$ue, 'ue_openDefaultEditor' ) );
    add_action("wp_head",array(&$ue,'ue_importSyntaxHighlighter'));
    add_action("wp_footer",array(&$ue,'ue_syntaxHighlighter'));
    add_action("admin_head",array(&$ue,'ue_importUEditorResource'));
    add_action('edit_form_advanced', array(&$ue, 'ue_renderUEditor'));
    add_action('edit_page_form', array(&$ue, 'ue_renderUEditor'));
    add_action( 'plugins_unload', array(&$ue, 'ue_openDefaultEditor'));

    add_filter('the_editor', 'enable_ueditor');
}
function enable_ueditor($editor_box){
    if( strpos($editor_box, 'wp-content-editor-container') > 0 ){
        $js=<<<js_enable_ueditor
        <script type="text/javascript">
                var ueditor_container = document.getElementById('postdivrich');
                var editor_content = document.getElementById('content');
                var ueditor_content = document.createElement('script');
                ueditor_content.innerText = editor_content.innerText;
                ueditor_container.appendChild(ueditor_content);
                ueditor_content.setAttribute('id', 'postdivrich');
                ueditor_content.setAttribute('class', 'postarea');
                ueditor_container.removeAttribute('id');
                ueditor_container.removeAttribute('class');
                var mce_container = document.getElementById("wp-content-wrap");
                mce_container.remove();
        </script>
js_enable_ueditor;
        return $editor_box.$js;
    }
    return $editor_box;
}

function UEditorAjaxGetHandler(){
    include_once( dirname( __FILE__ ) . "/ueditor/php/imageManager.php" );
    exit;
}
add_action( 'wp_ajax_ueditor_get', 'UEditorAjaxGetHandler' );

// Should return an array in the style of array( 'ext' => $ext, 'type' => $type, 'proper_filename' => $proper_filename )
function ueditor_mime_types($mime_types ){
    $types = array(
        'apk' => 'application/android binary'
    );
    return array_merge($types, $mime_types);
}
add_filter( 'mime_types', 'ueditor_mime_types' );

function UEditorAjaxPostHandler(){
    switch($_REQUEST['method']){
        case 'imageUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/imageUp.php" );
            break;
        case 'scrawlUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/scrawlUp.php" );
            break;
        case 'fileUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/fileUp.php" );
            break;
        case 'getRemoteImage':
            include_once( dirname( __FILE__ ) . "/ueditor/php/getRemoteImage.php" );
            break;
        case 'wordImage':
            include_once( dirname( __FILE__ ) . "/ueditor/php/wordImage.php" );
            break;
        case 'onekey':
            include_once( dirname( __FILE__ ) . "/ueditor/php/onekeyUp.php" );
            break;
        default:
            break;
    }
    exit;
}
add_action( 'wp_ajax_ueditor_post', 'UEditorAjaxPostHandler' );

?>