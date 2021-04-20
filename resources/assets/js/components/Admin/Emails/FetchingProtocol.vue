<template>
  
        <!-- if checkStatus is one, this becomes visible using css transitions-->
        <div :class="['protocol',{'protocol-settings': fetching_status == 1}]">
            <div class="row">
              <!--fetch-->
              <static-select :label="lang('fetching_protocol')"  :elements="fetching_protocols"
                             name="fetching_protocol" :value="fetching_protocol" classname="col-sm-3" :onChange="onChange"
                             :required="true">
              </static-select>

              <text-field :label="lang('host_name')" :value="fetching_host" name="fetching_host" classname="col-sm-3"
                          :onChange="onChange" :required="true">
              </text-field>

              <text-field id="fetching-port" v-if="shallPortBeVisible" :label="lang('port')" :value="fetching_port" name="fetching_port" classname="col-sm-3"
                          :onChange="onChange" type="number" :required="true">
              </text-field>

              <static-select id="fetching-encryption" v-if="shallEncryptionBeVisible" :label="lang('encryption')"  :elements="fetching_encryptions" name="fetching_encryption"
                             :value="fetching_encryption" classname="col-sm-3" :onChange="onChange" :required="true">
              </static-select>

              <static-select id="version" v-if="shallVersionBeVisible" :label="lang('version')"  :elements="versions" name="version"
                             :value="version" classname="col-sm-3" :onChange="onChange" :required="true">
              </static-select>
            </div>

            <div class="fetching-meta-actions">
              <!--delete mails -->
              <checkbox id="delete-email" name="delete_email" :value="delete_email" :label="lang('delete_mail_after_fetch')"
                        :onChange="onChange" classname="col-sm-4" :hint="lang('delete_mail_notes')">
              </checkbox>
            </div>

        </div>

</template>

<script>
    import {shallFetchingPortBeVisible, shallFetchingEncryptionBeVisible, shallVersionBeVisible} from "helpers/validator/emailSettingsRules";

    export default {
        name:'fetching-protocol',

        description:'contains fetching protocol form fields for email settings',

        props:['fetching_protocol','fetching_encryption','fetching_host','fetching_port','fetching_status',
            'onChange','delete_email', 'version'],

        data(){
            return {

                /**
                 * list of protocols available for fetching mails
                 * @type {Array}    array of object with `id` as the value that has to be sent to backend
                 *                   and name as the text that has to be displayed
                 */
                fetching_protocols:[
                    { id:"imap",name:"IMAP" },
                    { id:"pop3",name:"POP3" },
                    { id:"ews",name:"Exchange Web Services" }
                ],

                /**
                 * list of encryptions available for fetching mails
                 * @type {Array}    array of object with `id` as the value that has to be sent to backend
                 *                   and name as the text that has to be displayed
                 */
                fetching_encryptions:[
                    //no encyption
                    { id:'none', name:"None" },
                    { id:"ssl", name:"SSL" },
                    { id:"tls",name:"TLS" },
                    { id:"starttls", name:"STARTTLS" },
                ],

                versions: [
                  {id: "Exchange2007", name: "Microsoft Exchange 2007"},
                  {id: "Exchange2007_SP1", name: "Microsoft Exchange 2007 SP1"},
                  {id: "Exchange2009", name: "Microsoft Exchange 2007 SP2"},
                  {id: "Exchange2010", name: "Microsoft Exchange 2010"},
                  {id: "Exchange2010_SP1", name: "Microsoft Exchange 2010 SP1"},
                  {id: "Exchange2010_SP2", name: "Microsoft Exchange 2010 SP2"},
                  {id: "Exchange2013", name: "Microsoft Exchange 2013"},
                  {id: "Exchange2013_SP1", name: "Microsoft Exchange 2013 SP1"},
                  {id: "Exchange2016", name: "Microsoft Exchange 2016"},
                ],
            }
        },

        computed : {

          shallPortBeVisible(){
            return shallFetchingPortBeVisible(this.$props);
          },

          shallEncryptionBeVisible(){
            return shallFetchingEncryptionBeVisible(this.$props);
          },

          shallVersionBeVisible(){
            return shallVersionBeVisible(this.$props);
          },
        },

        components:{
            'static-select':require('components/MiniComponent/FormField/StaticSelect'),
            'text-field':require('components/MiniComponent/FormField/TextField'),
            'checkbox' :require('components/MiniComponent/FormField/Checkbox')
        },
    };
</script>

<style>
    .protocol {
        max-height: 0px !important;
        transition: max-height 0.25s ease-out;
        overflow: hidden;
    }

    .protocol.protocol-settings {
        max-height: 500px !important;
        transition: max-height 0.25s ease-in;
    }

/*    .fetching-meta-actions {
      margin: -15px;
    }*/
</style>
