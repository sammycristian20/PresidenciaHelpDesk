webpackJsonp([18],{

/***/ 2909:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2910);


/***/ }),

/***/ 2910:
/***/ (function(module, exports) {

webpackJsonp([16], { 3497: function _(t, e, a) {
    t.exports = a(3498);
  }, 3498: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var r = a(14),
        n = a.n(r),
        o = a(11);a(29).injectComponentIntoView("associated-ticket-crt", a(3499), "ticket-timeline-mounted-for-crt", "timeline-boxes-crt");new n.a({ el: "#crtworflow", store: o.a, components: { "crt-settings": a(3504) } });window.eventHub.$on("workflow-action-dispatch", function (t) {
      console.log(t);t.push({ default: 1, title: "RecurTicket", display_for_agent: !0, required_for_agent: !0, label: "Create recur ticket", value: "", type: "select", unique: "recur_list_ids", options: [], api_info: "url:=api/dependency/recur-tickets??paginate=true" });
    });
  }, 3499: function _(t, e, a) {
    var r = a(0)(a(3502), a(3503), !1, function (t) {
      a(3500);
    }, null, null);t.exports = r.exports;
  }, 3500: function _(t, e, a) {
    var r = a(3501);"string" == typeof r && (r = [[t.i, r, ""]]), r.locals && (t.exports = r.locals);a(2)("0680ca71", r, !0, {});
  }, 3501: function _(t, e, a) {
    (t.exports = a(1)(!1)).push([t.i, ".ticket-number{width:25%}.ticket-number,.ticket-subject{word-break:break-word!important}.ticket-subject{width:50%}.ticket-type-parent-or-child{width:25%;word-break:break-word!important}", ""]);
  }, 3502: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var r = a(7),
        n = a(18),
        o = a.n(n),
        i = Object.assign || function (t) {
      for (var e = 1; e < arguments.length; e++) {
        var a = arguments[e];for (var r in a) {
          Object.prototype.hasOwnProperty.call(a, r) && (t[r] = a[r]);
        }
      }return t;
    };e.default = { name: "associated-ticket-crt", components: { "faveo-box": o.a, "data-table": a(15) }, data: function data() {
        return { apiUrl: "", columns: ["ticket_number", "ticket_title", "type"], options: {} };
      }, props: { data: { type: String | Object } }, computed: i({}, Object(r.b)(["formattedTime", "formattedDate"])), beforeMount: function beforeMount() {
        var t = JSON.parse(this.data);t && (this.apiUrl = "api/crtworkflow/associated-tickets?ticket_id=" + t.id);var e = this;this.options = { headings: { ticket_number: "Ticket Number", type: "Parent/Child", ticket_title: "Subject" }, columnsClasses: { ticket_number: "ticket-number", ticket_type_for_crt: "ticket-type-parent-or-child", ticket_title: "ticket-subject" }, sortIcon: { base: "glyphicon", up: "glyphicon-chevron-up", down: "glyphicon-chevron-down" }, texts: { filter: "", limit: "" }, templates: { ticket_number: function ticket_number(t, a) {
              var r = a.id;return r ? t("a", { attrs: { href: e.basePath() + "/thread/" + r, target: "_blank" } }, a.ticket_number) : "--";
            } }, sortable: ["ticket_number"], filterable: !1, pagination: { chunk: 5, nav: "fixed", edge: !0 }, requestAdapter: function requestAdapter(t) {
            return { sort_field: t.orderBy ? t.orderBy : "id", sort_order: t.ascending ? "desc" : "asc", search_term: t.query, page: t.page, limit: t.limit };
          }, responseAdapter: function responseAdapter(t) {
            var e = t.data;return { data: e.data.data, count: e.data.total };
          } };
      } };
  }, 3503: function _(t, e) {
    t.exports = { render: function render() {
        var t = this.$createElement,
            e = this._self._c || t;return e("faveo-box", { attrs: { title: this.lang("crt_associated_tickets") } }, [e("div", { attrs: { id: "crt-associated-tickets" } }, [e("data-table", { attrs: { url: this.apiUrl, dataColumns: this.columns, option: this.options } })], 1)]);
      }, staticRenderFns: [] };
  }, 3504: function _(t, e, a) {
    var r = a(0)(a(3505), a(3506), !1, null, null, null);t.exports = r.exports;
  }, 3505: function _(t, e, a) {
    "use strict";

    Object.defineProperty(e, "__esModule", { value: !0 });var r = a(5),
        n = a(3),
        o = a.n(n);e.default = { name: "crt-workflow-settings", data: function data() {
        return { hasDataPopulated: !1, loading: !0, pageLoad: !1, crtworkflow_requester_and_cc: "", ccOptions: [{ name: "yes", value: 1 }, { name: "no", value: 0 }], reqOptions: [{ name: "ticket_requester_and_cc", value: "ticket_requester_and_cc" }, { name: "recur_requester_and_cc", value: "recur_requester_and_cc" }] };
      }, beforeMount: function beforeMount() {
        this.getValues();
      }, methods: { getValues: function getValues() {
          var t = this;o.a.get("/api/crtworkflow/settings").then(function (e) {
            t.hasDataPopulated = !0, t.loading = !1, t.crtworkflow_requester_and_cc = e.data.data.crtworkflow_requester_and_cc;
          }).catch(function (e) {
            t.hasDataPopulated = !0, t.loading = !1, Object(r.a)(e, "crt");
          });
        }, onChange: function onChange(t, e) {
          this[e] = t;
        }, onSubmit: function onSubmit() {
          var t = this,
              e = {};e.crtworkflow_requester_and_cc = this.crtworkflow_requester_and_cc, this.pageLoad = !0, o.a.post("/api/crtworkflow/settings", e).then(function (e) {
            t.pageLoad = !1, Object(r.b)(e, "crt"), t.getValues();
          }).catch(function (e) {
            t.pageLoad = !1, Object(r.a)(e, "crt");
          });
        } }, components: { alert: a(6), "custom-loader": a(9), loader: a(8), "radio-button": a(21) } };
  }, 3506: function _(t, e) {
    t.exports = { render: function render() {
        var t = this,
            e = t.$createElement,
            a = t._self._c || e;return a("div", [a("alert", { attrs: { componentName: "crt" } }), t._v(" "), a("div", { staticClass: "card card-light" }, [a("div", { staticClass: "card-header" }, [a("h3", { staticClass: "card-title" }, [t._v(t._s(t.trans("settings")))])]), t._v(" "), a("div", { staticClass: "card-body" }, [!t.hasDataPopulated || t.loading ? a("div", { staticClass: "row" }, [a("loader", { attrs: { "animation-duration": 4e3, size: 60 } })], 1) : t._e(), t._v(" "), t.hasDataPopulated ? a("div", { staticClass: "row" }, [a("radio-button", { attrs: { options: t.reqOptions, label: t.trans("select_requester_of_ticket"), name: "crtworkflow_requester_and_cc", value: t.crtworkflow_requester_and_cc, onChange: t.onChange, classname: "form-group col-sm-12", hint: t.trans("crtworkflow_requester_and_cc_tooltip") } })], 1) : t._e()]), t._v(" "), t.hasDataPopulated ? a("div", { staticClass: "card-footer" }, [a("button", { staticClass: "btn btn-primary", on: { click: t.onSubmit } }, [a("i", { staticClass: "fas fa-sync-alt" }), t._v(" " + t._s(t.trans("update")) + "\n\t\t\t\t")])]) : t._e()]), t._v(" "), t.pageLoad ? a("div", { staticClass: "row" }, [a("custom-loader", { attrs: { duration: 4e3 } })], 1) : t._e()], 1);
      }, staticRenderFns: [] };
  } }, [3497]);

/***/ })

},[2909]);