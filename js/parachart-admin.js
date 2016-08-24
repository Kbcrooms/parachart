jQuery(document).ready(function($){
  //Initializes colorpicker for php generated items
  $('.parachart-colorpick').wpColorPicker();
  //Collapse Functionality for Chart Item in Admin Menu
  $(document).on('click','.parachart-item-collapse',function(){
    $(this).next('.parachart-item-form').slideToggle(300);
    $(this).toggleClass('rotated');
  });
  //Funcitonality for NewChart Admin Menu Button
  $('.parachart-newchart').click(function(){
    var newItem = getParachartItem();
    $(this).before(newItem);
    $('.parachart-colorpick').wpColorPicker();
    $(this).prev('.parachart-item').slideToggle(300);
  });
  //Trips the clicked attribute to show whether Saved or Deleted
  $(document).on('click','.parachart-item-form input[type=submit]',function(){
    $(this).closest('.parachart-item-form').find(' input[type=submit][clicked=true]').removeAttr('clicked');
    $(this).attr('clicked','true');
  });

  //Grabs the category information using func defined in parachart-admin-page.php
  var categoryInfo = getParachartCategories();
  //Uses categoryInfo global var to generate a dropdown form list
  function genCategoryDropDown(){
    var dropdown ='<select name ="category">';
    for( i = 0; i<categoryInfo.length; i++){
      dropdown += '<option value="';
      dropdown += categoryInfo[i].slug;
      dropdown += '">';
      dropdown += categoryInfo[i].name;
      dropdown += '</option>';
    }
    dropdown += '</select>';
    return dropdown;
  }
  function getParachartItem(){
    var newItem ="";
    newItem += '<div style="display:none;" class="parachart-item">';
    newItem += '<div class="parachart-item-title">';
    newItem += 'New Chart';
    newItem += '</div>';
    newItem += '<div class="parachart-item-collapse">+</div>';
    newItem += '<form class="parachart-item-form" method= "post" action="'+paracharturl+'/parachart/parachart-admin-process.php'+'" data-new="true" >';
    newItem += '<div class="parachart-item-form-errors"></div>';
    newItem += '<p><label>Chart Name</label><input type="text" name="chartname" maxlength="70"></p>';
    newItem += '<p><label>Posts\' Category</label>';
    newItem += genCategoryDropDown()+'</p>';
    newItem += '<p><label>Width(%)</label><input type="number" name="width" value="80" min="0" max="100"></p>';
    newItem += '<p><label>Height(px)</label><input type="number" name="height" value="200" min="0"></p>';
    newItem += '<p><label>Item Padding(px)</label><input type="number" name="padding" value="0" min="0"></p>';
    newItem += '<p><label>Transparency(%)</label><input type="number" name="transparency" value="10" min="0" max="100"></p>';
    newItem += '<p><label>Title Color</label><input class="parachart-colorpick" name="titleColor" value="#FFF" data-default-color="#FFF"></p>';
    newItem += '<p><label>Display Content</label><input type="checkbox" class="parachart-item-check" name="display" value="true">';
    newItem += '<div class="parachart-item-submit"><input type="submit" name="submit" value="Save">';
    newItem += '<input type="submit" name="submit" value="Delete">';
    newItem += '</div>';
    newItem += '</form></div>';
    return newItem;
  }
});
