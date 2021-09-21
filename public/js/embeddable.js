var $baseUrl = "http://trainingconfirmation.com/";

if(location.hostname == "webiner.local") {
	$baseUrl = 'http://webiner.local/';
}

function serializeForm(form) {
    if (!form || form.nodeName !== "FORM") {
        return;
    }
    var i, j, q = [];
    for (i = form.elements.length - 1; i >= 0; i = i - 1) {
        if (form.elements[i].name === "") {
            continue;
        }
        if (form.elements[i].name == "schedule") {
        	continue;
        }

        switch (form.elements[i].nodeName) {
        case 'INPUT':
            switch (form.elements[i].type) {
            case 'text':
            case 'hidden':
            case 'password':
            case 'button':
            case 'reset':
            case 'submit':
                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                break;
            case 'checkbox':
            case 'radio':
                if (form.elements[i].checked) {
                    q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                }
                break;
            }
            break;
        case 'file':
            break;
        case 'TEXTAREA':
            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
            break;
        case 'SELECT':
            switch (form.elements[i].type) {
            case 'select-one':
                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                break;
            case 'select-multiple':
                for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                    if (form.elements[i].options[j].selected) {
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
                    }
                }
                break;
            }
            break;
        case 'BUTTON':
            switch (form.elements[i].type) {
            case 'reset':
            case 'submit':
            case 'button':
                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                break;
            }
            break;
        }
    }
    return q.join("&");
}


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

    // check if the sec is single digit and set zero in the front of sec
    minutes = (((minutes.toString()).length == 1) ? '0' : '') + minutes;
    seconds = (((seconds.toString()).length == 1) ? ':0' : ':') + seconds;

    // set this to the downdown container
    document.getElementById('startincountdownnumber').innerHTML = minutes + seconds;

    // migth use in the future (Fri Nov 29 2019 04:14:40 GMT+0800 (China Standard Time) "01" ":19")
	// console.log(currentDate, minutes, seconds);

	setTimeout(() => { startInCountDownGen() }, 1000);

}


function registrationModalNext ()
{
	// switch button
	document.getElementById("firstButtonNext").style.display = "none";
	document.getElementById("SecondButtonComplete").style.display = "block";
	
	// switch form
	document.getElementById('modalBodyTab1').style.display = "none";
	document.getElementById('modalBodyTab2').style.display = "block";
}

function formInputIconGrouper (icon="fa fa-calendar fa-lg") 
{
	var divGroup = divCustomClassGen('input-group', 'group');

	var divIcon = divCustomClassGen('input-group-prepend');
	divGroup.appendChild(divIcon);

	var span = document.createElement('span');
	span.setAttribute('class', 'input-group-text');

	var i = document.createElement('i');
	i.setAttribute('class', icon);

	span.appendChild(i);
	divIcon.appendChild(span);

	return divGroup;

}

function divRowGenv(id="")
{
	var bDiv = document.createElement('div');
	bDiv.setAttribute('class', 'row');
	if (id != "") {
		bDiv.setAttribute('id', id);
	}

	return bDiv;
}

function divColSm12Gen(id="")
{
	var bDiv = document.createElement('div');
	bDiv.setAttribute('class', 'col-sm-12');
	bDiv.setAttribute('style', 'padding-top:12px;padding-bottom:12px;');

	if(id != ""){
		bDiv.setAttribute('id', id);
	}

	return bDiv;
}

function divCustomClassGen(className="", role="")
{
	var bDiv = document.createElement('div');
	bDiv.setAttribute('class', className);

	if(role != "") {
		bDiv.setAttribute('role', role);
	}

	return bDiv;
}

function inputFormControlGen (name="", placeholder="")
{
	var bInput = document.createElement('input');
	bInput.setAttribute('class', 'form-control');
	bInput.setAttribute('type', 'text');
	bInput.setAttribute('name', name);
	bInput.setAttribute('placeholder', placeholder);

	return bInput;
}

