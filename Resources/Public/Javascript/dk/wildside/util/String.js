//Prototype function for strip_tags - removes all tag-like entities from a string.
String.prototype.strip_tags = function() { return this.replace(/<\/?[^>]+(>|$)/g, ""); };

// Strip spaces, tabs and newline characters from the beginning and end of a string.
String.prototype.trim = function() { return this.replace(/^[\n\r\s\t]+|[\n\r\s\t]+$/g, ""); };

// Pad a string on the left side
String.prototype.padLeft = function (len, char) { return new Array(len - this.length + 1).join(char || '0') + this; };

// Pad a string on the right side
String.prototype.padRight = function (len, char) { return this + new Array(len - this.length + 1).join(char || '0'); };

// Replace linebreaks with <br/>
String.prototype.nl2br = function() {
	// Remove carriage returns
	var retval = this.replace(/\r/gi, "");
	return retval.replace(/\n/gi, "<br/>");
};

// Replace <br/> with linebreaks
String.prototype.br2nl = function() { return this.replace(/\<br[^\>]*\>/gmi, "\n"); };