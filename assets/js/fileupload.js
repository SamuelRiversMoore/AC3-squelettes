$(document).ready(function(){

	// globals
	FileUpload.init();

});

var FileUpload = {
	init:function(){
		$(document).on('change','#fileupload', function(e){
			var label	 = this.nextElementSibling,
				labelVal = label.innerHTML;

			var fileName = e.target.value.split( '\\' ).pop(),
				file = this.files[0];

			if( fileName && file ) {
				label.innerHTML = fileName ? fileName : labelVal;
				label.className += " hasFile";

				if (file.type.match('image.*')) {
					// http://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded
					var reader = new FileReader();
					reader.onload = function (e) {
						$('#inputImg').html('<img src="'+e.target.result+'">');
					}
					reader.readAsDataURL(this.files[0]);
				}
				if (file.type.match('application/pdf') || file.name.match('\.pdf')) {
					$('#inputImg').html('<span class="gros fichier pdf">PDF</span>');
				}
				if (file.type.match('application/msword') || file.type.match('application\/.*officedocument') || file.name.match('\.doc') ) {
					$('#inputImg').html('<span class="gros fichier doc">DOC</span>');
				}
			} else {
				label.innerHTML = labelVal;
			}
		});
	}
}