function selectFormControlGen (name="", id="")
{
	var select = document.createElement('select');
	select.setAttribute('class', 'form-control');
	select.setAttribute('name', name);

	if(id != ""){
		select.setAttribute('id', id);
	}

	return select;
}

function divAndInputMerger (name="", placeholder="", icon="")
{
	var div = divColSm12Gen();
	var group = formInputIconGrouper(icon);
	var input = inputFormControlGen(name, placeholder);
	
	group.appendChild(input)
	div.appendChild(group)

	return div;
}

function divAndDropdownMerger (name="", icon="", id="") 
{
	var div = divColSm12Gen();
	var group = formInputIconGrouper(icon);
	var dropdown = selectFormControlGen(name, id);
	
	group.appendChild(dropdown)
	div.appendChild(group)

	return div;
}

function divAndspecialform () 
{
	var div = divColSm12Gen('modalPhoneDetails');
	div.setAttribute('style','display:none;');

	var group = formInputIconGrouper('fa fa-mobile fa-lg');
	var dropdown = selectFormControlGen('phone_country_code', 'phone_country_code');
	var pin = divCustomClassGen('input-group-prepend');

	var input = inputFormControlGen('phone', 'Enter Your Mobile Number');

	group.appendChild(pin)
	pin.appendChild(dropdown)
	group.appendChild(input)
	div.appendChild(group)

	return div;
}


function BootstrapCheckBox (name="", checkboxLabel="")
{
	var checkboxId = randomIdGen('dosms');

	var div = divColSm12Gen();

	var divCustom = divCustomClassGen("custom-control custom-checkbox");

	div.appendChild(divCustom);

	var input = document.createElement('input');
		input.setAttribute('type', 'checkbox');
		input.setAttribute('autocomplete', 'off');
		input.setAttribute('class', 'custom-control-input');
		input.setAttribute('value', '1');
		input.setAttribute('name', name);
		input.setAttribute('onchange', 'onChangeCheckbox(this)');
		input.setAttribute('id', checkboxId);

	var label = document.createElement('label');
		label.setAttribute('class', 'custom-control-label');
		label.setAttribute('for', checkboxId);
		label.innerHTML = checkboxLabel;

	divCustom.appendChild(input);
	divCustom.appendChild(label);

	return div;
}

function onChangeCheckbox (me)
{
	if (me.checked) {
		document.getElementById('modalPhoneDetails').style.display="block";
	} else {
		document.getElementById('modalPhoneDetails').style.display="none";
	}
}

function randomIdGen (idString = "")
{
	for(var x = 1; x <= 20; x++){
		idString = idString + Math.round(Math.random() * 9);
	}

	return idString;
}



// modal template
var modalContainer = document.createElement('div');
modalContainer.setAttribute('id',"uniqueModalNameMyModal");
modalContainer.setAttribute('class',"modal fade");
modalContainer.setAttribute('tabindex',"-1");
modalContainer.setAttribute('role',"dialog");

var modalDialog = document.createElement('div');
modalDialog.setAttribute('class',"modal-dialog");
modalDialog.setAttribute('role',"document");

var innderModalContainer = document.createElement('div');
innderModalContainer.setAttribute('class',"modal-content");



// modal header
var modalHeader = document.createElement('div');
modalHeader.setAttribute('class',"modal-header");

var modalHeaderTitle = document.createElement('h5');
modalHeaderTitle.setAttribute('class',"modal-title");

// modal header content
modalHeaderTitle.appendChild(document.createTextNode('Choose Best Time Option'));

var modalHeaderButton = document.createElement('button');
modalHeaderButton.setAttribute('type',"button");
modalHeaderButton.setAttribute('class',"close");
modalHeaderButton.setAttribute('data-dismiss',"modal");
modalHeaderButton.setAttribute('aria-label',"Close");

var modalHeaderButtonSpan = document.createElement('span');
modalHeaderButtonSpan.setAttribute('aria-hidden',"true");
modalHeaderButtonSpan.innerHTML = '&times;';

