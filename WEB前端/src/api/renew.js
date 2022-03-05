import request from "@/utils/request";

export function getLists(data) {
    return request({
        url: "member/member_level/getList",
        method: "post",
        data,
    });
}

export function buy(data) {
    return request({
        url: "goods/order/buy",
        method: "post",
        data,
    });
}