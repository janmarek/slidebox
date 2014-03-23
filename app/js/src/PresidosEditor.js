function PresidosEditor(texyEditor, previewUrl) {
	this.texyEditor = texyEditor;

	this.editorContent = ko.observable(
		this.texyEditor.document.getValue()
	);
	this.previewHtml = ko.observable();

	this.texyEditor.editor.on('change', function (e) {
		this.editorContent(this.texyEditor.document.getValue());
	}.bind(this));



	var self = this;
	ko.computed(function () {
		$.post(previewUrl, {
			text: self.editorContent()
		}, function (data) {
			self.previewHtml(data.html);
		});
	}).extend({
		rateLimit: 1000
	});
}
