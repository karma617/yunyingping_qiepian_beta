import request from "@/utils/request";

export function getLists(data) {
    return request({
        url: "member/member_file/fileLists",
        method: "post",
        data,
    });
}

export function deleteItem(data) {
    return request({
        url: "member/member_file/del",
        method: "post",
        data,
    });
}

export function saveExtUsers(data) {
    return request({
        url: "member/member_file/extUsers",
        method: "post",
        data,
    });
}