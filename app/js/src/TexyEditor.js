function TexyEditor(aceEditor) {
	this.editor = aceEditor;
	this.document = this.editor.session.getDocument();
	this.registerHandlers();
}

TexyEditor.UNDERLINE_MIN = 3;
TexyEditor.UNDERLINE_MAX = 20;

TexyEditor.prototype.registerHandlers = function () {
	this.editor.commands.addCommand({
		bindKey: 'Enter',
		exec: this.listHandler.bind(this)
	});
};

TexyEditor.prototype.listHandler = function () {
	var line = this.editor.getCursorPosition().row;
	var lineContent = this.document.getLine(line);

	this.editor.insert("\n");

	var endList = function () {
		this.document.removeLines(line, line);
		this.editor.insert("\n");
	}.bind(this);

	var ulMatches = lineContent.match(/^\s*([\-\*]) (.*)/);
	if (ulMatches) {
		if (ulMatches[2] === '') {
			endList();
		} else {
			this.editor.insert(ulMatches[1] + ' ');
		}
		return;
	}

	var olMatches = lineContent.match(/^\s*([\d]+)\) (.*)/);
	if (olMatches) {
		if (olMatches[2] === '') {
			endList();
		} else {
			var nextVal = parseInt(olMatches[1], 10) + 1;
			this.editor.insert(nextVal + ') ');
		}
		return;
	}

	var letterOlMatches = lineContent.match(/^\s*([a-zA-Z]+)\) (.*)/);
	if (letterOlMatches) {
		if (letterOlMatches[2] === '') {
			endList();
		} else {
			var letter = letterOlMatches[1];
			var nextLetter;
			if (letter.toLowerCase() === 'z') {
				nextLetter = letter;
			} else {
				nextLetter = String.fromCharCode(letter.charCodeAt(0) + 1);
			}

			this.editor.insert(nextLetter + ') ');
		}
	}
};

TexyEditor.prototype.tag = function (startText, endText) {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();
	var selectionRange = this.editor.getSelectionRange();
	var newText = startText + text + endText;

	this.document.replace(selectionRange, newText);

	if (text) {
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column + startText.length + endText.length);
		this.editor.selection.setSelectionRange(selectionRange);
	} else {
		this.editor.selection.clearSelection();
		this.editor.moveCursorTo(selectionRange.start.row, selectionRange.start.column + startText.length);
	}

	this.editor.focus();
};

TexyEditor.prototype.phrase = function (text) {
	this.tag(text, text);
};

TexyEditor.prototype.bold = function () {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();

	var matches = text.match(/^\*\*(.*)\*\*$/);

	if (matches) {
		var selectionRange = this.editor.getSelectionRange();
		this.document.replace(selectionRange, matches[1]);
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 4);
		this.editor.selection.setSelectionRange(selectionRange);
		this.editor.focus();
	} else {
		this.phrase('**');
	}
};

TexyEditor.prototype.italics = function () {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();

	if (text.match(/^\*\*\*.*\*\*\*$/) || text.match(/^\*[^*]+\*$/)) {
		var selectionRange = this.editor.getSelectionRange();
		this.document.replace(selectionRange, text.substring(1, text.length - 1));
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 2);
		this.editor.selection.setSelectionRange(selectionRange);
		this.editor.focus();
	} else {
		this.phrase('*');
	}
};

TexyEditor.prototype.heading = function (level) {
	var line = this.editor.getCursorPosition().row;
	var headingText = this.document.getLine(line);
	var emptyLine = headingText === "";

	// is already heading
	if (headingText) {
		var nextLine = this.document.getLine(line + 1);
		var matches = nextLine.match(/^([\*=\-]){3,}$/);
		if (matches) {
			this.document.removeLines(line + 1, line + 1);
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

	this.document.insertLines(line + (emptyLine ? 0 : 1), insertLines);

	this.select(line, 0, line, headingText.length);
	this.editor.focus();
};

TexyEditor.prototype.select = function (startRow, startColumn, endRow, endColumn) {
	var Range = ace.require("ace/range").Range;

	this.editor.selection.setSelectionRange(new Range(startRow, startColumn, endRow, endColumn));
};