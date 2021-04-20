import Vue from 'vue';

export const Ckeditor = {

    bind(el, binding, vnode) {
        //initialize ckediotr to element
        let ck = CKEDITOR.replace(el, {
            toolbarGroups: [
                { "name": "basicstyles", "groups": ["basicstyles"] },
                { "name": "links", "groups": ["links"] },
                { "name": "paragraph", "groups": ["list", "blocks"] },
                { "name": "document", "groups": ["mode"] },
                { "name": "insert", "groups": ["insert"] },
                { "name": "styles", "groups": ["styles"] }
            ],
            removeButtons: 'Subscript,Superscript,Anchor,Styles,Specialchar',
            removePlugins: 'liststyle,tabletools,scayt,menubutton,contextmenu,wsc',
            disableNativeSpellChecker: false
        });
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.extraPlugins = 'font,bidi,colorbutton,autolink,colordialog';
        CKEDITOR.config.menu_groups = 'tablecell,tablecellproperties,tablerow,tablecolumn,table,' + 'anchor,link,image,flash';
        //set value to model when ckeditor changes happen   
        ck.on('change', function () {
            el.value = ck.getData();
            // console.log(vnode.context[binding.expression], "binding expression")
            // console.log(vnode, "inside on change function");
            vnode.context[binding.expression](el.value);
        });
        // during rendering time append value to ckeditor body 
        if (el.value == '') {
            return;
        }
        else {
            ck.setData(el.value);
        }
    }
}
Vue.directive('ckeditor', Ckeditor);