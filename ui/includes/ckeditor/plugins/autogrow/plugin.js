/**
 * CkEditor autogrow plugin
 * 
 * @version 0.9.6
 *
 * @author Alexander Makarov
 * @author Eugenia Makarova
 *
 * @link http://rmcreative.ru/
 *
 */
(function() {
    var CKAutoGrow = {
        editor: null,
        wrapper: null,

        check : function() {
            if (!CKAutoGrow.editor.document) return;
            if (CKAutoGrow.editor.container.getChild(0).hasClass('cke_maximized')) return;
            
            var newHeight = CKAutoGrow.getHeight();
            newHeight = CKAutoGrow.getEffectiveHeight(newHeight);

            if(CKAutoGrow.wrapper || (CKAutoGrow.wrapper = document.getElementById('cke_contents_' + CKAutoGrow.editor.name))){
                CKAutoGrow.wrapper.style.height = newHeight + "px";
            }
        },

        getEffectiveHeight : function(height) {
            var minHeight = CKAutoGrow.editor.config.minHeight ? CKAutoGrow.editor.config.minHeight : false;
            var maxHeight = CKAutoGrow.editor.config.maxHeight ? CKAutoGrow.editor.config.maxHeight : false;

            if (minHeight && height <= minHeight) {
                height = minHeight;
            }
            else if(maxHeight && height > maxHeight) {
                height = maxHeight;                
            }
            
            else {
                // TODO: what height do we need not to show scrollbar?
                height+=54;
            }
            
            return height;
        },

        getHeight : function() {
            //TODO: if(CKAutoGrow.editor.document.compatMode!='CSS1Compat'){
            //ie in quirks mode → scrollHeight? 
            return CKAutoGrow.editor.document.getBody().$.clientHeight;
        },

        setListeners : function(editor) {            
            editor.on('contentDom', CKAutoGrow.check);
            editor.on('key', CKAutoGrow.check);
            editor.on('selectionChange', CKAutoGrow.check);
            editor.on('insertElement', function(){
                setTimeout(CKAutoGrow.check, 1000);
            });
        }
    };


    CKEDITOR.plugins.add('autogrow', {
        init : function(editor) {
            CKAutoGrow.editor = editor;

            // need this if we are destroying and then recreating editor on the same page
            CKAutoGrow.wrapper = document.getElementById('cke_contents_' + CKAutoGrow.editor.name);

            CKAutoGrow.setListeners(editor);
        }
    });
})();
