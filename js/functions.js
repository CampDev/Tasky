function load_image(url, div) {
	$.get(url, function(data) {
		div.attr('src', url);
	})
	.fail(function() {
		console.error('Couldn\'t load image '+url);
	})
}