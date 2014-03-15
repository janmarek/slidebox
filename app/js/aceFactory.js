function aceFactory(id) {
	var editor = ace.edit(id);
	editor.setTheme("ace/theme/tomorrow");
	editor.session.setMode("ace/mode/text");
	editor.renderer.setShowGutter(false);
	editor.commands.addCommand({
		bindKey: "Enter",
		exec: TexyEditor.listHandler
	});

	return editor;
}