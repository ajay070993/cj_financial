<script src='https://www.google.com/recaptcha/api.js'></script>

    <div class="right_conteiner_main container_common_width full_wrapper signup_page_main">
      <div class="container">
        <div class="seller_pages_title text_center">
          <h4>Sign Up for Funrumble</h4>
        </div><!-- END title -->
        <div class="signup_form_main">
          <div class="signup_form_main_inner">
            <div class="choose_account_title text_center">
              <span>Choose your account type</span>
            </div>
            <?php echo form_open(current_url(), array('class' => 'form-horizontal ajax_form custom_form'));?> 
              <div class="form_sign_tabs_type">
                <div class="form_all_type_row_main extra-space">
                  <div class="form_half_row right_padding_row float-left">
                    <div class="check_customs">
                      <ul>
                        <li>
                          <div class="check_label_perent">
                             <label class="click_lab introopecity" for="test1">
                              <span class="check_imag_span"><img src="<?php echo $this->config->item('front_assets'); ?>img/form_user_icon.png"></span> 
                              <span class="type_title_log">Member
                                <span class="sign_up_text">Sign up</span>
                              </span>
                             <span class="check_input_span"><input id="test1" type="radio" name="user_type" value="Customer" checked></span>
                            </label>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="form_half_row left_padding_row float-right">
                    <div class="check_customs">
                      <ul>
                        <li>
                          <div class="check_label_perent">
                            <label class="click_lab_two" for="test2">
                              <span class="check_imag_span"><img src="<?php echo $this->config->item('front_assets'); ?>img/form_user_icon.png"></span> 
                              <span class="type_title_log">Seller
                                <span class="sign_up_text">Sign up</span>
                              </span>
                              <span class="check_input_span"><input id="test2" type="radio" name="user_type" value="Seller"></span>
                            </label>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>                
                <div class="member_log display_block_form" style="display: none;">                
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>First Name<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="first_name_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Last Name<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="last_name_customer" />
                        </div>
                      </div>
                    </div><!-- END main row -->                  

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>Street address <span>(optional)</span></label>
                          <input class="form_input_box" type="text" name="address_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>City<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="city_customer" />
                        </div>
                      </div>
                    </div><!-- END main row -->

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>State<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="state_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Country<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="country_customer" />
                        </div>
                      </div>
                    </div><!-- END main row -->

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>zip code<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="postal_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>date of birth<em style="color:red">*</em></label>
                          <input class="form_input_box birth_date_coustomer" type="text" name="brith_date_customer" id="birth_date_coustomer" readonly/>                          
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>email address<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="email_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>phone number <span>(optional)</span> </label>
                          <input class="form_input_box" type="text" name="mobile_number_customer" />
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>screen name<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="screen_name_customer" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>password<em style="color:red">*</em> <span>(Must contain 6-15 characters)</span></label>
                          <input class="form_input_box" type="password" name="password_customer" />
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_bottom_tag_line">
                      <p>Must be 18 and older to use this site</p>
                    </div>
                    <div class="check_box_bottom_side">
                      <div class="custom_check_two">
                        <label>
                          <input type="checkbox" name="terms_conditions_customer" value="Yes">
                          <span>I have read and agree to the terms and conditions</span>
                        </label>
                      </div>
                    </div>
                    <div class="check_box_bottom_side extra_padding">
                      <div class="custom_check_two">
                        <label>
                          <input type="checkbox" name="newsletter_customer" value="Yes">
                          <span>Sign up for site newsletters</span>
                        </label>
                      </div>
                    </div>


                    <!-- <div class="g-recaptcha" data-sitekey="6Lc8P30UAAAAAKIDW30LaTFrquj07qRqbnjiXiJY" id="recaptcha2"></div> -->


                </div>

                <div class="seller_log" style="display: none;">                    
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>First Name<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="first_name_seller" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Last Name<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="last_name_seller" />
                        </div>
                      </div>
                    </div><!-- END main row -->

                    <!-- <div class="form_all_type_row_main">
                      <input type="hidden" name="latitude" id="latitude">
                      <input type="hidden" name="longitude" id="longitude">
                      <div class="form_full_row ">
                        <div class="input_form_group">
                          <label>Address<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="address_seller" id="location"/>
                        </div>
                        <div class="mapCanvas" id="map-0"></div>
                      </div>
                    </div> -->

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>Address<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="address_seller" id="location"/>
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>City<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="city_seller" />
                        </div>
                      </div>
                    </div>

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>State<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="state_seller" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Country<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="country_seller" />
                        </div>
                      </div>
                    </div>

                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>Email<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="email_seller" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>date of birth<em style="color:red">*</em></label>
                          <input class="form_input_box birth_date_seller birth_date_coustomer" type="text" name="brith_date_seller" readonly/>
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>Phone number<em style="color:red">*</em></label>
                          <input class="form_input_box" type="text" name="mobile_number_seller" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Referral code <span>(optional)</span></label>
                          <input class="form_input_box" type="text" name="referral_code" />
                        </div>
                      </div>                  
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main multi_sel_custom">
                      <div class="form_full_row">
                        <div class="input_form_group">
                          <label>Experience Category<em style="color:red">*</em> </label>
                          <div class="select_group placeholder-bx-outer">
                            <span class="arrow_select"></span>
                            <span class="placeholder-bx" id="text_that_apply">Please choose all that apply</span>
                            <select class="form_input_box myselect" name="category_id_seller[]" id="category_id_seller" multiple="" onchange="hideText();">
                              <option value="" disabled="">Please choose all that apply</option>
                              <?php 
                                $categories = $this->common_model->getAllActiveCategory();
                                if($categories){
                                  foreach ($categories as $key => $value) {
                              ?>
                                <option value="<?php echo htmlentities($value->id);?>"><?php echo htmlentities($value->category_name);?></option>
                              <?php } } else { ?>
                                <option value="">Category Not Found</option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div><!-- END main row -->
                    
                    <div class="form_all_type_row_main">
                      <div class="form_full_row">
                        <div class="input_form_group">
                          <label>Experience Title<em style="color:red">*</em> </label>
                          <input class="form_input_box" type="text" name="experience_title_seller" />
                        </div>
                      </div>
                    </div><!-- END main row -->

                    <div class="form_all_type_row_main">
                      <div class="form_full_row">
                        <div class="input_form_group">
                          <label>Business Name <span>(optional)</span></label>
                          <input class="form_input_box" type="text" name="business_title_seller" />
                        </div>
                      </div>
                    </div><!-- END main row -->

                    <div class="form_all_type_row_main">
                      <div class="form_full_row ">
                        <div class="input_form_group">
                          <label>Please describe in detail the experience you would like to offer.  Include your estimated fees and the duration of the experience.<em style="color:red">*</em></label>
                          <textarea class="form_input_box seller_signup_textarea" name="description_seller"></textarea> 
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main">
                      <div class="form_full_row ">
                        <div class="input_form_group">
                          <label>Is your experience wheelchair or handicapped accessible? If yes, please explain!</label>
                          <textarea class="form_input_box seller_signup_textarea" name="question_seller"></textarea> 
                        </div>
                      </div>
                    </div><!-- END main row -->
                    <div class="form_all_type_row_main">
                      <div class="form_half_row right_padding_row">
                        <div class="input_form_group">
                          <label>password<em style="color:red">*</em> <span>(Must contain 6-15 characters)</span></label>
                          <input class="form_input_box" type="password" name="password_seller" />
                        </div>
                      </div>
                      <div class="form_half_row left_padding_row">
                        <div class="input_form_group">
                          <label>Confirm password<em style="color:red">*</em> <span>(Must contain 6-15 characters)</span></label>
                          <input class="form_input_box" type="password" name="confirm_password_seller" />
                        </div>
                      </div>
                    </div><!-- END main row -->  
                    <div class="check_box_bottom_side">
                      <div class="custom_check_two">
                        <label>
                          <input type="checkbox" name="terms_conditions_seller" value="Yes">
                          <span>I have read and agree to the terms and conditions</span>
                        </label>
                      </div>
                    </div>
                    <div class="check_box_bottom_side extra_padding">
                      <div class="custom_check_two">
                        <label>
                          <input type="checkbox" name="newsletter_seller" value="Yes">
                          <span>Sign up for site newsletters</span>
                        </label>
                      </div>
                    </div>

                    <!-- <div class="g-recaptcha" data-sitekey="6Lc8P30UAAAAAKIDW30LaTFrquj07qRqbnjiXiJY" id="recaptcha1"></div> -->
                </div>  

                <div class="g-recaptcha" data-sitekey="6Lc8P30UAAAAAKIDW30LaTFrquj07qRqbnjiXiJY" id="recaptcha1"></div>

                <div class="form_submit_button">
                  <button type="submit" class="custom_primery">Sign up</button>
                </div> 
              
                </div>            
              </div>
            </form>
          <div class="img_man_left">
            <img src="<?php echo $this->config->item('front_assets'); ?>img/form_man_signup.png" alt="" />
          </div>
        </div>
      </div>
    </div>


<script type="text/javascript">
  function hideText(){                      
    $("#text_that_apply").addClass('select-text-none');  
  }                
</script>

<script>
$(function() {
  var date = new Date();  
  date.setFullYear( date.getFullYear() - 18);
  ///Month
  if(date.getMonth() < 10)
  {   
    var setmonth = '0'+( date.getMonth());    
  }
  else {
    var setmonth = ( date.getMonth());
  }  
  ///Date
  if(date.getDate() < 10)
  {    
    //var setdate = '0'+( date.getDate()-1);
    var setdate = '0'+( date.getDate());
  }
  else {
    var setdate = ( date.getDate());
  }  
  ///Year
  var setYear = date.getFullYear(); 
  var set_last_date = setmonth + '/' + setdate + '/' + setYear;      
  $('.birth_date_coustomer').val(set_last_date);  
  $('.birth_date_coustomer').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,   
    minYear: 1950,   
    maxDate: date,
  }, 
  );
});

