<template>
<div class="proxy-body">
	<div class="desktop-secton">
		<img src="/image/download/bg2.png" style="position:fixed;width:100%">	
		<div class="container">
			<div class="row">
				<div class="col-sm-12" >
					<img src="/image/download/logo.png" @click="openCustomModal()" style="display:block;margin:20px auto 0;cursor:pointer;width:30%">
				</div>
				
				<div class="col-sm-12">
					<img src="/image/download/title.png" @click="openCustomModal()" style="display:block;margin:0 auto;cursor:pointer;width:85%;">
				</div>
				<div class="col-sm-12">
					<img src="/image/download/mid_icon.png" @click="openCustomModal()" style="display:block;margin:0 auto;cursor:pointer;width:40%">
				</div>
				<div class="col-sm-12">
					<b-button variant="success" class="mt-3" block @click="openCustomModal()" style="width:360px;margin:0 auto 100px;font-size:35px;font-weight:bold;text-shadow:1px 1px 1px #000000;padding:10px;-webkit-box-shadow: 0 0 20px 1px #fafafa;box-shadow: 0 0 20px 1px #fafafa;cursor:pointer;">
						DOWNLOAD NOW!
					</b-button>
				</div>


			</div>
		</div>
	</div>

	<div class="mobile-secton" style="display:none;background: #050404;">
		<img src="/image/download/bg-mobile.png" style="position:fixed;width:100%;">	
		<div class="container">
			<div class="row">
				<div class="col-sm-12" >
					<img src="/image/download/logo.png" @click="openCustomModal()" style="display:block;margin:10px auto 0px;width: 150px;cursor:pointer;">
				</div>
				
				<div class="col-sm-12" style="padding:0">
					<img src="/image/download/title-mobile2.png" @click="openCustomModal()" style="display:block;margin:0 auto;width:90%;cursor:pointer;">
				</div>
				<div class="col-sm-12" style="padding:0">
					<img src="/image/download/mid_icon2.png" @click="openCustomModal()" style="display:block;margin:0 auto;width:50%;cursor:pointer;">
				</div>
				<div class="col-sm-12" style="padding:0">
					<b-button variant="success mobile-button" class="" block @click="openCustomModal()" style="width:200px;margin:0 auto;font-size:17px;font-weight:bold;text-shadow:1px 1px 1px #000000;padding:10px;-webkit-box-shadow: 0 0 20px 1px #fafafa;box-shadow: 0 0 20px 1px #fafafa;cursor:pointer;">
						DOWNLOAD NOW!
					</b-button>
				</div>

				<div class="col-sm-12" @click="openCustomModal()" >
					<img src="/image/download/footer.png" class="desktop-secton" @click="openCustomModal()" style="display:block;margin:70px auto 0;width:100%;cursor:pointer;">
					<img src="/image/download/mobile-logos.png" class="mobile-secton" @click="openCustomModal()" style="display:none;margin:17px auto 45px;width:100%;cursor:pointer;">
				</div>

			</div>
		</div>
	</div>


	<div class="page-footer">
		<div class="container">
			<div class="row justify-content-md-center">
				<div class="col-sm-12">
					<img class="mobile-footer-logo" src="/image/ty/gc-logo.png" style="display: block;margin: 45px auto 0;width: 25%;">
				</div>
				<div @click="openCustomModal()" class="col-sm-2 cwhite mobile-footer-link" style="text-align:center;cursor:pointer;"><b>Free Training</b></div>
				<div @click="openInfoModal('Contact Us')" class="col-sm-2 cwhite mobile-footer-link" style="text-align:center;cursor:pointer;"><b>Contact Us</b></div>
				<div @click="openInfoModal('Privacy Policy')" class="col-sm-2 cwhite mobile-footer-link" style="text-align:center;cursor:pointer;"><b>Privacy Policy</b></div>
				<div @click="openInfoModal('Terms of Use')" class="col-sm-2 cwhite mobile-footer-link" style="text-align:center;cursor:pointer;"><b>Terms of Use</b></div>
				<div @click="openInfoModal('Earnings Disclaimer')" class="col-sm-2 cwhite mobile-footer-link" style="text-align:center;cursor:pointer;"><b>Disclaimers</b></div>

				<div class="col-sm-12">
					<p class="cwhite mobile-footer-prop" style="font-size:12px;text-align:center;">
						<i>
							At Cardone Enterprises we believe that success is the result of hard work, education and persistence. Examples given should not be considered typical and there is never a guarantee of results. Information provided is educational in nature and is not legal or financial advice. By using this website or any related materials you agree to take full responsibility for your own results, or lack thereof. Our team is here to support you, but you should always do your own due diligence before making any investment or taking any risk. Success is your duty, obligation and responsibility.
						</i>
					</p>
					<p class="cwhite mobile-credit" style="text-align:center;">
						Cardone Training Technologies | 1-800-368-5771 | 18909 NE 29th Ave Aventura FL 33180<br>
						© Cardone Enterprises. All Rights Reserved.
					</p>
				</div>
			</div>
		</div>
	</div>
	<b-modal id="information-modal" size="lg" ref="informationModal" hide-footer  >
		<template v-slot:modal-title>
			<div style="text-align: left;font-size: 40px;color: #2ab0ee;">{{informationHeader}}</div>
		</template>

		<template v-if="informationHeader == 'Contact Us'">
			<contact-us></contact-us>
		</template>
		<template v-else-if="informationHeader == 'Privacy Policy'">
			<privacy-policy></privacy-policy>
		</template>
		<template v-else-if="informationHeader == 'Terms of Use'">
			<terms-of-service></terms-of-service>
		</template>
		<template v-else-if="informationHeader == 'Earnings Disclaimers'">
			<earning-disclaimer></earning-disclaimer>
		</template>
	</b-modal>

	<div v-if="cookieWarning" style="position:fixed;bottom:0;background:#ffcd69;width:100%;text-align:center;padding:10px 0;">
		<p class="desktopVersion" style="margin:0">We use cookies. By using the site you agree to our <a target="_blank" href="https://grantcardone.com/privacy-policy/">privacy policy</a></p>
		<p class="mobileVersion" style="margin:0;display:none;">We use cookies. By using the site<br> you agree to our <a target="_blank"  href="https://grantcardone.com/privacy-policy/">privacy policy</a></p>
		
		<b-btn variant="info" size="sm" @click="closeCookieWarning" style="padding: 1px 8px;font-size:12px;">
			Agree and Close
		</b-btn>
	</div>
	<!-- MODAL -->

	<div id="francoCustomModal" class="franco-custom-backdrop">
	    <div class="franco-custom-modal">
	    	<header style="border-bottom: 1px solid #dee2e6;padding:16px">
				<div class="col-sm-12">
					<div style="font-size:13px;text-align:center;margin-bottom:2px;color:#000">{{progressPercentLabel}}</div>
					<b-progress variant="success" :value="progressVal" :max="progressMax" striped :animated="true"></b-progress>
				</div>
	    	</header>

	    	<div class="d-block text-center" style="color:#000;margin:16px;">
				<b-form @submit.stop.prevent="formSubmit">
					<div class="row justify-content-md-center">
						<template v-if="signUpStep == 1">
							<div class="col-sm-10 text-area-input-group">
								<b-input-group @click="focusToInput('first_name')">
								  <b-input-group-prepend>
									    <span class="input-group-text" style="width:46px;"><i style="margin: 0 auto;" class="fa fa-user fa-lg"></i></span>
								  </b-input-group-prepend>
									<b-form-input style="font-size:20px;" ref="first_name" v-model="first_name" placeholder="First Name" :class="{'is-invalid':firstnameIsInvalid}" @input="fnChange"></b-form-input>
								  </b-form-input>
								  <div v-show="firstnameIsInvalid" class="invalid-feedback" style="text-align: left;padding-left: 40px;">First Name is required!</div>
								</b-input-group>
							</div>
							<div class="col-sm-10 text-area-input-group">
								<b-input-group>
								  <b-input-group-prepend @click="focusToInput('email_address')">
								    <span class="input-group-text"><i class="fa fa-envelope fa-lg"></i></span>
								  </b-input-group-prepend>
									<b-form-input style="font-size:20px;" ref="email_address" v-model="email_address" placeholder="Email Address" :class="{'is-invalid':emailIsInvalid}" @input="emailChange"></b-form-input>
								  </b-form-input>
								  <div v-show="emailIsInvalid" class="invalid-feedback" style="text-align: left;padding-left: 40px;">Email Address is required!</div>
									  <div v-show="email_error" class="invalid-feedback2" style="text-align: left;padding-left: 40px;display:block !important;">Please enter a valid Email Address</div>
								</b-input-group>
							</div>

							<div class="col-sm-12">
								<b-form-checkbox v-model="doSms" value="1" unchecked-value="0" @change="checkboxCheck">
							      Send reminders so I don't miss it!
							      <img src="/image/red_circle.png" style="height:44px;position:absolute;left:-40px;top:-11px;">
							    </b-form-checkbox>
							</div>
							<div class="col-sm-10" v-if="doSms == '1'">
								<b-input-group>
								  <b-input-group-prepend @click="focusToInput('phone')">
								    <span class="input-group-text"><i class="fa fa-mobile fa-lg"></i></span>
								  </b-input-group-prepend>
								  <b-input-group-prepend>
								  	<select v-model="phoneCode" class="form-control" style="font-size:20px;">
								  		<template v-for="(code, index) in phoneCodes">
								  			<option :value="index">{{index}} +{{code.code}}</option>
								  		</template>
									</select>
								  </b-input-group-prepend>
									<b-form-input ref="phone" v-model="phone" placeholder="Mobile Number" style="font-size:20px;"></b-form-input>
								  </b-form-input>
								</b-input-group>

							</div>
						</template>

						<template v-else-if="signUpStep == 2">

							<div class="col-sm-12 mobile-choose" style="text-align:center;font-weight:bold;font-size:2rem;color:#424242;padding-top:0">
								Choose Best Training Time...
							</div>

							
							<div class="col-sm-12 special-bullet">
								<b-form-group>
									<template v-if="!timeSchedules || timeSchedules.length <= 0">
										<option value="" selected>No Available Schedule</option>
									</template>
									<template v-else v-for="(val, index) in timeSchedules">
										<b-form-radio v-model="schedule" :value="val.schedule" style="text-align:left">
											{{val.date}}
											<template v-if="index == 0">
												<label style="font-size:17px;display:block;text-align:center;"><i>({{fullCountDownDisplay}})</i></label>
											</template>
										</b-form-radio>
									</template>
								</b-form-group>
							</div>

							<div class="col-sm-12">
								<span style="font-style: italic;">All time options are in your local time.</span>
							</div>
							
							<div class="col-sm-12" style="padding:0;">
								<div class="mcwidget-checkbox" data-widget-id="15043714" data-email="sample@gmail.com"></div>
							</div>

							
						</template>


						<!-- footer -->
						<div class="col-sm-12" style="margin-bottom:10px;">
							<template v-if="signUpStep == 2">
								<div style="width:50%; margin:0 auto">
									<b-button variant="warning" class="mt-3" block @click="next()">Next Step »</b-button>
								</div>
							</template>
							<template v-else-if="signUpStep == 1">
								<div class="complete-button-container" style="width:62%; margin:0 auto">
									<b-button variant="warning" class="mt-3" block @click="formSubmit()">✓ Complete Registration</b-button>
								</div>
							</template>
						</div>
					</div>
				</b-form>
			</div>


		</div>
	</div>



