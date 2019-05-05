<template>
  <div>
		<!-- "js-fileapi-wrapper" -- required class -->
		<div class="js-fileapi-wrapper upload-btn">
			<div class="upload-btn__txt">Choose files</div>
			<input id="choose" name="files" type="file" multiple />
		</div>
		<div id="images"><!-- previews --></div>
	</div>
</template>

<script>
import FileAPI from '@/libs/FileAPI/FileAPI.min.js'

FileAPI.debug = true

export default {
  data() {
    return {
      url: '/upload',
      fileapi: FileAPI,
    }
  },
  mounted() {
		var choose = document.getElementById('choose');
    var images = document.getElementById('images');
    var self = this

    console.log('FileAPI: ', FileAPI)

		FileAPI.event.on(choose, 'change', function (evt) {
			var files = FileAPI.getFiles(evt); // Retrieve file list

			FileAPI.filterFiles(files, function (file, info/**Object*/) {
				if( /^image/.test(file.type) ) {
					return	info.width >= 320 && info.height >= 240;
				}
				return	false;
			}, function (files/**Array*/, rejected/**Array*/) {
				if( files.length ) {
					// Make preview 100x100
					FileAPI.each(files, function (file) {
						FileAPI.Image(file).preview(100).get(function (err, img) {
							images.appendChild(img);
						});
					});

					// Uploading Files
					FileAPI.upload({
						url: self.url,
						files: { images: files },
						progress: function (evt) { /* ... */ },
						complete: function (err, xhr) {
              console.log('complete', xhr, err)
            }
					});
				}
			});
		});
  }
}
</script>