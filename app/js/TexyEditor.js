function TexyEditor(aceEditor) {
	this.aceEditor = aceEditor;
}

TexyEditor.UNDERLINE_MIN = 3;
TexyEditor.UNDERLINE_MAX = 20;

TexyEditor.listHandler = function (editor) {
	var line = editor.getCursorPosition().row;
	var lineContent = editor.session.getDocument().getLine(line);

	editor.insert("\n");

	var ulMatches = lineContent.match(/^([\-\*]) /);
	if (ulMatches) {
		editor.insert(ulMatches[1] + ' ');
		return;
	}

	var olMatches = lineContent.match(/^([\d]+)\) /);
	if (olMatches) {
		var nextVal = parseInt(olMatches[1], 10) + 1;
		editor.insert(nextVal + ') ');
		return;
	}

	var letterOlMatches = lineContent.match(/^([a-zA-Z]+)\) /);
	if (letterOlMatches) {
		var letter = letterOlMatches[1];
		var nextLetter;
		if (letter.toLowerCase() === 'z') {
			nextLetter = letter;
		} else {
			nextLetter = String.fromCharCode(letter.charCodeAt(0) + 1);
		}

		editor.insert(nextLetter + ') ');
	}
};

TexyEditor.prototype.tag = function (startText, endText) {
	// todo check if start row == end row

	var text = this.aceEditor.getSelectedText();
	var selectionRange = this.aceEditor.getSelectionRange();
	var newText = startText + text + endText;

	this.aceEditor.session.getDocument().replace(selectionRange, newText);

	if (text) {
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column + startText.length + endText.length);
		this.aceEditor.selection.setSelectionRange(selectionRange);
	} else {
		this.aceEditor.selection.clearSelection();
		this.aceEditor.moveCursorTo(selectionRange.start.row, selectionRange.start.column + startText.length);
	}

	this.aceEditor.focus();
};

TexyEditor.prototype.phrase = function (text) {
	this.tag(text, text);
};

TexyEditor.prototype.bold = function () {
	// todo check if start row == end row

	var text = this.aceEditor.getSelectedText();

	var matches = text.match(/^\*\*(.*)\*\*$/);

	if (matches) {
		var selectionRange = this.aceEditor.getSelectionRange();
		this.aceEditor.session.getDocument().replace(selectionRange, matches[1]);
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 4);
		this.aceEditor.selection.setSelectionRange(selectionRange);
		this.aceEditor.focus();
	} else {
		this.phrase('**');
	}
};

TexyEditor.prototype.italics = function () {
	// todo check if start row == end row

	var text = this.aceEditor.getSelectedText();

	if (text.match(/^\*\*\*.*\*\*\*$/) || text.match(/^\*[^*]+\*$/)) {
		var selectionRange = this.aceEditor.getSelectionRange();
		this.aceEditor.session.getDocument().replace(selectionRange, text.substring(1, text.length - 1));
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 2);
		this.aceEditor.selection.setSelectionRange(selectionRange);
		this.aceEditor.focus();
	} else {
		this.phrase('*');
	}
};

TexyEditor.prototype.heading = function (level) {
	var document = this.aceEditor.session.getDocument();
	var line = this.aceEditor.getCursorPosition().row;
	var headingText = document.getLine(line);
	var emptyLine = headingText === "";

	// is already heading
	if (headingText) {
		var nextLine = document.getLine(line + 1);
		var matches = nextLine.match(/^([\*=\-]){3,}$/);
		if (matches) {
			document.removeLines(line + 1, line + 1);
		}
		if (matches && matches[1] === level) {
			return;
		}
	}

	var insertLines = [];

	if (emptyLine) {
		headingText = prompt('Enter heading text', '');
		insertLines.push(headingText);

		if (!headingText) {
			return;
		}
	}

	var underlineLength = Math.max(TexyEditor.UNDERLINE_MIN, Math.min(TexyEditor.UNDERLINE_MAX, headingText.length));
	var underline = new Array(underlineLength + 1).join(level);
	insertLines.push(underline);

	document.insertLines(line + (emptyLine ? 0 : 1), insertLines);

	var Range = ace.require("ace/range").Range;

	this.aceEditor.selection.setSelectionRange(new Range(line, 0, line, headingText.length));
	this.aceEditor.focus();
};