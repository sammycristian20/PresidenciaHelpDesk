<template>
        <!-- if checkStatus is one, this becomes visible using css transitions-->
        <div :class="['row', 'protocol',{'protocol-settings': sending_status == 1}]">

            <!--send-->
            <static-select :label="lang('transfer_protocol')"  :elements="sending_protocols"
               name="sending_protocol" :value="sending_protocol" classname="col-sm-3" :onChange="onChange" :required="true">
            </static-select>

            <!--fetch , send in SMTP-->
            <text-field v-if="hasHost" :label="lang('host_name')" :value="sending_host" name="sending_host"
             classname="col-sm-3":onChange="onChange" :required="true">
            </text-field>

            <!--sending ports -->
            <text-field id="sending-port" v-if="shallPortBeVisible" :label="lang('port_number')" :value="sending_port" name="sending_port" classname="col-sm-3"
                :onChange="onChange" type="number" :required="true">
            </text-field>

            <!--sending encryptions -->
            <static-select id="sending-encryption" v-if="shallEncryptionBeVisible" :label="lang('encryption')"  :elements="sending_encryptions" name="sending_encryption"
               :value="sending_encryption" classname="col-sm-3" :onChange="onChange" :required="true">
            </static-select>

            <static-select id="version" v-if="shallVersionBeVisible" :label="lang('version')"  :elements="versions" name="version"
                           :value="version" classname="col-sm-3" :onChange="onChange" :required="true">
            </static-select>

            <text-field v-if="hasApiKey" :label="lang('api_key')" :value="api_key" name="api_key" classname="col-sm-3"
                        :onChange="onChange" :required="true"></text-field>

            <text-field v-if="hasSecret" :label="lang('secret')" :value="secret" name="secret" classname="col-sm-3"
                        :onChange="onChange" :required="true"></text-field>

            <text-field v-if="hasDomain" :label="lang('domain')" :value="domain" name="domain" classname="col-sm-3"
                        :onChange="onChange" :required="true"></text-field>

            <text-field v-if="hasRegion" :label="lang('region')" :value="region" name="region" classname="col-sm-3"
                        :onChange="onChange" :required="true"></text-field>

        </div>

</template>

<script>

    import {
      shallApiKeyBeVisible,
      shallSecretBeVisible,
      shallDomainBeVisible,
      shallRegionBeVisible,
      shallSendingHostBeVisible,
      shallSendingPortBeVisible,
      shallSendingEncryptionBeVisible,
      shallVersionBeVisible
    } from "helpers/validator/emailSettingsRules"

    export default {
        name:'sending-protocol',

        description:'contains sending protocol form fields for email settings',

        props:['onChange','sending_protocol','sending_encryption','sending_host','sending_port','sending_status',
            'api_key','domain','secret','region', 'version'],

        data(){
            return {

                /**
                 * list of protocols available for sending mails
                 * @type {Array}    array of object with `id` as the value that has to be sent to backend
                 *                   and name as the text that has to be displayed
                 */
                sending_protocols:[
                    { id:"smtp",name:"SMTP" },
                    { id:"mail",name:"Php mail" },
                    { id:"mailgun",name:"Mailgun" },
                    { id:"mandrill",name:"Mandrill" },
                    { id:"ses",name:"SES" },
                    { id:"sparkpost",name:"Sparkpost" },
                    { id:"ews",name:"Exchange Web Services" },
                ],

                /**
                 * list of encryptions available for sending mails
                 * @type {Array}    array of object with `id` as the value that has to be sent to backend
                 *                   and name as the text that has to be displayed
                 */
                sending_encryptions:[
                    //no encryption
                    { id:'none', name:"None" },
                    { id:"ssl", name:"SSL" },
                    { id:"tls",name:"TLS" },
                    { id:"starttls", name:"STARTTLS" }
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

        computed:{

            /**
             * Checks whether selected protocol has API key field or not
             * @return {Boolean}
             */
            hasApiKey(){
                return shallApiKeyBeVisible(this.$props)
            },

            /**
             * Checks whether selected protocol has secret field or not
             * @return {Boolean}
             */
            hasSecret(){
                return shallSecretBeVisible(this.$props)
            },

            /**
             * Checks whether selected protocol has domain field or not
             * @return {Boolean}
             */
            hasDomain(){
                return shallDomainBeVisible(this.$props)
            },

            /**
             * Checks whether selected protocol has region field or not
             * @return {Boolean}
             */
            hasRegion(){
                return shallRegionBeVisible(this.$props)
            },

            /**
             * Checks whether selected protocol has host field or not
             * It will have host field only for SMTP
             * @return {Boolean}
             */
            hasHost(){
                return shallSendingHostBeVisible(this.$props);
            },

            shallPortBeVisible(){
              return shallSendingPortBeVisible(this.$props);
            },

            shallEncryptionBeVisible(){
              return shallSendingEncryptionBeVisible(this.$props);
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

<style scoped>
    .protocol {
        max-height: 0px !important;
        transition: max-height 0.25s ease-out;
        overflow: hidden;
    }

    .protocol.protocol-settings {
        max-height: 500px !important;
        transition: max-height 0.25s ease-in;
    }

</style>
