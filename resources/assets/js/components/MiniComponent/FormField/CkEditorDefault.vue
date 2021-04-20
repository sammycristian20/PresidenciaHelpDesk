<template>
        <div id="newtextarea">
                <textarea  :name="name" class="form-control" :id="name" v-model="value"></textarea>
        </div>
</template>
<script>
   export default{

        props : {
            name : {type : String ,default : 'reply_content'},
            value : {type : String ,default : ''}
        },

        data(){
         	 return{
            base : ''
         	 }
         },
         watch:{
            value(newValue,oldValue){
                return newValue
            }
         },
         mounted(){
         	CKEDITOR.replace(this.name, {
            uiColor: '#ffffff',
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
          CKEDITOR.config.width = '100%';
          CKEDITOR.config.scayt_autoStartup = true;
          CKEDITOR.config.extraPlugins = 'font,bidi,colorbutton,autolink,colordialog';
          CKEDITOR.config.menu_groups='tablecell,tablecellproperties,tablerow,tablecolumn,table,' +'anchor,link,image,flash';
        }
      };
</script>
<style>
    .cke_bottom {
         border-top: 0px solid #bfbfbf !important; 
    }
    .cke_chrome {
        border: 1px solid #c3c0c0 !important;
    }
</style>