</div>
</template>


<style>
@media (min-width:1px) and (max-width: 640px) {
	.desktop-secton{display:none !important; }
	.mobile-secton{ display:block !important;}
	.mobile-choose{font-size:24px !important;}
	.btn-success{
		background-color:#28a745 !important;
		border-color:#28a745 !important;
	}

	.mobile-footer-logo{width:35% !important}
	.mobile-footer-prop{font-size:9px !important;}
	.mobile-credit{font-size:10px !important;}
	.mobile-footer-link{
	    width: 20% !important;
	    font-size: 9px !important;
	    padding: 12px 0px !important;
	}
	.franco-custom-modal{margin-top:20px !important;}
	.mobile-button{
		border-color:#14882f !important;
	}
}
/** MODAL **/
.franco-custom-backdrop{
    height: 0;
    width: 0;
    background: rgba(0, 0, 0, 0.5);
    top: 0px;
    position: fixed;
    border: 1px solid rgb(0, 0, 0);
    color: rgba(0, 0, 0, 0.5);
    overflow:auto;
    z-index:10;
}
.franco-custom-modal{
    height: auto;
    width: 100%;
    background: #fff;
    margin: 100px auto 0px;
    max-width: 500px;
    -webkit-border-radius: 5px 5px 5px 5px;
    border-radius: 5px 5px 5px 5px;
    border: 4px solid #5d5d5d;
}

