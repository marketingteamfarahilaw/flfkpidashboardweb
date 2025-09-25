var __cropInstance = false;


function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#previewimg').attr('src', e.target.result);

      if(__cropInstance){
      	__cropInstance.destroy();
      }

      __cropInstance = new Croppr('#previewimg', {
      	aspectRatio: 1,
      	minSize: [50,50,'px']
      });
    }

    reader.readAsDataURL(input.files[0]);

	$('.upload_pp_btns_cotnainer').removeClass('d-none').removeClass('d-inline-block').addClass('d-none');
	$('.previewimg_container').removeClass('d-none').removeClass('d-inline-block').addClass('d-inline-block');
  }
}

$("#file_profileimage").change(function() {
  readURL(this);
});

$(document).on('click','.upload_pp_btn_class',function(){
	$('#file_profileimage').click();
	return false;
});

$(document).on('click','.cancel_crop_pp_btn_class',function(){
	if(__cropInstance){
		__cropInstance.destroy();
	}

	hide_preview();
	return false;
});

$(window).resize(function(){
	if(__cropInstance){
		__cropInstance.destroy();
	}
	__cropInstance = new Croppr('#previewimg', {
		aspectRatio: 1,
		minSize: [50,50,'px']
	});
});


$(document).on('click','.crop_pp_btn_class',function(){

	var crop_val = __cropInstance.getValue();
	__cropInstance.destroy();

	var imgObject = new Image();
	imgObject.src = $('#previewimg').attr('src');
	imgObject.onLoad = onImgLoaded();

	function onImgLoaded() {
		var canvas = document.createElement("canvas");
		var ctx = canvas.getContext("2d");

		canvas.width = crop_val.width;
		canvas.height = crop_val.height;
		ctx.drawImage(imgObject,crop_val.x,crop_val.y,crop_val.width,crop_val.height,0,0,crop_val.width,crop_val.height);
		var cropped_img = canvas.toDataURL();

		$('#resultimg').attr('src', cropped_img);
		$('#resultimg').removeClass('d-none').removeClass('d-block').addClass('d-block');
		$('#profileimage').val(cropped_img);
		hide_preview();
	}
	
	return false;
});

function hide_preview(){
	$('.upload_pp_btns_cotnainer').removeClass('d-none').removeClass('d-inline-block').addClass('d-inline-block');
	$('.previewimg_container').removeClass('d-none').removeClass('d-inline-block').addClass('d-none');
	$('#previewimg').attr('src','');
	$('.profile-img-alert').remove();
}

$(document).on('click','.file_gallery_img',function(){
	$('.file_gallery_container .file_gallery_img').removeClass('selected');
	$(this).addClass('selected');
	return false;
});

$(document).on('click','.gallery-close',function(){
	$('.file_gallery_container .file_gallery_img').removeClass('selected');
});

$(document).on('click','.gallery-select-image',function(){
	var selected_src = $('.file_gallery_container .file_gallery_img.selected').attr('src');
	$('.file_gallery_container .file_gallery_img').removeClass('selected');
	$('#resultimg').attr('src', selected_src);
	$('#resultimg').removeClass('d-none').removeClass('d-block').addClass('d-block');
	$('#profileimage').val(selected_src);
	$('#image_file_gallery').modal('hide');
});

$(document).on('submit','form',function(){
	if($('.croppr').length >0){
		if($('.profile-img-alert').length >0){
			$('.profile-img-alert').hide();
			$('.profile-img-alert').slideDown();
		}else{
			var profile_img = `<div class="profile-img-alert alert alert-warning alert-dismissible fade show" role="alert">
			  Profile image not saved continue cropping?
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">&check;</span>
			  </button>
			</div>`;
			$('.previewimg_container ').append(profile_img);
		}
		return false;
	}
});