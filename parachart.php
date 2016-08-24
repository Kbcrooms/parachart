<?php
/**
*Plugin Name: Parachart
*Plugin URI: http://www.khristancrooms.com
*Description: Create elegant parallax chart to display posts
*Version: 1.0.0
*Author: Khristan Crooms
*Author URI: http://www.khristancrooms.com
*License: GPL2
*License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if ( !defined('ABSPATH') )
	die('-1');
require 'parachart-admin-page.php';
require 'parachart-admin-process.php';
require 'parachart-widget.php';
//Add menu item under plugins
add_action('admin_menu','parachart_menu');
//Hook php function parachart_process for AJAX call
add_action('wp_ajax_parachart_process','parachart_process');
function parachart_menu(){
  add_plugins_page('Parachart','Parachart','publish_posts','parachart','parachart_display_menu');
}

//Add scripts/styles for admin menu plugin use
add_action('admin_enqueue_scripts','parachart_register_scripts');
function parachart_register_scripts(){
  parachart_register_admin_scripts();
}
function parachart_register_admin_scripts(){
  //Makes the color picker and image chooser available
  wp_enqueue_media();
  wp_enqueue_style( 'wp-color-picker' );
  //Makes sure jquery is available
  wp_enqueue_script("jquery");
  wp_register_style('parachart',plugins_url('parachart/css/parachart.css'));
  wp_enqueue_style('parachart');
  wp_register_script('parachart-admin',plugins_url('parachart/js/parachart-admin.js'),array('wp-color-picker'),false,true);
  wp_enqueue_script('parachart-admin');
}
//Add scripts/styles for parachart display
add_action('wp_enqueue_scripts','parachart_register_display_scripts');
function parachart_register_display_scripts(){
  parachart_register_chart_scripts();
}
function parachart_register_chart_scripts(){
  wp_enqueue_script("jquery");
  wp_register_style('parachart-display',plugins_url('parachart/css/parachart-display.css'));
  wp_enqueue_style('parachart-display');
  wp_register_script('parachart-display',plugins_url('parachart/js/parachart-display.js'));
  wp_enqueue_script('parachart-display');
}
//Add option to table upon plugin activation
register_activation_hook(__FILE__,'parachart_activate');
function parachart_activate(){
  register_uninstall_hook(__FILE__,'parachart_uninstall');
  add_option('parachart-charts',array());
}
//Remove option from table upon plugin uninstall
function parachart_uninstall(){
  delete_option('parachart-charts');
}
//Widget Functionality
add_action('widgets_init',function(){
  return register_widget("parachart_widget");
});
//Shortcode Functionality
add_shortcode('parachart','parachart_shortcode');
function parachart_shortcode($atts){
  $pull_atts = shortcode_atts(array('name'=>''),$atts);
  if($pull_atts['name']=='')
    return 'Error: Improperly Formed Shortcode';
  return parachart_display_chart($pull_atts['name']);
}
//Display selected chart on page
function parachart_display_chart($name){
  $charts = get_option('parachart-charts');
  if(!isset($charts[$name]))
    return 'Error: Chart Does Not Exist';
  $chartInfo = $charts[$name];
  $chartHtml = '<div class="parachart-container" style="width:'.esc_attr($chartInfo['width']).'%;">';
  $args = array('category_name'=>$chartInfo['category'],'posts_per_page'=>-1);
  $query = new WP_Query($args);
  while($query->have_posts()):
    $query->the_post();
    $div_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),'full');
    $chartHtml .= '<div class="parachart-chart-holder" style="padding:'.esc_attr($chartInfo['padding']).'px 0;">';
    if($chartInfo['display']=='false'){
      $chartHtml .= '<a href="'.get.'">';
  		$chartHtml .= '<div class="parachart-chart-parallax" style="color:'.esc_attr($chartInfo['titleColor']).';height:'.esc_attr($chartInfo['height']).'px;line-height:'.esc_attr($chartInfo['height']).'px;background-image: linear-gradient(rgba(0,0,0,'.esc_attr(((int)$chartInfo['transparency']/100)).'),rgba(0,0,0,'.esc_attr(((int)$chartInfo['transparency']/100)).')),url('.$div_image[0].');">';
  		$chartHtml .= get_the_title();
  		$chartHtml .= '</div>';
      $chartHtml .='</a>';
    }
    else{
      $chartHtml .= '<div class="parachart-chart-parallax" style="color:'.esc_attr($chartInfo['titleColor']).';height:'.esc_attr($chartInfo['height']).'px;line-height:'.esc_attr($chartInfo['height']).'px;background-image: linear-gradient(rgba(0,0,0,'.esc_attr(((int)$chartInfo['transparency']/100)).'),rgba(0,0,0,'.esc_attr(((int)$chartInfo['transparency']/100)).')),url('.$div_image[0].');">';
      $chartHtml .= get_the_title();
      $chartHtml .= '</div>';
    	$chartHtml .= '<div class="parachart-chart-content">';
      $content = get_the_content($more_link_text, $stripteaser, $more_file);
      $content = apply_filters('the_content', $content);
      $content = str_replace(']]>', ']]&gt;', $content);
    	$chartHtml .= $content;
    	$chartHtml .= '</div>';
    }
		$chartHtml .= '</div>';
  endwhile;
  $chartHtml .='</div>';
  wp_reset_postdata();
  return $chartHtml;
}
//Add the functionality scripts for the admin Menu
add_action( 'admin_footer', 'parachart_admin_menu_scripts');
?>
<?php
function parachart_admin_menu_scripts(){
  //Create Security Value For callin parachart-admin-process.php with ajax
  $ajax_nonce = wp_create_nonce("AvocadoNose");
?>
  <script type="text/javascript">
    //AJAX submit form handler
    jQuery(document).ready(function($){
      $(document).on('submit','.parachart-item-form',function(e){
        var form = $(this);
        var process = form.find('input[type=submit][clicked=true]').val();
        var chartName = form.find('input[name=chartname]').val();
        form.find('.parachart-item-form-errors').empty();
        var formValues ={
          'action' : 'parachart_process',
          'security' : '<?php echo $ajax_nonce; ?>',
          'process' : process,
          'boundName' : form.attr('data-bound-name'),
          'chartName' : chartName,
          'category': form.find('select[name=category]').val(),
          'width': form.find('input[name=width]').val(),
          'height': form.find('input[name=height]').val(),
          'padding': form.find('input[name=padding]').val(),
          'transparency' : form.find('input[name=transparency]').val(),
          'titleColor' : form.find('input[name=titleColor]').val(),
          'display': form.find('input[name=display]').prop('checked'),
          'new': form.attr("data-new")
        };
        var posting = $.post(ajaxurl,formValues,function(data){
          console.log(data);
          data = JSON.parse(data);
          if(data.success){
            if(process=='Save'){
              form.attr('data-new','false');
              form.attr('data-bound-name',chartName);
              form.parent('.parachart-item').find('.parachart-item-title').empty().append(chartName);
            }
            else if(process=='Delete'){
              form.parent('.parachart-item').remove();
            }
          }
          else if(!data.success){
            var errors = '<ul>';
            for(var value in data.errors)
              errors +='<li>'+data.errors[value]+'<li>';
            errors += '</ul>';
            form.find('.parachart-item-form-errors').append(errors);
          }
        });
        e.preventDefault();
      });
    });

    var paracharturl = '<?php echo plugins_url();?>';
    function getParachartCategories(){
        //Serializes and sends the $cat_information array
        return <?php echo json_encode(parachart_gen_categories()); ?>;
    }
  </script>
<?php
}
//Grabs all Category Objects
function parachart_gen_categories(){
  $categories = get_categories(array('hide_empty'       => 0));
  $cat_information = array();
  foreach ($categories as $category):
    //Pulls the category names and slugs out of the $categories array
    array_push($cat_information,array('name'=>$category->cat_name,'slug'=>$category->slug));
  endforeach;
  return $cat_information;
}

?>
