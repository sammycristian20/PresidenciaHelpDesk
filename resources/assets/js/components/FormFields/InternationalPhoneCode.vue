<template>
	 <input type="tel" class="form-control inteleCode"  style="visibility:hidden"/>
</template>
<script>
export default {
  data() {
    return {};
  },
  mounted() {
    var script = window.axios.defaults.baseURL + "/lb-faveo/js/utils.min.js";
    $(".inteleCode").intlTelInput({
      // allowDropdown: false,
      // autoHideDialCode: false,
      // autoPlaceholder: "off",
      // dropdownContainer: "body",
      // excludeCountries: ["us"],
      // formatOnDisplay: false,
      geoIpLookup: function(callback) {
        $.get("https://ipinfo.io", function() {}, "jsonp").always(function(
          resp
        ) {
          var countryCode = resp && resp.country ? resp.country : "";
          callback(countryCode);
        });
      },
      initialCountry: "auto",
      // nationalMode: false,
      // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
      // placeholderNumberType: "MOBILE",
      // preferredCountries: ['cn', 'jp'],
      separateDialCode: true,
      utilsScript: script
    });
  }
};
</script>