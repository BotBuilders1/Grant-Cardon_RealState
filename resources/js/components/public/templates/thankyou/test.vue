<template>

<div class="template">
	<div class="row" style="background: #1c1c1c; margin-top:0">
		<div class="col-sm-6 mobile-logo-cont" style="padding:45px;">
			<img class="mobile-logo" src="/image/ty/logo.png" style="width:300px;display:block;margin:0 auto;">
		</div>
		<div class="col-sm-6  no-padding-tb">
			<div class="mobile-header-container" style="width:55%;margin: 0 auto;display: block;height: 55px;">
				<img class="mobile-header-label" style="float:left;width: 45px;margin-top: 10px;" src="/image/ty/red_check.png" >
				<label class="mobile-schedule" style="color:#ffffff;font-size:30px;font-weight:700;font-family:sans-serif;margin: 10px 0 0 5px;display: block;float: left;">Confirmed for </label>
				<label class="mobile-schedule" style="color:#d09b2f;font-size:30px;font-weight:700;font-family:sans-serif;margin: 10px 0 0 5px;display: block;float: left;">
					{{thankYouData.date}}
				</label>
			</div>
			<img @click="trainingLink()" class="mobile-header-button" style="display:block;margin:0 auto;cursor:pointer;width:30%;" src="/image/ty/header_botton.png" >
			<p class="mobile-header-text" style="color:#fff;font-size:14px;text-align:center;"><i>Details were sent to {{thankYouData.email}}</i></p>
		</div>

	</div>
	<div class="container container-body mt-30">
		<div class="row">
			<div class="col-sm-12 mb-30 ">
				<img src="/image/ty/title.png" class="top-sticker" style="width:85%;display:block;margin:45px auto;" />
				<iframe src="https://player.vimeo.com/external/542849609.hd.mp4?s=8cf9bffbe9715a4f5c00c3eec749071da7638507&profile_id=174&autoplay=1" class="videoframe" width="100%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
			</div>
		</div>
		<div class="row">

			<div class="col-sm-6">
				<div class=" mid-container">
					<img src="/image/ty/step1.png" class="mobile-step1" style="width:85%;display:block;margin:40px auto 0;" >
					
					<div class="row">
						<div class="col-sm-4 pb0">
							<img class="mobile-tablet" src="/image/ty/ipad2.png" style="width: 170%;margin-top: 20px;margin-left: -50px;display:block;">
						</div>
						<div class="col-sm-8 pt0">
							<img class="mobile-workbook" src="/image/ty/10XBusinessWorkbook.png" style="width:75%;display:block;margin:50px auto 0;">
							
							<img src="/image/ty/Arrow-Drawing.gif" style="display: block;margin:10px auto 0;width:120px;">
							

							<template v-if="hasLogedInFb">
								<div class="mcwidget-embed" data-widget-id="15043748" :data-widget-payload="thankYouData.user_id"></div>
							</template>
							<template v-else>
								<a href="https://10xworkshop.com/api/workbook/download" target="_blank">
									<img src="/image/ty/button.png" style="display: block;margin:20px auto 15px;width: 75%;cursor:pointer;">
								</a>
							</template>


							<p class="mobile-prop" style="color: #231f20;display: block;width: 80%;margin:0 auto;font-weight:600;margin-bottom:55px;text-align:center;font-style:italic;">You will customize your plan<br>during the interactive workshop!</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="mid-container">
					<img src="/image/ty/step2.png" style="width:85%;display:block;margin:27px auto 0;" >
					
					<div class="row">
						<div class="col-sm-12">
							<img src="/image/ty/10XBusinessWorkbshop.png" style="width:60%;display:block;margin:30px auto 0;">
						</div>
						<div class="col-sm-12">	
							<p style="color:#231f20;text-align:center;font-weight:700;font-size:21px;margin:0;font-family:sans-serif;">YOUR WORKSHOP STARTS:</p>
							<flip-countdown :deadline="deadline1"></flip-countdown>
						</div>
						<div class="col-sm-12">
							<img  @click="trainingLink()" src="/image/ty/training_button.png" style="width:61%;margin:0 auto;display: block;cursor:pointer;">
						</div>
						<div class="col-sm-12 mobile-download-text" style="margin-bottom: 15px;padding-top:0;">
							<p class="mobile-mid-text" style="margin:0;font-weight:700;text-align:center;">✓ Watch training from a laptop or desktop</p>
							<p class="mobile-mid-text" style="margin:0;font-weight:700;text-align:center;">✓ Be 100% focused and ready to take notes</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row mobile-bottom-row" style="margin-top:50px;">
			<div class="col-sm-6">
				<img src="/image/ty/grant.png" style="display: block;width: 216px;margin:0 auto 10px;">
				<img src="/image/ty/instructor.png" style="display: block;width: 313px;margin: 0 auto;">

				<ul style="width: 76%;margin:20px auto;font-size: 18px;">
					<li><b>Companies generate $250+ Million yearly</b></li>
					<li>Ranked #1 Marketing influencer by Forbes</li>
					<li><b>Real Estate portfolio of over $2 Billion</b></li>
					<li>Owns the World's #1 Business Conference</li>
					<li><b>7-Time New York Times Best Selling Author</b></li>
				</ul>
			</div>
			<div class="col-sm-6">
				<img src="/image/ty/laptop.png" style="display: block;width: 216px;margin:0 auto 10px;">
				<img src="/image/ty/online_workshop.png" style="display: block;width: 335px;margin: 0 auto;">
				
				<ul style="width: 88%;margin:20px auto;font-size: 18px;">
					<li><b>Interactive hands-on workshop creates real results</b></li>
					<li>Personalize your step-by-step business plan workbook</li>
					<li><b>Learn the #1 new business income stream for 2021</b></li>
					<li>Create a sales and marketing plan to multiply revenue</li>
					<li><b>Set goals and create a customized plan to reach them </b></li>
				</ul>
			</div>
		</div>

		<div class="row mobile-section " style="display:none;margin-bottom:50px;">
			<div class="col-sm-12">
				<img src="/image/ty/step1.png" style="width:85%;display:block;margin:0 auto 0;" >
				<img src="/image/ty/button.png" @click="downloadFunc()" style="display: block;margin:20px auto;width: 60%;cursor:pointer;">
			</div>
			<div class="col-sm-12">
				<img src="/image/ty/step2.png" style="width:85%;display:block;margin:0 auto 0;" >
				<img  @click="trainingLink()" src="/image/ty/training_button.png" style="width:61%;margin:20px auto;display: block;cursor:pointer;">
			</div>
		</div>



	</div>

	<div class="page-footer">
		<div class="container"  style="background-color:#212529">
			<div class="row justify-content-md-center">
				<div class="col-sm-12 mobile-no-padding-top">
					<img class="mobile-footer-logo" src="/image/grant-cardone-logo-white.png" style="display: block;margin: 30px auto 0;width: 25%;">
				</div>
				<div @click="openCustomModal()" class="col-sm-2 cwhite mobile-footer-link desktop-section" style="text-align:center;cursor:pointer;padding:0;max-width: 11.666667%;"><b>Free Training</b></div>
				<div @click="openInfoModal('Contact Us')" class="col-sm-2 cwhite mobile-footer-link desktop-section" style="text-align:center;cursor:pointer;padding:0;max-width: 11.666667%;"><b>Contact Us</b></div>
				<div @click="openInfoModal('Privacy Policy')" class="col-sm-2 cwhite mobile-footer-link desktop-section" style="text-align:center;cursor:pointer;padding:0;max-width: 11.666667%;"><b>Privacy Policy</b></div>
				<div @click="openInfoModal('Terms of Use')" class="col-sm-2 cwhite mobile-footer-link desktop-section" style="text-align:center;cursor:pointer;padding:0;max-width: 11.666667%;"><b>Terms of Use</b></div>
				<div @click="openInfoModal('Earnings Disclaimer')" class="col-sm-2 cwhite mobile-footer-link desktop-section" style="text-align:center;cursor:pointer;padding:0;max-width: 11.666667%;"><b>Disclaimers</b></div>

				<div @click="openCustomModal()" class="col-sm-2 cwhite mobile-footer-link mobile-section" style="text-align:center;cursor:pointer;"><b>Training</b></div>
				<div @click="openInfoModal('Contact Us')" class="col-sm-2 cwhite mobile-footer-link mobile-section" style="text-align:center;cursor:pointer;"><b>Contact</b></div>
				<div @click="openInfoModal('Privacy Policy')" class="col-sm-2 cwhite mobile-footer-link mobile-section" style="text-align:center;cursor:pointer;"><b>Privacy</b></div>
				<div @click="openInfoModal('Terms of Use')" class="col-sm-2 cwhite mobile-footer-link mobile-section" style="text-align:center;cursor:pointer;"><b>Terms</b></div>
				<div @click="openInfoModal('Earnings Disclaimer')" class="col-sm-2 cwhite mobile-footer-link mobile-section" style="text-align:center;cursor:pointer;"><b>Disclaimers</b></div>

				<div class="col-sm-12 mobile-no-padding-top">
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
		<template v-else-if="informationHeader == 'Earnings Disclaimer'">
			<earning-disclaimer></earning-disclaimer>
		</template>
	</b-modal>