modalHeaderButton.appendChild(modalHeaderButtonSpan);

modalHeader.appendChild(modalHeaderTitle);
modalHeader.appendChild(modalHeaderButton);



// modal footer
var modalFooter = document.createElement('div');
modalFooter.setAttribute('class',"modal-footer");

// modal footer content
var modalFooterButtonNext = document.createElement('button');
modalFooterButtonNext.setAttribute('id','firstButtonNext');
modalFooterButtonNext.setAttribute('onclick','registrationModalNext()');
modalFooterButtonNext.setAttribute('type','button');
modalFooterButtonNext.setAttribute('class','btn btn-warning');
modalFooterButtonNext.setAttribute('style','width: 100%;');
modalFooterButtonNext.innerHTML = 'Next';
modalFooter.appendChild(modalFooterButtonNext);

var modalFooterButtonComplete = document.createElement('button');
modalFooterButtonComplete.setAttribute('id','SecondButtonComplete');
modalFooterButtonComplete.setAttribute('type','button');
modalFooterButtonComplete.setAttribute('class','btn btn-warning');
modalFooterButtonComplete.setAttribute('style','width: 100%;display:none;');
modalFooterButtonComplete.innerHTML = 'Complete Registration';
modalFooter.appendChild(modalFooterButtonComplete);


// modal body
var modalBody = document.createElement('div');
modalBody.setAttribute('class',"modal-body");


// modal body content
{
	// tab 1
	var modalBodyTab1 = divRowGenv('modalBodyTab1');
	modalBodyTab1.setAttribute('style','display:block;');

	// start in 
	var tab1StartInDiv = divColSm12Gen();
	tab1StartInDiv.setAttribute('style','text-align:center;padding-top:12px;padding-bottom:12px;');

	var tab1StartInStrong = document.createElement('strong');
	tab1StartInStrong.innerHTML = "Starts in ";

	var tab1StartInSpan = document.createElement('span');
	tab1StartInSpan.setAttribute('id', 'startincountdownnumber');
	tab1StartInSpan.innerHTML = "14:59";

	tab1StartInDiv.appendChild(tab1StartInStrong);
	tab1StartInDiv.appendChild(tab1StartInSpan);


	// dropdown here
	var tab1Dropdown = divAndDropdownMerger('schedule', 'fa fa-calendar fa-lg', 'scheduledropdown');



	// location 
	var tab1LocationDiv = divColSm12Gen();
	tab1LocationDiv.setAttribute('style','text-align:center;padding-top:12px;padding-bottom:12px;');

	var tab1LocationStrong = document.createElement('strong');
	tab1LocationStrong.innerHTML = "All Time Options Are In Your Local Time.";

	tab1LocationDiv.appendChild(tab1LocationStrong);


	// put in tab 1
	modalBodyTab1.appendChild(tab1StartInDiv);
	modalBodyTab1.appendChild(tab1Dropdown);
	modalBodyTab1.appendChild(tab1LocationDiv);
	// tab 1 end


	// tab 2
	var modalBodyTab2 = divRowGenv('modalBodyTab2');
	modalBodyTab2.setAttribute('style','display:none;');


	var firstNameInput = divAndInputMerger('first_name', 'Enter Your First Name', 'fa fa-user fa-lg');
	var emailInput = divAndInputMerger('email', 'Enter Your Email Address', 'fa fa-envelope fa-lg');
	var checkboxInput = BootstrapCheckBox('sms', "Check the box to get automated reminders for the webinar");


	var fullPhone = divAndspecialform();

	// phoneCode
	// phone

	modalBodyTab2.appendChild(firstNameInput);
	modalBodyTab2.appendChild(emailInput);
	modalBodyTab2.appendChild(checkboxInput);
	modalBodyTab2.appendChild(fullPhone);
	// tab 2 end
}




modalBody.appendChild(modalBodyTab1);
modalBody.appendChild(modalBodyTab2);


