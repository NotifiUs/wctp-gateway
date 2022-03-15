window._ = require("lodash");

window.Popper = require("@popperjs/core");

require("bootstrap");

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
