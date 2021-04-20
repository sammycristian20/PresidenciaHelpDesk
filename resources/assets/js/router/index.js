import Router from 'vue-router'

import Vue from 'vue'

import {store} from 'store'

import Home from 'components/Client/Pages/Index'

import Login from 'components/Client/Pages/Auth/LoginPage'

import ForgotPassword from 'components/Client/Pages/Auth/ForgotPassword'

import PasswordReset from 'components/Client/Pages/Auth/PasswordReset'

import EmailActivation from 'components/Client/Pages/EmailActivation'

import EmailVerify from 'components/Client/Pages/EmailVerify'

import UserProfile from 'components/Client/Pages/UserProfile'

import Knowledgebase from 'components/Client/Pages/Kb/Knowledgebase'

import ArticleList from 'components/Client/Pages/Kb/KbArticlesList'

import CategoryList from 'components/Client/Pages/Kb/KbCategoryList'

import kbPages from 'components/Client/Pages/Kb/KbPages'

import TagsById from  'components/Client/Pages/Kb/KbTagDetails'

import ArticleById from  'components/Client/Pages/Kb/KbArticleDetails'

import CategoryById from  'components/Client/Pages/Kb/KbCategoryDetails'

import SearchResult from 'components/Client/Pages/Kb/KbSearchResult'

import MyTickets from 'components/Client/Pages/Tickets/MyTicketsIndex'

import CheckTicket from 'components/Client/Pages/Tickets/ClientTicketTimeline'

import ShowTicket from 'components/Client/Pages/Tickets/ShowTicket'

import Organization from 'components/Client/Pages/Tickets/Organization'

import CreateUser from 'components/Client/Pages/Tickets/OrganizationMembersCreate'

import EditUser from 'components/Client/Pages/Tickets/OrganizationMembersCreate'

import Register from 'components/Client/Pages/Auth/Register'

import ClientPanelCreateTicket from 'components/Client/Pages/Tickets/ClientPanelCreateTicket'

import ApprovalTicketConversation from 'components/Client/Pages/ApprovalTicketConversation/ApprovalTicketConversation.vue'

import TicketConversation from 'components/Client/Pages/Tickets/TicketConversation.vue';

import TicketRating from 'components/Client/Pages/TicketRating/TicketRating.vue'

import NotFound from 'components/Client/Pages/NotFound/404'

import NotFoundComponent from 'components/Client/Pages/NotFound/NotFoundComponent'

import Verify2FA from 'components/Client/Pages/Auth/Verify2FA'

import ServerError from 'components/Client/Pages/NotFound/500'

/***********************************
* Billing
*/
import OrderInfo from "components/Client/Billing/OrderInfo";

import PackageList from "components/Client/Billing/PackageList";

import UserInvoice from "components/Client/Billing/UserInvoice";

import UserPackage from "components/Client/Billing/UserPackage";

import PackageView from "components/Client/Billing/PackageView";

import CheckoutPage from "components/Client/Billing/CheckoutPage";

import InvoiceView from "components/Client/Billing/InvoiceView";

Vue.use(Router)

Vue.config.productionTip = false;

