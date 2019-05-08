<template>
  <div>
		<!-- "js-fileapi-wrapper" -- required class -->
		<div class="js-fileapi-wrapper upload-btn">
			<div class="btn-indigo flex items-center" @click="selectFiles">Choose files</div>
			<div class="upload-btn__txt">Choose files</div>
			<input ref="fileInput" id="choose" name="files" type="file" multiple />
		</div>
		<div id="cam"></div>
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
			csrfToken: null,
    }
  },

  mounted() {
		this.init()

		var choose = document.getElementById('choose');
		var images = document.getElementById('images');
		var camera = document.getElementById('cam')
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
							console.log('**** ', err)
						});
					});

					// Uploading Files
					FileAPI.upload({
						url: self.url,
						headers: {
							'X-CSRF-TOKEN': self.csrfToken,
						},
						files: { filedata: files },
						imageTransform: {
							maxWidth: 1080,
							maxHeight: 1080,
							type: 'image/jpeg',
							quality: 0.86,
						},
						imageAutoOrientation: true,
						progress: function (evt) { /* ... */ },
						complete: function (err, xhr) {
							if (err) {
								console.log('upload error: ', err)
							} else {
								console.log('upload complete: ', xhr)
							}
            }
					});
				}
			});
		});
	},
	
	methods: {
		init() {
			this.getCsrfToken()

			FileAPI.debug = true
			FileAPI.staticPath = this.staticPath

			for (var x in this.options) {
				console.log(x, this.options[x])
				if (typeof(FileAPI[x]) !== undefined) {
					FileAPI[x] = this.options[x]
				}
			}
		},

		getCsrfToken() {
			const meta = document.getElementsByTagName('meta')

			for(var i = 0; i < meta.length; i++) {
				if (meta[i].name.toLowerCase() === 'csrf-token') {
					this.csrfToken = meta[i].content
				}
			}
		},

		selectFiles() {
			this.$refs.fileInput.click()
		},
	}
}
</script>

<style>
#choose {
	opacity: .5;
}
</style>
