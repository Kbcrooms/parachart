<?php
if ( !defined('ABSPATH') )
	die('-1');
function generate_cat_dropdown($selected){
  $cat_information = parachart_gen_categories();
  echo '<select name="category">';
  for( $i =0; $i< sizeof($cat_information); $i++):
    echo '<option value="';
    echo $cat_information[$i]['slug'].'"';
    if($cat_information[$i]['slug']==$selected)
      echo "selected";
    echo '>';
    echo $cat_information[$i]['name'];
    echo '</option>';
  endfor;
  echo '</select>';
}
function parachart_display_menu(){
  $charts = get_option('parachart-charts');
  $charts = array_reverse($charts);
?>
  <div>
    <h1>Parachart</h1>
  </div>
  <div class="parachart-admin-page">
    <?php
      foreach($charts as $chart => $chartInfo):
        echo '<div class="parachart-item">';
        echo '<div class="parachart-item-title">';
        echo esc_html($chart);
        echo '</div>';
        echo '<div class="parachart-item-collapse">+</div>';
        echo '<form class="parachart-item-form method="post" action="'.plugins_url('parachart/parachart-admin-process.php').'" data-new="false" data-bound-name="'.esc_attr($chart).'">';
        echo '<div class="parachart-item-form-errors"></div>';
        echo '<p><label>Chart Name</label><input type="text" name="chartname" maxlength="70" value="'.esc_attr($chart).'"></p>';
        echo '<p><label>Posts\' Category</label>';
        generate_cat_dropdown(esc_attr($chartInfo['category']));
        echo '<p><label>Width(%)</label><input type="number" name="width" value="'.esc_attr($chartInfo['width']).'" min="0" max="100"></p>';
        echo '<p><label>Height(px)</label><input type="number" name="height" value="'.esc_attr($chartInfo['height']).'" min="0"></p>';
        echo '<p><label>Item Padding(px)</label><input type="number" name="padding" value="'.esc_attr($chartInfo['padding']).'" min="0"></p>';
        echo '<p><label>Transparency(%)</label><input type="number" name="transparency" value="'.esc_attr($chartInfo['transparency']).'" min="0" max="100"></p>';
        echo '<p><label>Title Color</label><input class="parachart-colorpick" name="titleColor" value="'.esc_attr($chartInfo['titleColor']).'" data-default-color="#FFF"></p>';
        echo '<p><label>Display Content</label><input type="checkbox" class="parachart-item-check" name="display" value="true"';
        if(isset($chartInfo['display']) && esc_attr($chartInfo['display'])=='true')
          echo 'checked';
        echo '></p>';
        echo '<div class="parachart-item-submit"><input type="submit" name="submit" value="Save">';
        echo '<input type="submit" name="submit" value="Delete">';
        echo '</div>';
        echo '</form></div>';
      endforeach;
    ?>
    <div class="parachart-newchart">
        Click to Add New Chart
    </div>
  </div>
<?php
  /*
  //Debug Code
  foreach ($charts as $key => $value) {
    echo "Chart: $key<br>";
    foreach($value as $k => $v)
      echo "$k => $v";
    echo "<br>";
  }
  */
}
?>