.dropdown-carret{
	-webkit-appearance: listbox;
    -moz-appearance: listbox;
    appearance: listbox;
}

.b-dropdown, .b-dropdown button, .b-dropdown button:hover{
	border: none !important;
	background:#fff !important;
	color:#000 !important;
}
.btn-webinar, .btn-webinar:hover{
    -webkit-border-radius: 50px 50px 50px 50px;
    border-radius: 50px 50px 50px 50px;
    background:#ffb500;
    border:4px solid #ff9900;
    color:#fff !important;
    font-weight: bold;
    font-size: 40px;
    padding: 6px 30px;
    text-shadow: 2px 2px 2px #000000;
    float:left;
    margin: 0 auto;
    padding-top: 0px;
    display: block;
}
.btn-warning{
	background-color:#b90700 !important;
	border:3px solid #7d1511 !important;
	color:#fff !important;
	text-shadow: rgb(0, 0, 0) 1px 1px 1px;
    font-size: 22px;
    margin-top: 0px !important;
    -webkit-border-radius: 20px 20px 20px 20px;
	border-radius: 20px 20px 20px 20px;
}
.btn-warning:hover{
	background-color:#218838 !important;
	border:3px solid #1e7e34 !important;
	color:#fff !important;
}

.progress-bar-animated{ background-repeat: repeat-x; }

