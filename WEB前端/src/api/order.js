import request from "@/utils/request";

export function pay(data) {
    return request({
        url: "one_pay/index/apply",
        method: "post",
        data,
    });
}

export function getOrderInfo(data) {
    return request({
        url: "goods/order/getOrderInfo",
        method: "post",
        data,
    });
}

export function getOrderStatus(data) {
    return request({
        url: "goods/order/getOrderStatus",
        method: "post",
        data,
    });
}
