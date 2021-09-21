
function startInCountDownGen ()
{
  var currentDate = new Date();
  var minutes = currentDate.getMinutes();
  var seconds = currentDate.getSeconds();

  while(minutes > 15) {
    minutes = minutes - 15;
  }

  minutes = 15 - minutes;
  seconds = 60 - seconds;

  seconds = (seconds == 60) ? 0 : seconds;

    // Stop when reach 0
  if (minutes == 0 && seconds == 0) {
    document.getElementById('startincountdownnumber').innerHTML = minutes + seconds;
    return false;
  }

    // set this to the downdown container
    document.getElementById('startincountdownnumber').innerHTML = minutes + ' min ' + seconds + ' sec';

    // migth use in the future (Fri Nov 29 2019 04:14:40 GMT+0800 (China Standard Time) "01" ":19")
  // console.log(currentDate, minutes, seconds);

  setTimeout(() => { startInCountDownGen() }, 1000);

}

function GlobalTimeZoneGMTFuntion ()
{
  String.prototype.splice = function(idx, rem, str) {
      return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
  };

  var currentDate = new Date();
  
  var arrayDate = String(currentDate).split(" ");

  return String(arrayDate[5]).splice(6, 0, ':');
}

function isEmail($email){
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test($email);
}

var GlobalTimeZoneGMT = GlobalTimeZoneGMTFuntion();
var GlobalSchedulesResult = {};
var GlobalPhoneCodeResult = {};


$(document).ready(function () {
  $('#triggerModalToggle').on('click', function() {
    $('#webinarEmbedModal').modal('show')
  })

  startInCountDownGen();
  

  setTimeout(()=>{
    $("#webinarEmbedModal").addClass('modal fade')
  }, 2000)

  $('#nextModalButton').on('click', function(){
    $('#modalProgressbar').width('75%');
    $('#modalProgressbarDisplay').html('75% Complete');

    $('.modalStep1').hide();
    $('.modalStep2').show();
  })

  $('.custom-checkbox').on('click', function(){
    var checkBoxes = $("input.custom-control-input");
    checkBoxes.prop("checked", !checkBoxes.prop("checked"));

    if(checkBoxes.prop("checked")){
      $('#modalPhoneContainer').show();
    }else{
      $('#modalPhoneContainer').hide();
    }
  })

  $.ajax({
    url: $baseUrl + "api/v1/embed/get-schedule/" + GlobalTimeZoneGMT,
    type: "GET",
    success: function(res) {
      GlobalSchedulesResult = res;

      for(var x=0; x < res.schedules.length; x++){
        $('#scheduledropdown').append(new Option(res.schedules[x].date, res.schedules[x].schedule));
      } 

          // $('#phone_country_code').val(res.country_code);
    }
  });

  $.ajax({
    url: $baseUrl + "api/v1/embed/get-phone-code",
    type: "GET",
    success: function(res) {
      GlobalPhoneCodeResult = res
      $.each(res, function(k,v){
          $('#phone_country_code').append(new Option( k + ' +' + v.code, k));
      })

    }
  });

  $('#first_name').on('input propertychange paste',function () {
    if($('#first_name').val().length > 0) {
      $('#first_nameValidation').hide();
      $('#first_name').removeClass('is-invalid');

    }
  })

  $('#email_address').on('input propertychange paste',function () {
    var emailValid = isEmail($('#email_address').val());

    if($('#email_address').val().length > 0) {
      $('#emailValidation1').hide();
      $('#email_address').removeClass('is-invalid');

    }

    if(emailValid){
      $('#emailValidation2').hide();
      $('#email_address').removeClass('is-invalid');
    }

  })

  $('#confirModalButton').on('click', function(e){

    var formError = false;
    if($('#first_name').val().length <= 0) {
      formError = true;
      $('#first_nameValidation').show();
      $('#first_name').addClass('is-invalid');

    }

    var emailValid = isEmail($('#email_address').val());
    
    if($('#email_address').val().length <= 0) {
      formError = true;
      $('#emailValidation1').show();
      $('#email_address').addClass('is-invalid');

    } else if(!emailValid){
      formError = true;
      $('#emailValidation2').show();
      $('#email_address').addClass('is-invalid');
    }

    if(formError) {
      e.preventDefault()
      return
    }

    var formSerialized = $('#webinarEmbedForm').serializeArray();
    formValues = {
      'first_name': '',
      'last_name': '',
      'email': '',
      'schedule': '',
      'ip_address': '',
      'phone_country_code': '',
      'phone': '',
      'timezone': '',
      'real_dates': 1,
      'sms': '',
      'schedule_code': '',
      'hidden_schedule': '',
      'php_timezone': '',
      'list_data': '',
    };

    $.each(formSerialized, function(k, v){
      formValues[v.name] = v.value
    })

    formValues['phone_country_code'] = GlobalPhoneCodeResult[formValues['phone_country_code']].code;

    var list_data = {
        'description': GlobalSchedulesResult.description,
        'name': GlobalSchedulesResult.name,
        'presenters': GlobalSchedulesResult.presenters
    }

    formValues['last_name'] = '';
    formValues['ip_address'] = '';
    formValues['real_dates'] = 1;
    formValues['list_data'] = list_data;
    formValues['php_timezone'] = GlobalSchedulesResult.timezone;
    formValues['hidden_schedule'] = GlobalSchedulesResult.internal_status[formValues['schedule']];
    formValues['schedule_code'] = formValues['schedule'];
    formValues['timezone'] = GlobalTimeZoneGMT;
    formValues['email'] = formValues['email_address'];
    formValues['embed'] = true;


    $.ajax({
      url: $baseUrl + "api/v1/embed/set-schedule?",
      type: "POST",
      data: formValues,
      dataType: "JSON",
      success: function(res) {
        window.location.href = $baseUrl + 'thankyouraw/' + res
      }
    });

  })


})