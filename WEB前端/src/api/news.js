import request from "@/utils/request";

export function getLists(data) {
    return request({
        url: "news/index/msg",
        method: "post",
        data,
    });
}
