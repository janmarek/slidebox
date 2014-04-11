function formatDateTime(dateString) {
	dateString = ko.unwrap(dateString);

	if (!dateString) {
		return null;
	}

	return new Date(dateString).toLocaleString();
}