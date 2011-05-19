/***************************************************************
* FileUploadWidget - Gui Object
* 
* File upload interface which uses a third-party file uploader 
* (HTML5 / Flash compatible) to upload and manage files based on 
* the contents of a "File"-type field (which stores files as 
* comma-separated filenames in a BLOB field).
* 
* Can be subclassed to allow overriding the file upload controller, 
* the on-response actions, the management privileges and the type 
* of fields used to store filenames (CSV or array)
* 
***************************************************************/

dk.wildside.display.widget.FileUploadWidget = function(jQueryElement) { };