.special-bullet .form-group{
    width: 285px;
    margin: 0 auto;
    font-size:19px;
    line-height: 18px;
}
.special-bullet .form-group .custom-control{
    margin-bottom: 8px;
}

/** PAGE **/
.proxy-body{
	/**background-image: url("/image/download/desktop/full_page.png");**/
    width: 100%;
    height: 100vh;
}

.page-footer{background:#231f20 !important;}
.cwhite{color:#ffffff;}

.btn-success{
	background-color:#28a745;
	/** background-color:#ec0900; **/
	border:4px solid #14882f;
}
.btn-success:hover{
	background-color:#ec0900;
	border:4px solid #ec0900;
}

</style>


<script>
import moment from 'moment'
import ContactUs from './../modal/ContactUs.vue'
import PrivacyPolicy from './../modal/PrivacyPolicy.vue'
import TermsOfService from './../modal/TermsOfService.vue'
import EarningsDisclaimer from './../modal/EarningsDisclaimer.vue'

export default {
	name: 'app',
	metaInfo: {
		title: 'index',
		titleTemplate: '10xworkshop',
	},
	components: {
		'contact-us': ContactUs,
		'privacy-policy': PrivacyPolicy,
		'terms-of-service': TermsOfService,
		'earning-disclaimer': EarningsDisclaimer,
	},
	data: () => ({
		signUpStep: 2,
		timeSchedules: [],
		arrayTimeschedule: [],
		phoneCodes: [],

		phoneCode: '',
		schedule: '', 
		timeZone: 0,
		first_name: '',
		email_address: '',
		myIp: '',
		doSms: 0,
		phone: '',
		value: '',
		stringTimeZone: '',
		phpTimezone: '',

		fullCountDownDisplay: '',
		getScheduledData: [],
		CheckBoxHtml: '',
		countDownContainer: true,
		progressVal: 50,
		progressMax: 100,
		progressPercentLabel: '50% Complete',
		
		// validation
		firstnameIsInvalid: false,
		emailIsInvalid: false,

		indexMinutes: '00',
		indexSeconds: '00',

		currency: {
			'default_amount': 100000,
			'max_amount': 1000000,
			'symbol': '$',
		},
		currencies: [],

		informationHeader: '',
		locationInformation: {},

		email_error: false,
		cookieWarning: false,
	}),
	created (){
		this.getClientTimezoneData()
		this.countDownTimer()

		// delete cookie to always have fresh data
		this.$cookie.delete('countdown')

		this.getPhoneCodes()
		this.getAllCurrency()


		// close modal in esc
        let app = this
        document.addEventListener('keyup', function (evt) {
            if (evt.keyCode === 27) {
                app.closeCustomModal();
            }
        });

        document.addEventListener('click', function(e) {
			if (e.target.id == 'francoCustomModal') {
				app.closeCustomModal();
			}
		});

		if(this.$route.query.email){
			this.email_address = this.$route.query.email
		}

		if(this.$route.query.name){
			if(this.$route.query.name != "Friend" && this.$route.query.name != "friend"){
				this.first_name = this.$route.query.name
			}
		}
	},
	methods: {
		closeCookieWarning() {
			// https://stackoverflow.com/questions/22371826/how-do-i-set-a-cookie-to-expire-after-1-minute-or-30-seconds-in-jquery
			// https://stackoverflow.com/questions/50754590/how-to-set-cookie-in-vuejs

			// set date value 1hr in cookie
			var dateEx = new Date();
			dateEx.setTime(dateEx.getTime() + (3600 * 1000));
			this.$cookie.set('cookieWarning',true, { expires: dateEx })
			this.cookieWarning = false
		},
		closeCustomModal () {
			const fcm = document.getElementById("francoCustomModal")
			fcm.style.height = '0'
			fcm.style.width = '0'
		},
		openCustomModal () {

			const fcm = document.getElementById("francoCustomModal")
			fcm.style.height = '100vh'
			fcm.style.width = '100%'

		},
		countDownTimer() {
			var currentDate = new Date()
			var minutes = parseInt(moment(currentDate).format('mm'))
			var seconds = parseInt(moment(currentDate).format('ss'))

			while(minutes > 30) {
				minutes = minutes - 30
			}

			minutes = 30 - minutes
			seconds = 60 - seconds


            if (minutes == 0 && seconds == 0) {
            	return false
            }

            if (seconds <= 0) {
            	minutes = minutes - 1
            	seconds = seconds + 60
            }

            seconds = seconds - 1

            this.indexMinutes = (String(minutes).length <= 1) ? '0' + minutes : minutes;
            this.indexSeconds = (String(seconds).length <= 1) ? '0' + seconds : seconds;

        	this.fullCountDownDisplay = minutes + ' min ' + seconds + ' sec'
			// this.$cookie.set("countdown", this.fullCountDownDisplay, "15m")

        	setTimeout(() => { this.countDownTimer() }, 1000)
        },
		next () {
			
			this.progressVal = 75;
			this.signUpStep = 1;
			this.progressPercentLabel = "75% Complete"


			var app = this
			setTimeout(()=>{
				app.$refs.first_name.$el.focus();
			},600)
			
		},
		getClientTimezoneData () {
			axios.get('/client-timezone-data').then(response => {
				this.locationInformation = response.data;
				this.timeZone = (response.data) ? response.data.gmt : 'GMT-5'
				this.stringTimeZone = response.data.timezone
				this.phoneCode = response.data.countryCode
				this.getSchedule()
				// this.getCurrencyEstimation(response.data.currency)

				if(response.data.continent == 'Europe'){
					if(this.$cookie.get('cookieWarnings') == null){
						this.cookieWarning = true
					}
				}
			})
		},
		getSchedule () {
			axios.get('/gc/get-schedule/' + this.timeZone).then(response => {
				this.getScheduledData = response.data
				this.timeSchedules = response.data.schedules
				this.arrayTimeschedule = response.data.internal_status
				this.phpTimezone = response.data.timezone

				if (this.timeSchedules.length > 0) {
					// select the first data as default
					this.schedule = response.data.schedules[0].schedule;
					this.changeSchedule()
				}
			})
		},
		onChangedTimezone () {
			// when timezone change update the schedule value
			this.getSchedule()
		},
		formSubmit () {

			// validate first_name and email here
			var hasError = false;
			if (this.first_name == ""){
				this.firstnameIsInvalid = true
				hasError = true				
			}

			if (this.email_address == ""){
				this.emailIsInvalid = true
				hasError = true				
			}

			if (hasError) {
				return
			}

			window.mcAsyncInit = function () {
				MC.getWidget(15043714).submit()
			}


			var list_data = {
				'description': this.getScheduledData.description,
				'name': this.getScheduledData.name,
				'presenters': this.getScheduledData.presenters
			};

			var formData = {
				'first_name': this.first_name,
				'last_name': '',
				'email': this.email_address,
				'schedule': this.schedule,
				'ip_address': '',
				'phone_country_code': this.phoneCodes[this.phoneCode].code,
				'phone': this.phone,
				'timezone': this.timeZone,
				'real_dates': 1,
				'sms': this.doSms,
				'schedule_code': this.schedule,
				'hidden_schedule': this.arrayTimeschedule[this.schedule],
				'php_timezone': this.phpTimezone,
				'list_data': list_data,
				'location_information': this.locationInformation,
				'goal': 0,
				'page': 'download',
			}

			axios.put('gc/set-schedule/' + this.timeZone, formData).then(response => {
				if(response.data){
					window.location.href = '/thankyou/' + response.data
				} else {
					this.email_error = true
				}
			}).catch(function (error) {
			    console.log(error.response);
			});
		},
		getPhoneCodes () {
			axios.get('/get-phone-code/').then(response => {
				this.phoneCodes = response.data
			})
		},
		setMyChatCheckbox () {
		},
		changeSchedule () {
			this.setMyChatCheckbox();

			if(this.schedule == "jot") {
				this.countDownContainer = true
			}else{
				this.countDownContainer = true
			}
		},
		focusToInput (refName) {
			this.$refs[refName].$el.focus()

		},
		checkboxCheck () {
			var app = this
			setTimeout(()=>{
				app.$refs.phone.$el.focus();
			},600)
		},
		fnChange () {
			if (this.first_name == ""){
				this.firstnameIsInvalid = true
			} else {
				this.firstnameIsInvalid = false
			}
		},
		emailChange () {
			if (this.email_address == ""){
				this.emailIsInvalid = true
			} else {
				this.emailIsInvalid = false
			}
		},
		formatPrice(value) {
	        let val = (value/1)
	        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	    },
	    getCurrencyEstimation (currency) {
	    	axios.get('/currency/' + currency).then(response => {
				this.currency = response.data
			})
	    },
	    getAllCurrency () {
	    	axios.get('/currencies').then(response => {
				this.currencies = response.data
			})
	    },
	    changeCurrency (i) {
	    	this.currency = this.currencies[i]
	    },
		openInfoModal (name="") {
			this.informationHeader = name
			this.$bvModal.show('information-modal')
		}
	},
	watch: {
	},
	mounted() {

		this.$root.$on('bv::modal::hide', (bvEvent, modalId) => {
			// back to step 1
			this.signUpStep = 2
		})


		let recaptchaScript2 = document.createElement('script')
		recaptchaScript2.setAttribute('src', 'https://connect.facebook.net/en_US/sdk.js')
		document.head.appendChild(recaptchaScript2)

		var fb_ok=false;

		window.fbAsyncInit = function() {
			FB.init({
				appId : '386440791957243',
				status : true,
				xfbml : true,
				version : 'v2.7'
			});
			FB.getLoginStatus(function(response) {
				console.log(response);
				if (response.status === 'connected' || response.status === 'not_authorized') {
					let recaptchaScript = document.createElement('script')
					recaptchaScript.setAttribute('src', '//widget.manychat.com/236959743562.js')
					document.head.appendChild(recaptchaScript)
				} 
			})
		}

		/**
		setTimeout(()=>{
			!function(f,b,e,v,n,t,s)
			{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window, document,'script',
			'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', '174127893452645');
			fbq('track', 'PageView');
			fbq('trackCustom', 'RossWebinarLandingEvent');
		}, 1000);
		**/
	}
}
</script>