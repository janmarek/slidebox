function ImageFromUploadTab(chooseImageWindow, insertWindow, config) {
	this.chooseImageWindow = chooseImageWindow;
	this.insertWindow = insertWindow;
	this.config = config;

	this.name = 'Upload image';
	this.key = 'upload';

	// TODO
	this.ajaxUploadAvailable = Boolean(window.FormData);

	this.loaded = ko.observable(false);
	this.loading = ko.observable(false);
	this.files = ko.observable();
	this.uploading = ko.observable(false);
	this.errors = ko.observableArray();
	this.uploadedFiles = ko.observableArray();
}

ImageFromUploadTab.prototype.init = function () {
	this.files(null);
	this.errors([]);

	if (!this.loaded() && !this.loading()) {
		this.loading(true);
		$.getJSON(this.config.uploadedImagesListUrl, function (data) {
			this.uploadedFiles(data.images);
			this.loading(false);
			this.loaded(true);
		}.bind(this));
	}
};

ImageFromUploadTab.prototype.upload = function () {
	var files = this.files();

	if (!files || files.length === 0) {
		alert('Please select file to upload.');
		return;
	}

	var formData = new FormData();
	formData.append('image', files[0]);

	var self = this;
	this.uploading(true);

	$.ajax({
		url: this.config.uploadImageUrl,
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function (data) {
			self.uploading(false);
			self.errors(data.errors);

			if (data.errors.length === 0) {
				self.loaded(false);
				self.chooseImageWindow.opened(false);
				self.insertWindow.open(data.url);
			}
		}
	});
};

ImageFromUploadTab.prototype.insert = function (image) {
	this.chooseImageWindow.opened(false);
	this.insertWindow.open(image.url);
};