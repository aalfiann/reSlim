<!-- Defer Javascript -->
<script>
(function() {
	function getScript(url,success){
		var script=document.createElement('script');
		script.src=url;
		var head=document.getElementsByTagName('head')[0],
		done=false;
		script.onload=script.onreadystatechange = function(){
			if ( !done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') ) {
				done=true;
				success();
				script.onload = script.onreadystatechange = null;
				head.removeChild(script);
			}
		};
		head.appendChild(script);
	  }

	// Load LazySizes
	getScript('assets/js/lazysizes.min.js',function(){});

	// Load jQuery
	getScript('assets/js/jquery-1.10.2.min.js',function(){
		// Load Bootstrap
		getScript('assets/js/bootstrap.min.js',function(){
			// Load Checkbox, Radio & Switch Plugins
			getScript('assets/js/bootstrap-checkbox-radio.min.js',function(){});
			// Load Paper Dashboard Core javascript and methods for Demo purpose
			getScript('assets/js/paper-dashboard.min.js',function(){});
			// Load Export
			getScript('assets/js/package.export.js',function(){});
			// Load jQuery UI
			getScript('assets/js/jquery-ui-1.10.2.min.js',function(){
				$("head").append("<link rel='stylesheet' type='text/css' href='assets/css/jquery-ui-theme-1.10.1.min.css' /><style>.ui-autocomplete{max-height:200px;overflow-y:auto;overflow-x:hidden}* html .ui-autocomplete{height:200px} .ui-autocomplete-loading { background:url('assets/img/ajax-loader.gif') no-repeat right center }</style><style>.lazyload {opacity: 0;} .lazyloading {opacity: 1;transition: opacity 300ms;background: #f7f7f7 url(assets/img/blank.gif) no-repeat center;} .sidebar .nav > li.active-bottom{position:fixed;width:100%;bottom:10px;} .sidebar .nav > li.active-bottom a{background:rgba(255,255,255,0.14);opacity:1;color:#FFFFFF;}</style><style>.modal{overflow:auto;min-height:100%;position:absolute;background-color:#000000;opacity:0.95 !important;} body.modal-open .main-panel{overflow:hidden !important;}</style>");
				$("#firstdate").datepicker({
					dateFormat:"yy-mm-dd"
				}),
				$("#lastdate").datepicker({
					dateFormat:"yy-mm-dd"
				});
			});
			$('#formUpload').submit(function() {
				$.ajax({
					url : $(this).attr("action"),
					type: "POST",
					data : new FormData(this),
					contentType: false,
					cache: false,
					processData:false,
					xhr: function(){
						//upload Progress
						var xhr = $.ajaxSettings.xhr();
						if (xhr.upload) {
							xhr.upload.addEventListener('progress', function(event) {
								var percent = 0;
								var position = event.loaded || event.position;
								var total = event.total;
								if (event.lengthComputable) {
									percent = Math.ceil(position / total * 100);
								}
								/*update progressbar for jQuery UI
								$('#statusprogress').text(percent +"%");
								$( '#progressbar' ).progressbar({
									value: percent
								});*/
								//update progressbar for Twitter Bootstrap
								$('#statusprogress').text(percent +"%");
								$('#progressbar').css('width', percent+'%').attr('aria-valuenow', percent); 
							}, true);
						}
						return xhr;
					},
					mimeType:"multipart/form-data"
				}).done(function(res){ //
					//$(my_form_id)[0].reset(); //reset form
					//$(result_output).html(res); //output response from server
					//submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
				});
			});
		});
	});	
})();
</script>