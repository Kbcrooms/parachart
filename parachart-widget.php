<?php
if ( !defined('ABSPATH') )
	die('-1');
class parachart_widget extends WP_Widget{
  //constructor
  function __construct(){
    $widget_ops = array(
			'classname'=>'parachart_widget',
			'description'=>'Create elegant parallax chart to display posts'
		);
		parent::__construct('parachart_widget','Parachart',$widget_ops);
  }
  //widget form
  function form($instance){
    if($instance){
      $title = esc_attr($instance['title']);
      $chartName = esc_attr($instance['chartName']);
    }
    else{
      $title = '';
      $chartName = '';
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title');?>">
        <?php _e('Title', 'parachart_widget'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title;?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('chartName');?>">
        <?php _e('Chart','parachart_widget');?>
      </label>
      <select name="<?php echo $this->get_field_name('chartName');?>" id="<?php echo $this->get_field_id('chartName');?>" class="widefat">
        <?php
          $charts = get_option('parachart-charts');
          foreach($charts as $k=>$v):
            $cName = esc_attr($k);
            echo '<option value="'.$cName.'"';
            if($cName == $chartName)
              echo 'selected="selected"';
            echo '>'.$cName.'</option>';
          endforeach;
        ?>
      </select>
    </p>
    <?php

  }
  //widget update
  function update($new_instance,$old_instance){
    $instance=array();
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['chartName'] = strip_tags($new_instance['chartName']);
    return $instance;
  }
  //widget display
  function widget($args,$instance){
    extract($args);
    $chartName = $instance['chartName'];
    echo $before_widget;
    $title = apply_filters('parachart_widget', $instance['title']);
    //Check if title is set
    if($title)
      echo $before_title.$title.$after_title;
    //Check if chart name is set
    if($chartName)
      echo parachart_display_chart($chartName);
    echo $after_widget;
  }
}
?>
