import request from "@/utils/request";

export function regrister(data) {
	return request({
		url: "member/register/account_register",
		method: "post",
		data,
	});
}

export function login(data) {
	return request({
		url: "member/login/login",
		method: "post",
		data,
	});
}

export function getInfo() {
	return request({
		url: "member/member/get_info",
		method: "post",
	});
}

export function logout() {
	return request({
		url: "member/member/logout",
		method: "post",
	});
}

export function modifySetting(data) {
	return request({
		url: "member/member/modify_setting",
		method: "post",
		data
	});
}

export function checkHost(data) {
	return request({
		url: "member/member/checkHost",
		method: "post",
		data
	});
}
