/*(function(){
 
    //tinymce.PluginManager.requireLangPack('idmce');
 
    tinymce.create('tinymce.plugins.idmce', {
 
        init : function(ed, url){
            ed.addCommand('idmcePHP', function(){
                ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
                tinyMCE.activeEditor.selection.setContent('1');
            });
            ed.addButton('idmce_buttonPHP', {
                title: 'ilcsyntax.php',
                image: url + '/php.png',
                cmd: 'mceilcPHP'
            });
            //ed.addShortcut('alt+ctrl+x', ed.getLang('idmce.php'), 'idmcePHP');
        },
        createControl : function(n, cm){
            return null;
        },
        getInfo : function(){
            return {
                longname: 'IgnitionDeck Shortcodes',
                author: 'Virtuous Giant',
                authorurl: 'http://VirtuousGiant.com/',
                infourl: 'http://IgnitionDeck.com',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('idmce', tinymce.plugins.idmce);
})();*/