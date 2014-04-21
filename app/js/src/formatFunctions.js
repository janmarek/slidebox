function formatDateTime(dateString) {
	dateString = ko.unwrap(dateString);

	if (!dateString) {
		return null;
	}

	return moment(dateString).format('YYYY-M-D H:m:s');
}