import Vue from "vue";

import Cookies from "js-cookie";

import "normalize.css/normalize.css"; // a modern alternative to CSS resets

import Element from "element-ui";
import "./styles/element-variables.scss";

import "@/styles/index.scss"; // global css

import App from "./App";
import store from "./store";
import router from "./router";

import "./icons"; // icon
import "./permission"; // permission control
import "./utils/error-log"; // error log

import * as filters from "./filters"; // global filters

import md5 from "js-md5";

Vue.prototype.$md5 = md5;

//在main.js里面引入调用
import Fingerprint2 from "fingerprintjs2";
// 调用,一般是在main.js或者vuex里面调用
Fingerprint2.get(function(components) {
	const values = components.map(function(component, index) {
		if (index === 0) {
			//把微信浏览器里UA的wifi或4G等网络替换成空,不然切换网络会ID不一样
			return component.value.replace(/\bNetType\/\w+\b/, "");
		}
		return component.value;
	});
	// 生成最终id murmur
	const murmur = Fingerprint2.x64hash128(values.join(""), 31);
	window.localStorage.setItem("UUID", murmur);
});

/**
 * If you don't want to use mock-server
 * you want to use MockJs for mock api
 * you can execute: mockXHR()
 *
 * Currently MockJs will be used in the production environment,
 * please remove it before going online ! ! !
 */
// if (process.env.NODE_ENV === "production") {
// 	const {
// 		mockXHR
// 	} = require("../mock");
// 	mockXHR();
// }

Vue.use(Element, {
	size: Cookies.get("size") || "medium", // set element-ui default size
});

// register global utility filters
Object.keys(filters).forEach((key) => {
	Vue.filter(key, filters[key]);
});

Vue.config.productionTip = false;

new Vue({
	el: "#app",
	router,
	store,
	render: (h) => h(App),
});
