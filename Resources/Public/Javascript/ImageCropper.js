(function(jQuery){
	jQuery.fn.imageCropper = function(options) {
		var defaults = {
			'uploader': null,
			'path': '',
			'url': ''
		};
		var options = jQuery.extend(defaults, options);
		return this.each(function() {
			var element = jQuery(this);
			var scale = 1;
			var aspectRatio = options.aspectRatio;
			var large = element.find('.large img');
			var cropper = jQuery.Jcrop(large);
			var maxWidth = options.maxWidth;
			var maxHeight = options.maxHeight;
			var thumbnail = element.find('.preview img');
			var thumbWidth = element.find('.preview .img-div').width();
			var thumbHeight = element.find('.preview .img-div').height();
			var button = element.find('.button button');
			var cropData;

			function makeCropper() {
				if (cropper) {
					cropper.destroy();
				};
				cropper = jQuery.Jcrop(large);
				cropper.setOptions({
					"aspectRatio": aspectRatio,
					"bgOpacity": .3,
					"onChange": function(coordinates) {
						var scaleX = large.width() / coordinates.w;
						var scaleY = large.height() / coordinates.h;
						var newWidth = scaleX * options.previewWidth;
						var newHeight = scaleY * options.previewHeight;
						var offsetRatioX = coordinates.x / large.width();
						var offsetRatioY = coordinates.y / large.height();
						var newOffsetX = offsetRatioX * newWidth;
						var newOffsetY = offsetRatioY * newHeight;
						var css = {
							"width": Math.round(newWidth) + "px",
							"height": Math.round(newHeight) + "px",
							"marginLeft": "-" + Math.round(newOffsetX) + "px",
							"marginTop": "-" + Math.round(newOffsetY) + "px"
						};
						thumbnail.css(css);
						coordinates.scale = scale;
						coordinates.x *= scale;
						coordinates.y *= scale;
						coordinates.w *= scale;
						coordinates.h *= scale;
						coordinates.x2 *= scale;
						coordinates.y2 *= scale;
						cropData = coordinates;
						button.show();
					}
				});
			};

			function adjustScale() {
				if (large.width() > maxWidth) {
					scale = large.width() / maxWidth;
					large.css({
						"max-width": maxWidth + 'px'
					});
				} else {
					scale = 1;
				};
				thumbnail.css({
					"width": options.previewWidth + 'px',
					"height": options.previewHeight + 'px',
					"marginLeft": '0px',
					"marginTop": '0px'
				});
			};

			if (options.uploader) {
				options.uploader.bind('FileUploaded', function(up, file, info) {
					large.attr('src', options.path + file.name);
					thumbnail.attr('src', options.path + file.name);
					element.show();
					thumbnail.show();
				});
			};
			if (large.hasClass('placeholder')) {
				element.hide();
				thumbnail.hide();
			};

			large.load(function() {
				adjustScale();
				makeCropper();
			});
			
			button.hide();
			button.click(function(event) {
				jQuery.ajax({
					url: options.url,
					type: 'post',
					data: {
						'imageFile': large.attr('src'),
						'cropData': cropData
					},
					complete: function(request) {
						large.attr('src', options.path + request.responseText);
						thumbnail.attr('src', options.path + request.responseText);
						adjustScale();
						makeCropper();
					}
				})
				event.cancelled = true;
				event.stopPropagation();
			});
		});
	};
})(jQuery);