</script>

<script type="text/javascript">

/*$(document).ready(function () {
    var date = new Date();
    date.setFullYear( date.getFullYear() - 18);
    var lastDate = (date.getFullYear())+ '/0' + ( date.getMonth())+ '/0' + ( date.getDate());        
    $('.birth_date_coustomer').datepicker(
    {      
      format: 'yyyy-mm-dd',      
      todayHighlight: true,
      endDate:lastDate, 
      autoclose:true
    }
  );   
});*/

/*$(document).ready(function () {
    var date = new Date();
    date.setFullYear( date.getFullYear() - 18);
    var lastDate = (date.getFullYear())+ '/0' + ( date.getMonth())+ '/0' + ( date.getDate());        
    $('.birth_date_seller').datepicker(
    {      
      format: 'yyyy-mm-dd',      
      todayHighlight: true,
      endDate:lastDate, 
      autoclose:true
    }
  );   
});*/

</script>

<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCxhgcC9Un6YMIVL5agYr7ygNvQMt306Nc&sensor=false&libraries=places"></script>
<script type="text/javascript">
var source_lattitude = '';
var source_longitude = '';
/*var address_input = document.getElementById('location');
var address_autocomplete = new google.maps.places.Autocomplete(address_input);*/

/* code for load default map */
function addresslatLong(lat,long){
  window.source_lattitude = lat;
  window.source_longitude = long;
}