// create form before the modalbody and modalFooter
var modalForm = document.createElement('form');
modalForm.setAttribute('method','POST');
modalForm.setAttribute('action', $baseUrl + 'api/embed/set-schedule');
modalForm.setAttribute('id','webinarModalFormData');


// 3 parts of modal
innderModalContainer.appendChild(modalHeader);
modalForm.appendChild(modalBody); // append in form
modalForm.appendChild(modalFooter); // append in form
innderModalContainer.appendChild(modalForm);


modalDialog.appendChild(innderModalContainer);
modalContainer.appendChild(modalDialog);


document.getElementById('automatedmagicmodal').appendChild(modalContainer);


// get data attribute from the container
var safsfafas = document.getElementById('automatedmagicmodal').dataset;
// console.log(safsfafas);


	




// Button to launch the modal
var modalTriggerButton = document.createElement("button");
modalTriggerButton.setAttribute('type',"button");
modalTriggerButton.setAttribute('class',"btn btn-info");
modalTriggerButton.setAttribute('data-toggle',"modal");
modalTriggerButton.setAttribute('data-target',"#uniqueModalNameMyModal");
modalTriggerButton.innerHTML = 'Sign Up';

document.getElementById('automatedmagicmodal').appendChild(modalTriggerButton);



function GlobalTimeZoneGMTFuntion ()
{
	String.prototype.splice = function(idx, rem, str) {
	    return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
	};

	var currentDate = new Date();
	
	var arrayDate = String(currentDate).split(" ");

	return String(arrayDate[5]).splice(6, 0, ':');
}

var GlobalTimeZoneGMT = GlobalTimeZoneGMTFuntion();
var GlobalSchedulesResult = {};
var GlobalPhoneCodeResult = {};


// on click complete button
document.getElementById('SecondButtonComplete').onclick = function () {
	var form = serializeForm(document.getElementById('webinarModalFormData'));	
	
	// set gmt
	form = form + '&schedule=' + GlobalSchedulesResult.schedules[$('#scheduledropdown option:selected').val()].schedule;
	form = form + '&php_timezone=' + GlobalSchedulesResult.timezone; 
	form = form + '&hidden_schedule=' + GlobalSchedulesResult.schedules[$('#scheduledropdown option:selected').val()].hide;
	form = form + '&timezone=' + GlobalTimeZoneGMT;

	//required
	form = form + '&real_dates=1';
	form = form + '&embed=true';

	// fields that has no values
	form = form + '&last_name=&ip_address=';
	form = form + '&phone_country_code=' + GlobalPhoneCodeResult[$('#phone_country_code').val()].code;


	$.ajax({
		url: $baseUrl + "api/v1/embed/set-schedule?" + form,
		type: "GET",
		success: function(res) {
			window.location.href = res
		}
	});

}


// start the countdown
startInCountDownGen()


// import necessary js file
var importJquery = document.createElement('script');

importJquery.setAttribute('src', $baseUrl + 'js/jquery.min.js');
document.getElementById('automatedmagicmodal').appendChild(importJquery);

// make sure that bootstrap will be much later than jquery
setTimeout(function (){ 
	var importBootstrap = document.createElement('script');
	importBootstrap.setAttribute('src', $baseUrl + 'js/bootstrap.min.js');
	document.getElementById('automatedmagicmodal').appendChild(importBootstrap);

	$.ajax({
		url: $baseUrl + "api/v1/embed/get-schedule/" + GlobalTimeZoneGMT,
		type: "GET",
		success: function(res) {
         	GlobalSchedulesResult = res;

         	for(var x=0; x < res.schedules.length; x++){
	     		$('#scheduledropdown').append(new Option(res.schedules[x].date, x));
         	} 

         	$('#phone_country_code').val(res.country_code);

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

},100)




/**
 * Reference
 * https://stackoverflow.com/questions/6964927/how-to-create-a-form-dynamically-via-javascript
 * https://www.taniarascia.com/how-to-connect-to-an-api-with-javascript/
 **/