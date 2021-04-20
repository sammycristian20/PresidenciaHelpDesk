<template>
        <div id="newtextarea">
                <textarea  name="reply_content" class="form-control" id="reply_content"></textarea>
        </div>
</template>
<script>
   export default{
         props:['editorWidth'],
         data(){
         	 return{

         	 }
         },
         mounted(){
         	CKEDITOR.replace('reply_content', {
                     toolbarGroups: [
                                        {"name":"basicstyles", "groups":["basicstyles"]},
                                        {"name":"links", "groups":["links"]},
                                        {"name":"paragraph", "groups":["list", "blocks"]},
                                        {"name":"document", "groups":["mode"]},
                                        {"name":"insert", "groups":["insert"]},
                                        {"name":"styles", "groups":["styles"]},
                                        {"name": "colors", "groups": ["TextColor", "BGColor"]}
                                    ],
                                    removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,Image',
                                    removePlugins: 'liststyle,tabletools,scayt,menubutton,contextmenu,wsc',
                                    disableNativeSpellChecker: false
                                });
            CKEDITOR.config.width = this.editorWidth;
            CKEDITOR.config.scayt_autoStartup = true;
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.config.extraPlugins = 'font,bidi,colorbutton,autolink,colordialog,uploadimage';
            CKEDITOR.config.uploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json';
            CKEDITOR.config.menu_groups = 'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +'anchor,link,image,flash';
         }
   }
</script>