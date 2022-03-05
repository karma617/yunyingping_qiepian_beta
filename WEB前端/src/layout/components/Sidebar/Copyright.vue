<template>
    <div class="copyright-pages">
        <div :class="!collapse ? 'copyright-pages-show' : ''">
            <h1 class="copyright-title" v-if="!collapse">
                Copyright © 2021
                <a target="_blank" href="https://www.qdapi.com">燕祺阁</a>
                <p>All rights reserved.</p>
            </h1>
        </div>
        <div :class="!collapse ? '' : 'copyright-pages-show'">
            <h1 class="copyright-title" v-if="collapse">
                <a target="_blank" href="https://www.qdapi.com">燕祺阁</a>
            </h1>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Copyright',
    props: {
        collapse: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {};
    }
};
</script>

<style lang="scss">
.copyright-pages {
    position: relative;
    width: 100%;
    height: 80px;
    line-height: 90px;
    background: #2b2f3a;
    text-align: center;
    overflow: hidden;
    .copyright-title {
        display: inline-block;
        margin: 0;
        color: #fff;
        line-height: 20px;
        font-size: 0.1cm;
        vertical-align: middle;

        > a {
            display: inline !important;
        }
    }
}
.copyright-pages-show {
    -webkit-animation: fadeIn 1.3s 1 linear;
}
@-webkit-keyframes fadeIn {
    0% {
        opacity: 0; /*初始状态 透明度为0*/
    }
    50% {
        opacity: 0.5; /*中间状态 透明度为1*/
    }
    100% {
        opacity: 1; /*结尾状态 透明度为0*/
    }
}
</style>