</div>	
</template>


<style>
/** common styling **/
body{
	background:#f4f4f4;
}
.template{
	background:url('/image/ty/bg.png');background-size:100% 100%;background-repeat: no-repeat;
}
@media (min-width:641px) and (max-width: 750px) {
	.videoframe{
	    height: 295px !important;
	}
}
@media (min-width:1px) and (max-width: 640px) {
	.mobile-logo-cont{
		padding:25px 0 0 0 !important;
	}
	.mobile-logo{
		margin:0 auto !important;
		display:block;
		width:180px !important;
	}
	.mobile-header-container{
		width:95% !important;
		height:40px !important;
	}
	.mobile-header-label{
		width: 30px !important;
	    margin: 10px auto 0 !important;
	}
	.mobile-header-button{
		margin:0 auto !important;
		width:60% !important;
	}
	.mobile-header-text{text-align:center;}

	.mobile-tablet{
		width: 80% !important;
		margin:0 auto !important;
	}

	.flip-clock span b{ font-size:33px;}
	.mobile-mid-text{font-size:15px !important;font-weight:100 !important;}

	.mobile-bottom-row{margin-top:12px;}
	.videoframe{
		height: 200px !important;
	}
	.mobile-schedule{font-size:20px !important;}

	.top-sticker{
		width:95% !important;
		margin:30px auto 10px !important;
	}
	.mobile-workbook{
		margin-top:0 !important;
	}

	.mobile-download-text{
		margin-bottom:15px !important;
	}

	.bottomlogo{
		width:40% !important;
	}
	.mobile-bottom-font{font-size:10px !important;}
	.mobile-prop{margin-bottom:15px !important;}


	.pt0{padding-top:0 !important;}
	.pb0{padding-bottom:0 !important;}

	
	.desktop-section{display:none !important;}
	.mobile-section{display:block !important;}
	.mobile-no-padding-top{padding-top:0 !important;}
	.mobile-no-padding-bottom{padding-bottom:0 !important;}
	.mobile-step1{
		margin-top:5px !important;
	}

	/** FOOTER **/
	.page-footer{margin-top:0 !important;}
	.mobile-footer-logo{width:35% !important}
	.mobile-footer-link{
	    width: 20% !important;
	    font-size: 9px !important;
	    padding: 12px 0px !important;
	}
	.mobile-footer-prop{font-size:8px !important;}
	.mobile-credit{font-size:10px !important;}

}

