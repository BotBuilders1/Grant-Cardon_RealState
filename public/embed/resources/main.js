var $baseUrl = "https://trainingconfirmation.com/";

if(location.hostname == "webiner.local") {
	$baseUrl = 'http://webiner.local/';
}


function removejscssfile(filename, filetype){
    var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist from
    var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
    var allsuspects=document.getElementsByTagName(targetelement)
    for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
    if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1)
        allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
    }
}

if (window.location.hostname == 'www.botbuilders.com') {
	console.log(document.styleSheets);
	document.styleSheets[0].disabled = true;
	document.styleSheets[1].disabled = true;
	document.styleSheets[2].disabled = true;
	removejscssfile("skeleton-above.js", "js")
	removejscssfile("skeleton-below.js", "js")
	
}

// import css
var myHeaders = document.head || document.getElementsByTagName('head')[0];

var bootstrapCustomCss  = document.createElement('link');
bootstrapCustomCss.setAttribute('href', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
bootstrapCustomCss.setAttribute('rel', 'stylesheet');
bootstrapCustomCss.setAttribute('crossorigin', 'anonymous');
bootstrapCustomCss.setAttribute('integrity', 'sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN');
myHeaders.appendChild(bootstrapCustomCss);

var bootstrapMinCss  = document.createElement('link');
bootstrapMinCss.setAttribute('href', $baseUrl + 'embed/resources/css/bootstrap.min.css');
bootstrapMinCss.setAttribute('rel', 'stylesheet');
myHeaders.appendChild(bootstrapMinCss);

// var bootstrapCustomCss  = document.createElement('link');
// bootstrapCustomCss.setAttribute('href', $baseUrl + 'embed/resources/css/bootstrapandcustom.css');
// bootstrapCustomCss.setAttribute('rel', 'stylesheet');
// myHeaders.appendChild(bootstrapCustomCss);



// import necessary js file
var importJquery = document.createElement('script');
importJquery.setAttribute('src', $baseUrl + 'embed/resources/js/jquery.min.js');
myHeaders.appendChild(importJquery);

var importBootstrap = document.createElement('script');
importBootstrap.setAttribute('src', $baseUrl + 'embed/resources/js/bootstrap.min.js');
myHeaders.appendChild(importBootstrap);



// import template
setTimeout(function (){ 
	
	$(document).ready(function () {

		$.ajax({
	        url: $baseUrl + "api/v1/embed/resources/template",
	        type: "GET",
	        success: function(data) {
				$('div#webinarModalLauncherContainer').html(data);
	        }
	    });

	})

},200)