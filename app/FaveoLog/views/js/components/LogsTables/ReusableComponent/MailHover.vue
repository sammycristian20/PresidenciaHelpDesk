<template>

    <div id="main" v-on:mouseenter="popOver(data.id)" v-on:mouseleave="popOver(null)">

        <a v-for="element in formattedData[objectKey]" id="action_mail" href="javascript:;" @click="getData(element, formattedData.id)" v-popover:right>{{element}}&nbsp;&nbsp;

            <popover id="action_mail_popover" v-if="popId == data.id" event="click" name="right" style="top: !42px;left:!927px;width:289px">

                <div v-if="showAlert === true && loading === false"><p class="text-muted">{{err_message}}</p></div>

                <div v-if="loading === true">

                    <loader :animation-duration="4000" color="#1d78ff" :size="30"/>
                </div>

                <div v-if="hasDataPopulated === true && loading === false && showAlert === false" class="box-widget widget-user-2 cus-container">

                    <div class="widget-user-header bg-info custom-head">

                        <div class="widget-user-image">

                            <faveo-image-element id="user_img" :source-url="user_data.profile_pic" :classes="['img-circle w-40','img-responsive']" alternative-text="User Image" :img-width="40" :img-height="40"/>

                            <a :href="user_data.profile_link" target="_blank"><i v-tooltip="lang('view')" class="fas fa-eye float-right cus-icon"></i></a>
                        </div>

                        <h3 class="widget-user-username custom-name"><span v-tooltip="user_data.name">{{subString(user_data.name)}}</span></h3>
                    </div>

                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                          <li class="nav-item">
                            <a href="javascript:;" class="nav-link text-dark">
                              {{lang('email')}} <span class="float-right" v-tooltip="user_data.email">{{subString(user_data.email)}}</span>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a href="javascript:;" class="nav-link text-dark">
                              {{lang('Role')}} <span class="float-right" v-tooltip="user_data.role">{{subString(user_data.role)}}</span>
                            </a>
                          </li>
                        </ul>
                      </div>
                </div>
            </popover>
        </a>
    </div>
</template>

<script>

    import axios from 'axios';

    import { getSubStringValue } from 'helpers/extraLogics';

    import Vue      from 'vue'

    import Popover from 'vue-js-popover'

    Vue.use(Popover)

    import { mapGetters } from 'vuex'

    export default {

        name:"mail-hover",

        description: "Mail hover component",

        props: {

            data : { type : Object, required : true },

            /**
             * Key in the data prop which has to be made hoverable
             */
            objectKey: {type: String, required: true}
        },

        data(){

            return {

                popId : '',

                user_data : {},

                base : '',

                loading : true,

                hasDataPopulated : false,

                showAlert : false,

                err_message : ''
            }
        },

        computed:{
            ...mapGetters(['getUserData']),

            formattedData(){
                if(!Array.isArray(this.data[this.objectKey])) {
                    // cloning the prop to avoid prop mutation
                    let tempDataHolder = Object.assign({}, this.data);
                    tempDataHolder[this.objectKey] = [this.data[this.objectKey]];
                    return tempDataHolder;
                }

                return this.data;
            }
        },

        watch : {
            popId(newValue,oldValue){
                return newValue
            },

            getUserData(newValue,oldValue){
                this.base = newValue.system_url
                return newValue
            }
        },

        beforeMount(){
            if(this.getUserData.system_url){
                this.base = this.getUserData.system_url
            }
        },

        methods:{

            popOver(x) {
                this.popId = x;
            },

            getData(mail,id){

                this.user_data = ''

                this.showAlert = false

                this.loading = true,

                    this.popId = id

                axios.get('/api/get-user-by-email?email='+mail).then(res=>{

                    this.showAlert = false

                    this.user_data = res.data.data.user;

                    this.loading = false

                    this.hasDataPopulated = true

                }).catch(err=>{

                    this.loading = false

                    this.showAlert = true

                    this.err_message = err.response.data.message
                })
            },

            subString(name){
                return getSubStringValue(name,30)
            }
        },

        components:{

            'loader':require('components/Client/Pages/ReusableComponents/Loader'),
            'faveo-image-element': require('components/Common/FaveoImageElement')
        }
    };
</script>

<style type="text/css">
    .user-img{
        margin: auto;
        border: 1px solid #d2d6de;
        border-radius: 50px;
        width: 40px !important;
        height: 40px !important;
    }
    .custom-head {
        padding: 7px !important;
        min-height: 12vh !important;
    }
    .custom-name {
        margin-top: 11px !important;
        margin-left: 45px !important;
        font-size: 15px !important;
    }
    .cus-icon { color : white !important; }

    .w-40{ width: 40px !important; }

    .info-body { padding: 15px !important; padding-left: 7px !important;}

    .vue-popover { padding: 1px !important; }
</style>