/** FOOTER **/
.page-footer{
	background-color:#212529;
	margin-top: 100px;
}
.cwhite{color:#ffffff;}
.mobile-section{display:none;}


.mid-container{
	padding: 10px 15px;
	-webkit-border-radius: 10px 10px 10px 10px;
	border-radius: 10px 10px 10px 10px;
	-webkit-box-shadow: 0 0 15px 5px #C9C9C9;
	box-shadow: 0 0 15px 5px #C9C9C9;
	background: #ffffff;
}

.videoframe{
	height:625px;
}




</style>

<script>
import FlipCountdown from 'vue2-flip-countdown'
import ContactUs from './../modal/ContactUs.vue'
import PrivacyPolicy from './../modal/PrivacyPolicy.vue'
import TermsOfService from './../modal/TermsOfService.vue'
import EarningsDisclaimer from './../modal/EarningsDisclaimer.vue'


export default {
	name: 'app',
	metaInfo: {
		title: 'thankyou',
		titleTemplate: '%s',
	},
	components: {
		FlipCountdown,
		'contact-us': ContactUs,
		'privacy-policy': PrivacyPolicy,
		'terms-of-service': TermsOfService,
		'earning-disclaimer': EarningsDisclaimer,
	},
	data: () => ({
		thankYouData: {},
		presenter: {},
		startInCountDown: {},
		desc: '',
		goHereHref: '',
		hasLogedInFb: false,
		informationHeader: '',
		deadline1: '2021-04-03 04:00:00'
	}),
	created (){
		this.getThankyouData()

		var video = document.getElementById('video-container');
		// video.play();
	},
	methods: {
		getThankyouData () {
			axios.get('/free/get-thankyou-data/' + this.$route.params.id).then(response => {
				this.deadline1 = response.data.schedule

				this.thankYouData = response.data
				this.presenter = response.data.common_data.presenters[0]
				this.desc = response.data.common_data.description

				// this.deadline1 = this.thankYouData.schedule

				this.goHereHref = 'https://m.me/mattleitzofficial?ref=w8803264--' + response.data.user_id

				this.thankYouData.short_url = 'http://' + this.thankYouData.short_url

				this.startIn()
			})
		},

		calendarUrl (type) {
			var url = "https://www.addevent.com/dir/?client=avENgYdkxzzOCYVpLmYl53702&service=" + type

			url += "&start=" + this.thankYouData.start
			url += "&title=💰 10X Income Webclass with Grant Cardone"
			url += "&description=" + this.desc + '<br>' + this.thankYouData.short_url

			url += "&timezone=" + this.thankYouData.timezone
			url += "&duration=90&alarm=10"

			return url;
		},

		startIn () {
			axios.get('/start-in/?date=' + this.thankYouData.start + '&timezone=' + this.thankYouData.timezone).then(response => {
				this.startInCountDown = response.data

				//start the countdonw
				this.countDonw()
			})
		},

		countDonw () {
			if(this.startInCountDown.s > 0) {
				this.startInCountDown.s = this.startInCountDown.s - 1
			} else {
				if(this.startInCountDown.i > 0) {
					this.startInCountDown.i = this.startInCountDown.i - 1
					this.startInCountDown.s = 59
				} else {
					if(this.startInCountDown.h > 0) {
						this.startInCountDown.h = this.startInCountDown.h - 1
						this.startInCountDown.i = 59
						this.startInCountDown.s = 59
					} else {
						if(this.startInCountDown.d > 0) {
							this.startInCountDown.d = this.startInCountDown.d - 1
							this.startInCountDown.h = 24
							this.startInCountDown.i = 60
							this.startInCountDown.s = 59
						} else {
							window.location.href = this.thankYouData.live_room_url
						}
					}
				}
			}


			var me = this
			setTimeout(function(){
				me.countDonw()
			}, 1000);


		},
		openInfoModal (name="") {
			this.informationHeader = name
			this.$bvModal.show('information-modal')
		},
		trainingLink (){
			window.location.href = this.thankYouData.live_room_url	
		},
		downloadFunc(){
			window.location.href = 'https://10xworkshop.com/api/workbook/download'
		}

	},
    mounted() {
    	var app = this

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
				console.log(response.status)
				if (response.status === 'connected' || response.status === 'not_authorized') {
					app.hasLogedInFb = true
					console.log('logggggggedin')
					let recaptchaScript = document.createElement('script')
					recaptchaScript.setAttribute('src', '//widget.manychat.com/236959743562.js')
					document.head.appendChild(recaptchaScript)

					let recaptchaScript3 = document.createElement('script')
					recaptchaScript.setAttribute('src', 'https://mccdn.me/assets/js/widget.js')
					document.head.appendChild(recaptchaScript3)
				} 
				console.log(app.hasLogedInFb)
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
			fbq('track', 'CompleteRegistration');
			fbq('trackCustom', 'MainRegisteredWebinarEvent');
		}, 1000);

		setTimeout(()=>{
			gtag('event', 'conversion', {'send_to': 'AW-978097576/HugACM2Dz9YBEKirstID'});
		}, 1000);
		**/

    },

}
</script>