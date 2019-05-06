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

export default {
	props: {
		url: String,
		options: {
			type: Object,
			default: () => ({}),
		}
	},

  data() {
    return {
      fileapi: FileAPI,
    }
  },

  mounted() {
		this.init()

		var choose = document.getElementById('choose');
    var images = document.getElementById('images');
    var self = this

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
	},
	
	methods: {
		init() {
			FileAPI.debug = true
			FileAPI.staticPath = this.staticPath

			for (var x in this.options) {
				console.log(x, this.options[x])
				if (typeof(FileAPI[x]) !== undefined) {
					FileAPI[x] = this.options[x]
				}
			}
		}
	}
}
</script>