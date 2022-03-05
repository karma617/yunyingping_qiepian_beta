module.exports = {
    // 站点名
    title: process.env.VUE_APP_BASE_SITE_TITLE,

    /**
     * @type {boolean} true | false
     * @description 是否显示右侧悬浮设置按钮
     */
    showSettings: true,

    /**
     * @type {boolean} true | false
     * @description 是否固定 Header 栏
     */
    fixedHeader: false,

    /**
     * @type {boolean} true | false
     * @description 是否显示logo图片
     */
    sidebarLogo: true,

    //token名称
    tokenName: "accessToken",
    
    //token在localStorage、sessionStorage、cookie存储的key的名称
    tokenTableName: "Karma-Token",
    
    //token存储位置localStorage sessionStorage cookie
    storage: "localStorage",
    
    //token失效回退到登录页时是否记录本次的路由
    recordRoute: true,
}
