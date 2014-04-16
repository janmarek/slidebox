function aceFactory(id) {
	var editor = ace.edit(id);
	editor.setTheme('ace/theme/chrome');
	editor.session.setMode('ace/mode/texy');
	editor.session.setUseWrapMode(true);
	editor.session.setUseSoftTabs(true);
	editor.session.setTabSize(2);
	editor.renderer.setShowGutter(false);
	editor.renderer.setShowPrintMargin(false);

	return editor;
}