var address_input = document.getElementById('location').value;
var geocoder =  new google.maps.Geocoder();
geocoder.geocode( { 'address': address_input}, function(results, status) {
  if(status == google.maps.GeocoderStatus.OK) {
    source_lattitude = results[0].geometry.location.lat();
    source_longitude = results[0].geometry.location.lng();
    addresslatLong(source_lattitude,source_longitude);
  }
});
/* code for load default map */


var geocoder = new google.maps.Geocoder();
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      updateMarkerAddress(responses[0].formatted_address);
    } else {
      updateMarkerAddress();
    }
  });
}

function updateMarkerStatus(str) {
  //document.getElementById('markerStatus').innerHTML = str;
}

function updateMarkerPosition(latLng) {
  source_lattitude = latLng.lat();
  source_longitude = latLng.lng();
  $('#latitude').val(latLng.lat());
  $('#longitude').val(latLng.lng());
}
function updateMarkerAddress(str) 
{ 
  $('#location').val(str); 
}
function initialize() {
    var address_latLng = new google.maps.LatLng(source_lattitude,source_longitude);
    var mapOptions = {
      center: address_latLng,
      zoom: 7,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById('map-0'),
      mapOptions);
    var address_input = document.getElementById('location');
    var address_autocomplete = new google.maps.places.Autocomplete(address_input);
    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
      map: map,
      position: address_latLng,
      title: 'Drag to change',
      map: map,
      draggable: true
    });
  updateMarkerPosition(address_latLng);
  //geocodePosition(address_latLng);



  // Add dragging event listeners.
  google.maps.event.addListener(marker, 'dragstart', function() {
    updateMarkerAddress('Dragging...');
  });
  google.maps.event.addListener(marker, 'drag', function() {
    updateMarkerStatus('Dragging...');
    updateMarkerPosition(marker.getPosition());
  });
  google.maps.event.addListener(marker, 'dragend', function() {
    updateMarkerStatus('Drag ended');
    geocodePosition(marker.getPosition());
    
  });

  google.maps.event.addListener(address_autocomplete, 'place_changed', function() {
    infowindow.close();
    var place = address_autocomplete.getPlace();
    if (place.geometry.viewport) 
    {
      map.fitBounds(place.geometry.viewport);
    } 
    else 
    {
      map.setCenter(place.geometry.location);
      map.setZoom(10);  // Why 17? Because it looks good.
    }
        
    marker.setPosition(place.geometry.location);
    updateMarkerPosition(place.geometry.location);
    var address = '';
  });

  // Sets a listener on a radio button to change the filter type on Places
  // Autocomplete.
  function setupClickListener(id, types) {
    var radioButton = document.getElementById(id);
      google.maps.event.addDomListener(radioButton, 'click', function() {
      autocomplete.setTypes(types);
    });
  }
}
google.maps.event.addDomListener(window, 'load', initialize);

</script>
