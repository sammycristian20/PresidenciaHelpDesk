webpackJsonp([19],{

/***/ 2907:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2908);


/***/ }),

/***/ 2908:
/***/ (function(module, exports) {

webpackJsonp([15], { 254: function _(t, e, a) {
    var s = a(0)(a(3490), a(3491), !1, function (t) {
      a(3488);
    }, "data-v-5d16a5a8", null);t.exports = s.exports;
  }, 3486: function _(t, e, a) {
    t.exports = a(3487);
  }, 3487: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var s = a(11),
        n = a(29);n.injectComponentIntoView("alliance-customer-timeline", a(254), "ticket-timeline-boxes-mounted", "timeline-boxes"), n.injectComponentIntoView("alliance-customer-profile", a(254), "user-box-mounted", "user-view-table");new Vue({ el: "#alliance-crm-settings", store: s.a, components: { "crm-settings": a(3492) } });
  }, 3488: function _(t, e, a) {
    var s = a(3489);"string" == typeof s && (s = [[t.i, s, ""]]), s.locals && (t.exports = s.locals);a(2)("42893470", s, !0, {});
  }, 3489: function _(t, e, a) {
    (t.exports = a(1)(!1)).push([t.i, ".info-row[data-v-5d16a5a8]{border-bottom:1px solid #d8d8d8;padding:10px}", ""]);
  }, 3490: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var s = a(3),
        n = a.n(s);e.default = { name: "alliance-customer-timeline", props: { data: { type: String, required: !0 } }, data: function data() {
        return { showUserData: !1, customerDataFields: {}, loading: !0, userData: null };
      }, beforeMount: function beforeMount() {
        this.userData = JSON.parse(this.data), this.getCustomerFromCRM();
      }, created: function created() {
        window.eventHub.$on("ticket-timeline-boxes-mounted", this.updateUserData);
      }, methods: { updateUserData: function updateUserData(t) {
          this.userData.user.user_name != t.user.user_name && (this.userData = t, this.getCustomerFromCRM());
        }, getCustomerFromCRM: function getCustomerFromCRM() {
          var t = this,
              e = this.userData.user.user_name;this.showUserData = !1, this.loading = !0, n.a.get("/alliance-crm/api/search-customer/" + e).then(function (e) {
            t.showUserData = !0, t.customerDataFields = e.data.data, t.loading = !1;
          }).catch(function (e) {
            t.loading = !1;
          });
        }, ucfirst: function ucfirst(t) {
          return t.charAt(0).toUpperCase() + t.slice(1);
        } }, components: { "faveo-box": a(18), loader: a(8) } };
  }, 3491: function _(t, e) {
    t.exports = { render: function render() {
        var t = this,
            e = t.$createElement,
            a = t._self._c || e;return a("faveo-box", { attrs: { title: t.trans("alliance_crm_infomation") } }, [a("template", { slot: "customActions" }, [a("div", { staticClass: "card-tools" }, [a("a", { directives: [{ name: "tooltip", rawName: "v-tooltip", value: t.trans("refresh"), expression: "trans('refresh')" }], staticClass: "btn-tool", attrs: { href: "javascript:;" }, on: { click: t.getCustomerFromCRM } }, [a("i", { staticClass: "fas fa-sync-alt" })])])]), t._v(" "), t.loading ? a("div", { staticClass: "row" }, [a("loader", { attrs: { "animation-duration": 4e3, size: 60 } })], 1) : a("div", [a("div", { staticClass: "row" }, [a("div", { staticClass: "col-md-12" }, [t.showUserData ? a("div", { staticClass: "row" }, t._l(t.customerDataFields, function (e, s) {
          return a("div", { staticClass: "col-md-6 info-row" }, [a("div", { staticClass: "col-md-6" }, [a("label", [t._v(t._s(t.ucfirst(s)))])]), t._v(" "), a("div", { staticClass: "col-md-6" }, [t._v("\n                            " + t._s(e) + "\n                        ")])]);
        })) : a("div", [t._v("\n                    User not found on CRM or system is unable to load data at the moment\n                ")])])])])], 2);
      }, staticRenderFns: [] };
  }, 3492: function _(t, e, a) {
    var s = a(0)(a(3495), a(3496), !1, function (t) {
      a(3493);
    }, "data-v-7e52336f", null);t.exports = s.exports;
  }, 3493: function _(t, e, a) {
    var s = a(3494);"string" == typeof s && (s = [[t.i, s, ""]]), s.locals && (t.exports = s.locals);a(2)("45ca247a", s, !0, {});
  }, 3494: function _(t, e, a) {
    (t.exports = a(1)(!1)).push([t.i, ".ml_10[data-v-7e52336f]{margin-left:-10px!important}", ""]);
  }, 3495: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var s = a(3),
        n = a.n(s),
        i = a(5),
        r = a(11),
        o = a(16),
        l = a(4);e.default = { name: "crm-settings", description: "Alliance CRM Settings page", data: function data() {
        return { loading: !0, hasDataPopulated: !1, username: "", password: "", source: "", app_id: "", app_secret: "", radioOptions: [{ name: "yes", value: 1 }, { name: "no", value: 0 }], required_for_agents: 1, required_for_clients: 1 };
      }, beforeMount: function beforeMount() {
        this.getInitialValues();
      }, methods: { getInitialValues: function getInitialValues() {
          var t = this;this.loading = !0, n.a.get("/alliance-crm/api/settings").then(function (e) {
            t.loading = !1, t.hasDataPopulated = !0, t.updatesStateWithData(e.data.data);
          }).catch(function (e) {
            t.loading = !1, t.hasDataPopulated = !0;
          });
        }, updatesStateWithData: function updatesStateWithData(t) {
          var e = this,
              a = this.$data;Object.keys(t).map(function (s) {
            a.hasOwnProperty(s) && (e[s] = t[s]);
          });
        }, isValid: function isValid() {
          var t = function (t) {
            var e = { username: [t.username, "isRequired"], password: [t.password, "isRequired"], source: [t.source, "isRequired"] },
                a = new o.Validator(l.s).validate(e),
                s = a.errors,
                n = a.isValid;return r.a.dispatch("setValidationError", s), { errors: s, isValid: n };
          }(this.$data),
              e = (t.errors, t.isValid);return e;
        }, onChange: function onChange(t, e) {
          this[e] = t;
        }, onSubmit: function onSubmit() {
          var t = this;if (this.isValid()) {
            this.loading = !0;var e = {};e.username = this.username, e.password = this.password, e.source = this.source, e.app_id = this.app_id || null, e.app_secret = this.app_secret || null, e.required_for_agents = this.required_for_agents, e.required_for_clients = this.required_for_clients, n.a.post("/alliance-crm/api/settings", e).then(function (e) {
              t.loading = !1, t.hasDataPopulated = !0, Object(i.b)(e, "crm-settings"), t.getInitialValues();
            }).catch(function (e) {
              t.loading = !1, t.hasDataPopulated = !0, Object(i.a)(e, "crm-settings");
            });
          }
        } }, components: { "text-field": a(12), "faveo-box": a(18), alert: a(6), "custom-loader": a(9), "radio-button": a(21) } };
  }, 3496: function _(t, e) {
    t.exports = { render: function render() {
        var t = this,
            e = t.$createElement,
            a = t._self._c || e;return a("div", [a("alert", { attrs: { componentName: "crm-settings" } }), t._v(" "), t.loading ? a("div", [a("custom-loader", { attrs: { loadingSpeed: 4e3 } })], 1) : t._e(), t._v(" "), t.hasDataPopulated ? a("faveo-box", { attrs: { title: t.trans("alliance_crm_integration") } }, [a("div", { staticClass: "row" }, [a("text-field", { staticClass: "col-sm-6", attrs: { label: t.trans("user_name"), type: "text", value: t.username, name: "username", required: !0, onChange: t.onChange } }), t._v(" "), a("text-field", { attrs: { label: t.trans("password"), type: "password", name: "password", value: t.password, classname: "col-sm-6", required: !0, onChange: t.onChange } })], 1), t._v(" "), a("div", { staticClass: "row" }, [a("text-field", { staticClass: "col-sm-6", attrs: { label: t.trans("source"), type: "text", value: t.source, name: "source", required: !0, onChange: t.onChange } }), t._v(" "), a("text-field", { attrs: { label: t.trans("app_id"), type: "text", name: "app_id", value: t.app_id, classname: "col-sm-6", required: !1, onChange: t.onChange } })], 1), t._v(" "), a("div", { staticClass: "row" }, [a("text-field", { staticClass: "col-sm-6", attrs: { label: t.trans("app_secret"), type: "text", value: t.app_secret, name: "app_secret", required: !1, onChange: t.onChange } }), t._v(" "), a("radio-button", { attrs: { options: t.radioOptions, label: t.trans("required_for_agents"), name: "required_for_agents", value: t.required_for_agents, onChange: t.onChange, classname: "form-group col-sm-6" } }), t._v(" "), a("radio-button", { attrs: { options: t.radioOptions, label: t.trans("required_for_clients"), name: "required_for_clients", value: t.required_for_clients, onChange: t.onChange, classname: "form-group col-sm-6" } })], 1), t._v(" "), a("div", { staticClass: "card-footer", attrs: { slot: "actions" }, slot: "actions" }, [a("button", { staticClass: "btn btn-primary", attrs: { slot: "actions", type: "button", disabled: t.loading }, on: { click: function click(e) {
              t.onSubmit();
            } }, slot: "actions" }, [a("i", { staticClass: "fa fa-save" }), t._v(" " + t._s(t.trans("save")) + "\n\t\t\t")])])]) : t._e()], 1);
      }, staticRenderFns: [] };
  } }, [3486]);

/***/ })

},[2907]);