<template>
  
  <div id="newtextarea">
    
    <textarea  :name="name" class="form-control" :id="name" v-model="value"></textarea>
  
  </div>

</template>

<script>
  
  import { mapGetters } from 'vuex';

  export default{

    props : {
      
      name : {type : String ,default : 'reply_content'},
      
      value : {type : String ,default : ''},

      noDropdown : { type : Boolean , default : false}
    
    },

    data(){
      
      return{
        
        base : '',
        
        lang_locale : ''
      }
    },

    computed : {
      ...mapGetters(['getUserData'])
    },
    
    watch:{
      
      value(newValue,oldValue){
        
        return newValue
      
      },

      getUserData(newValue,oldValue){
        
        this.lang_locale = newValue.user_data.user_language;
         this.rtlMethod(this.lang_locale)
        return newValue;
      }
    
    },

    beforeMount(){
      if(this.getUserData.user_data){
        this.lang_locale = this.getUserData.user_data.user_language;
        this.rtlMethod(this.lang_locale)
      }
    },

    methods : {

      rtlMethod(locale){
        if(locale=='ar') {
          setTimeout(function(){  
            $('.cke_ltr').toggleClass('cke_rtl');
            $('.cke_wysiwyg_frame').contents().find('body').css('direction','rtl');
          }, 5000); 
        }
      }
    },

    mounted(){
      
      CKEDITOR.replace(this.name, {
      
        toolbarGroups: [
          {"name":"basicstyles", "groups":["basicstyles"]},
          {"name":"links", "groups":["links"]},
          {"name": "paragraph", "groups": ["list", "blocks", "align"]},
          {"name":"document", "groups":["mode"]},
          {"name":"insert", "groups":["insert"]},
          {"name":"styles", "groups":["styles"]},
          {"name": "colors", "groups": ["TextColor", "BGColor"]}
        ],
      
        removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,Image',
      });
      CKEDITOR.config.width = '100%';
      CKEDITOR.config.scayt_autoStartup = true;
      CKEDITOR.config.allowedContent = true;
      CKEDITOR.config.removePlugins = 'liststyle,tabletools,scayt,menubutton,contextmenu';
      CKEDITOR.config.extraPlugins = 'colorbutton,font,justify,colordialog,youtube,codesnippet';
      CKEDITOR.config.menu_groups = 'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +'anchor,link,image,flash';
      
      if(this.noDropdown === false){
        var arr = [];
        axios.get('get-articles').then(res=>{
          var data = res.data.message.data;
          for(var i in data){
            arr.push([data[i].name,window.axios.defaults.baseURL+'/show/'+data[i].slug])
          }
        })
        var InternPagesSelectBox = arr;
        (function () {
          function refreshItems(sel) {
            sel.clear();
            sel.items && sel.items.forEach(function (itm) {
              sel.add(itm[0], itm[1]);
            });
          }

          CKEDITOR.plugins.add('internpage', {
            lang : [CKEDITOR.lang.detect()]
          });

          CKEDITOR.plugins.setLang('internpage', 'en', {
            internpage : {
              internpage : 'Internal page'
            }
          });

          CKEDITOR.on('dialogDefinition', function (a) {  
            var b = a.data.name,
            c = a.data.definition,
            d = a.editor;
            if (b == 'link') {
            var e = c.getContents('info');
            // c.getContents('info').get('protocol')['items'].push( ['article', window.axios.defaults.baseURL +'/show/'] );
            e.add({
              type : 'select',
              id : 'intern',
              label : 'Select article',
              'default' : '',
              style : 'width:100%',
              items : InternPagesSelectBox,
              onChange : function () {
                var f = CKEDITOR.dialog.getCurrent();
                f.setValueOf('info', 'url', this.getValue());
                f.setValueOf('info', 'protocol','http://');
              },
              setup : function (f) {
                this.allowOnChange = false;
                refreshItems(this);
                this.setValue(f.url ? f.url.url : '');
                this.allowOnChange = true;
              }
            }, 'browse');
            c.onLoad = function () {
              var f = this.getContentElement('info', 'intern');
              f.reset();
            };
          }
        });
      })();
    }
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