const router = new Router({
  
    routes: [
    
        { 
            path: '/',
            component: Home,
            name: 'Home',
            meta: { breadcrumb: [{text:'home'}]}
        },

        { 
            path: '/auth/login/',
            component: Login,
            name: 'Login',
            meta: { breadcrumb: [{to:'/',text:'home'},{to:'/auth/register',text:'create_account'},{text:'login'}] }
        },
        {
            path: '/social/login/:provider',
            component: Login,
            name: 'SocialCallback',
            meta: { breadcrumb: [{to:'/',text:'home'},{to:'/auth/register',text:'create_account'},{text:'login'}] }

        },        
        {
            path: '/auth/register', component: Register, name: 'Register',
            meta: { breadcrumb: [{ to: '/', text: 'home' }, { text: 'register' }] }
        },

        {
            path: '/create-ticket', component: ClientPanelCreateTicket, name: 'ClientPanelCreateTicket',
            meta: { breadcrumb: [{ to: '/', text: 'home' }, { text: 'submit_a_ticket' }] }
        },

        {
            path: '/password/email',
            component: ForgotPassword,
            name: 'ForgotPassword',
            meta: { breadcrumb: [{text:'forgot_password'}]}
        },

        {
            path: '/reset/password/:id',
            component: PasswordReset,
            name: 'PasswordReset',
            meta: {
                breadcrumb: [{text:'reset_password'}]
            }
        },

        {
            path: '/client-profile',
            component: UserProfile,
            name: 'UserProfile',
            beforeEnter: requireAuth,
            meta: {
                breadcrumb: [{text:'my_profile'}],
            }
        },

        {
            path: '/knowledgebase',
            component: Knowledgebase,
            name: 'Knowledgebase',
            meta: { breadcrumb: [{text:'knowledge_base'}]}
        },

        {
            path: '/category-list',
            component: CategoryList,
            name: 'CategoryList',
            meta: {
                breadcrumb: [{to:'/knowledgebase',text:'knowledge_base'},{text:'Category List'}]
            }
        },

        {
            path: '/article-list',
            component: ArticleList,
            name: 'ArticleList',
            meta: {
                breadcrumb: [{to:'/knowledgebase',text:'knowledge_base'},{text:'Article List'}]
            }
        },

        {
            path: '/show/:slug',
            component: ArticleById,
            name: 'Articles',
            meta: {
                page : 'article',
                breadcrumb: [{text:'knowledge_base',to:'/knowledgebase'}, {text:'Article List',to:'/article-list'}]
            }
        },

        {
            path: '/category-list/:slug',
            component: CategoryById,
            name: 'Category',
            meta: {
                breadcrumb: [{text:'knowledge_base', to:'/knowledgebase'}, {text:'Category List',to:'/category-list'}]
            }
        },

        {
            path: '/kb-tag-articles/:tag_id',
            component: TagsById,
            name: 'Tags',
            meta: {
                breadcrumb: [{text:'knowledge_base', to:'/knowledgebase'}, {text:'Tag'}]
            }
        },

        {
            path: '/organization/:org_id/',
            component: Organization,
            name : 'Organizations',
            beforeEnter: requireAuth,
            props:true,
            meta: {
                breadcrumb:  [{to:'/',text:'home'},{text:'Organization'}],
            }
        },

        {
            path: '/create/user',
            component: CreateUser,
            name : 'Create User',
            beforeEnter: requireAuth,
            props:true,
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'create_user'}],
            }
        },

        {
            path: '/edit/user/:id',
            component: EditUser,
            name : 'EditUser',
            beforeEnter: requireAuth,
            props:true,
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'edit_user'}],
            }
        },

        {
            path: '/pages/:slug',
            component: kbPages,
            name : 'Pages',
            props:true,
            meta: {
                breadcrumb: [{to:'/knowledgebase',text:'knowledge_base'},{text:'Pages'}]
            }
        },

        {
            path: '/mytickets',
            component: MyTickets,
            name: 'MyTickets',
            beforeEnter: requireAuth,
            props: true,
            meta: {
                breadcrumb: [{text:'my_tickets'}],
            }
        },

        {
            path: '/check-ticket/:id',
            component: CheckTicket,
            name: 'CheckTicket',
            beforeEnter: requireAuth,
            meta: {
                breadcrumb: [{to:'/mytickets',text:'myticket'},{text:'check_ticket'}],
            }
        },

        {
            path: '/show-ticket/:hash',
            component: ShowTicket,
            name: 'ShowTicket',
            meta: {
                breadcrumb: [{text:'show_ticket'}],
            }
        },

        {
            path: '/search',
            component: SearchResult,
            name: 'SearchResult',
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'Search Results'}]
            }
        },

        {
            path: '/ticket-conversation/:id',
            component: ApprovalTicketConversation,
            name: 'ApprovalTicketConversation',
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'Approval Ticket Conversation'}]
            }
        },

        {
            path: '/ticket-conversation-guest/:id',
            component: TicketConversation,
            name: 'TicketConversation',
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'Ticket Conversation'}]
            }
        },

        {
            path: '/rating/:id/:rate',
            component: TicketRating,
            name: 'TicketRating',
            meta: {
                breadcrumb: [{to:'/',text:'home'},{text:'Ticket Rating'}]
            }
        },


        {
            path: '/verify-email',
            name:"EmailVerify",
            component: EmailVerify,
            props:true,
            meta: {
                breadcrumb: [{text:'Email Verification'}]
            }
        },

         {
            path: '/verify-2fa',
            name:"Verify2FA",
            component: Verify2FA,
            props:true,
            meta: {
                breadcrumb: [{text:'Two Factor Verification'}]
            }
        },

        { 
            path: '*',
            name:"404",
            component: NotFound,
            meta: {breadcrumb: [{text:'404'}]}
        },
        
        { 
            path: '/server-error',
            name:"ServerError",
            component: ServerError,
            meta: {breadcrumb: [{text:'500'}]}
        },

        { 
            path: '/not-found',
            name:"NotFound",
            component: NotFoundComponent, 
            props : true,
            meta: {breadcrumb: [{text:'404'}]}
        },

        { 
            path: '/account/activation/:id',
            name:"EmailActivation",
            component: EmailActivation,
            props:true,
            meta: {breadcrumb: [{text:'Email Activation'}]}
        },

        // For client bilinng module
        
        { 
            path: '/billing-user-packages',
            component: UserPackage,
            beforeEnter: requireAuth, 
            name: 'MyPackaage',
            meta: { breadcrumb: [{to:'/', text:'home'},{text:'My Package'}]}
        },
        
        { 
            path: '/billing-user-invoices',
            component: UserInvoice, 
            beforeEnter: requireAuth,
            name: 'UserInvoice',
            meta: { breadcrumb: [{to:'/', text:'home'},{text:'My Invoices'}]}
        },
        
        {   
            path: '/billing-order-info/:id',
            component: OrderInfo,  
            beforeEnter: requireAuth,
            name: 'OrderInfo',
            meta: { breadcrumb: [{to:'/', text:'home'},{text:'Order info'}]}
        },
        
        { 
            path: '/billing-package-list',
            component: PackageList, 
            name: 'PackageList',
            meta: { breadcrumb: [{to:'/', text:'home'},{text:'Packages'}] }
        },
        
        {   
            path: '/package/:id', 
            component: PackageView,
            beforeEnter: requireAuth, 
            name: 'PackageView',
            meta: { breadcrumb: [{to:'/',text:'home'}, {text:'Package'}]}
        },
        
        {   
            path: '/checkout/:id', 
            component: CheckoutPage, 
            beforeEnter: requireAuth,
            name: 'CheckoutPage', props: true,
            meta: { breadcrumb: [{to:'/',text:'home'}, {text:'checkout'}]}
        },
        
        {   
            path: '/invoice/:id', 
            component: InvoiceView,
            beforeEnter: requireAuth, 
            name: 'InvoiceView',
            meta: { breadcrumb: [{to:'/',text:'home'}, {text:'Invoice'}]}
        },
    ],

    mode : 'history',
      
    linkActiveClass: '',
      
    linkExactActiveClass :'active',
})


/*
    This will cehck to see if the user is authenticated or not.
*/
function requireAuth (to, from, next) {
    
    store.watch((state, getters) => getters.getUserLoadStatus, (newValue, oldValue) => {

        if (newValue) {
            
            if( !Array.isArray(store.getters.getUserData.user_data) != '' ){
                
                next();
                    
            }else{
                next('/auth/login');
            }
        }
      });

    next();
}

export default router