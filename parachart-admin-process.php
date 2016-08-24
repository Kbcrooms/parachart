<?php
if ( !defined('ABSPATH') )
	die('-1');
//This Function is way to be should be broken up and mudularized later
//Maybe make dedicated error handling function
function parachart_process(){
  check_ajax_referer( 'AvocadoNose', 'security' );
  $errors = array();
  //Check For Obvious Errors
  if(empty($_POST['category'])||empty($_POST['new'])||empty($_POST['process'])){
    $errors['form'] = 'Form Error Has Occured(1)';
  }
  if($_POST['new']=='true' && $_POST['process']=='Delete'){
    parachart_process_success();
    wp_die();
  }
  if(empty($_POST['chartName']))
    $errors['chartName'] = 'Chartname is required';
  if(empty($_POST['width']))
    $_POST['width'] = '0';
  if(empty($_POST['height']))
    $_POST['height'] = '0';
  if(empty($_POST['padding']))
    $_POST['padding'] = '0';
  if(empty($_POST['transparency']))
    $_POST['transparency'] = '0';
  if(empty($_POST['titleColor']))
    $_POST['titleColor'] = '#FFF';
  if(empty($_POST['display']))
    $_POST['display']='false';
  if(!empty($errors)){
    parachart_process_fail($errors);
    wp_die();
  }
  $charts = get_option('parachart-charts');
  if($_POST['new']=='true'){
    if($_POST['process']=='Save'){
      $safeTitle = sanitize_text_field($_POST['chartName']);
      if(isset($charts[$safeTitle])){
        $errors['chartName'] = 'Chart with same name exists. Use unique name';
        parachart_process_fail($errors);
        wp_die();
      }
      $safeCategory = sanitize_text_field($_POST['category']);
      $safeWidth = sanitize_text_field($_POST['width']);
      $safeHeight = sanitize_text_field($_POST['height']);
      $safePadding = sanitize_text_field($_POST['padding']);
      $safeTransparency = sanitize_text_field($_POST['transparency']);
      $safeTitleColor = sanitize_text_field($_POST['titleColor']);
      $safeDisplay = sanitize_text_field($_POST['display']);
      $chartInfo = array('category'=>$safeCategory,
        'width'=>$safeWidth,
        'height'=>$safeHeight,
        'padding'=>$safePadding,
        'transparency'=>$safeTransparency,
        'titleColor'=>$safeTitleColor,
        'display'=>$safeDisplay
      );
      $charts[$safeTitle] = $chartInfo;
      update_option('parachart-charts',$charts);
      parachart_process_success();
      wp_die();
    }
  }
  elseif($_POST['new']=='false'){
    if($_POST['process']=='Delete'){
      if(empty($_POST['boundName'])){
        $errors['form'] = 'Form is missing Bound Name';
        parachart_process_fail($errors);
        wp_die();
      }
      $safeTitle = sanitize_text_field($_POST['boundName']);
      unset($charts[$safeTitle]);
      update_option('parachart-charts',$charts);
      parachart_process_success();
      wp_die();
    }
    elseif($_POST['process']=='Save'){
      $safeTitle = sanitize_text_field($_POST['chartName']);
      $safeBoundTitle = sanitize_text_field($_POST['boundName']);
      if($safeTitle!=$safeBoundTitle && isset($charts[$safeTitle])){
        $errors['chartName'] = 'Chart with same name exists. Use unique name';
        parachart_process_fail($errors);
        wp_die();
      }
      unset($charts[$safeBoundTitle]);
      $safeCategory = sanitize_text_field($_POST['category']);
      $safeWidth = sanitize_text_field($_POST['width']);
      $safeHeight = sanitize_text_field($_POST['height']);
      $safePadding = sanitize_text_field($_POST['padding']);
      $safeTransparency = sanitize_text_field($_POST['transparency']);
      $safeTitleColor = sanitize_text_field($_POST['titleColor']);
      $safeDisplay = sanitize_text_field($_POST['display']);
      $chartInfo = array('category'=>$safeCategory,
        'width'=>$safeWidth,
        'height'=>$safeHeight,
        'padding'=>$safePadding,
        'transparency'=>$safeTransparency,
        'titleColor'=>$safeTitleColor,
        'display' =>$safeDisplay
      );
      $charts[$safeTitle] = $chartInfo;
      update_option('parachart-charts',$charts);
      parachart_process_success();
      wp_die();
    }
  }
  $errors['form'] = 'Form Error Has Occured(2)';
  parachart_process_fail($errors);
  wp_die();
}
function parachart_process_fail($errors){
  $returnData['errors'] = $errors;
  $returnData['success'] = false;
  echo json_encode($returnData);
}
function parachart_process_success(){
  $returnData['success'] = true;
  echo json_encode($returnData);
}
?>
