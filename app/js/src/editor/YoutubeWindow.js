function YoutubeWindow(texyEditor) {
	this.texyEditor = texyEditor;
	this.opened = ko.observable(false);
	this.youtube = ko.observable('');
	this.youtubeFocused = ko.observable(false);

	var self = this;
	this.youtubeId = ko.computed(function () {
		var youtube = self.youtube();
		var matches = youtube.match("[?&]v=([a-zA-Z0-9_-]+)");
		if (matches) {
			return matches[1];
		} else {
			return youtube;
		}
	});
	this.thumbnail = ko.computed(function () {
		return '//img.youtube.com/vi/' + self.youtubeId() + '/default.jpg';
	});
}

YoutubeWindow.prototype.open = function () {
	this.opened(true);
	this.youtube('');
	this.youtubeFocused(true);
};

YoutubeWindow.prototype.insert = function () {
	this.opened(false);
	this.texyEditor.image('youtube:' + this.youtubeId());
};

