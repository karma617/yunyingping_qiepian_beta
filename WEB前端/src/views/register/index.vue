<template>
    <div class="register-container">
        <div @contextmenu.prevent="menuPlayer()">
            <video ref="video" class="video-box" loop muted autoplay>
                <source :src="videoSrc" type="video/mp4" />
                抱歉，您的浏览器不支持内嵌视频
            </video>
        </div>
        <el-form
            ref="registerForm"
            :model="registerForm"
            :rules="registerRules"
            class="register-form"
            autocomplete="on"
            label-position="left"
        >
            <div class="title-container">
                <h3 class="title">{{title}}</h3>
            </div>

            <el-form-item prop="username">
                <span class="svg-container">
                    <svg-icon icon-class="user" />
                </span>
                <el-input
                    ref="username"
                    v-model="registerForm.username"
                    placeholder="请输入登录账号"
                    name="username"
                    type="text"
                    tabindex="1"
                    autocomplete="on"
                />
            </el-form-item>

            <el-tooltip
                v-model="capsTooltip"
                content="大写锁定已开启"
                placement="right"
                manual
            >
                <el-form-item prop="password">
                    <span class="svg-container">
                        <svg-icon icon-class="password" />
                    </span>
                    <el-input
                        :key="passwordType"
                        ref="password"
                        v-model="registerForm.password"
                        :type="passwordType"
                        placeholder="请输入登录密码"
                        name="password"
                        tabindex="2"
                        autocomplete="on"
                        @keyup.native="checkCapslock"
                        @blur="capsTooltip = false"
                        @keyup.enter.native="handleregister"
                    />
                    <span class="show-pwd" @click="showPwd">
                        <svg-icon
                            :icon-class="
                                passwordType === 'password' ? 'eye' : 'eye-open'
                            "
                        />
                    </span>
                </el-form-item>
            </el-tooltip>

            <el-tooltip
                v-model="capsTooltip"
                content="大写锁定已开启"
                placement="right"
                manual
            >
                <el-form-item prop="repassword">
                    <span class="svg-container">
                        <svg-icon icon-class="password" />
                    </span>
                    <el-input
                        :key="passwordType"
                        ref="repassword"
                        v-model="registerForm.repassword"
                        :type="passwordType"
                        placeholder="再次输入密码"
                        name="repassword"
                        tabindex="2"
                        autocomplete="on"
                        @keyup.native="checkCapslock"
                        @blur="capsTooltip = false"
                        @keyup.enter.native="handleRegister"
                    />
                    <span class="show-pwd" @click="showPwd">
                        <svg-icon
                            :icon-class="
                                passwordType === 'password' ? 'eye' : 'eye-open'
                            "
                        />
                    </span>
                </el-form-item>
            </el-tooltip>

            <el-form-item prop="qq">
                <span class="svg-container"><svg-icon icon-class="qq" /></span>
                <el-input
                    ref="qq"
                    v-model="registerForm.qq"
                    placeholder="绑定QQ号"
                    name="qq"
                    type="text"
                    tabindex="1"
                    autocomplete="on"
                />
            </el-form-item>

            <div class="btn-text">
                <router-link :to="{ name: 'login' }">
                    <el-dropdown-item>已有帐号？去登录</el-dropdown-item>
                </router-link>
            </div>

            <el-button
                :loading="loading"
                type="primary"
                style="width: 100%; margin: 30px 0"
                @click.native.prevent="handleRegister"
            >
                注册
            </el-button>
        </el-form>
    </div>
</template>

<script>
import { validUsername } from '@/utils/validate';

export default {
    name: 'Register',
    data() {
        const validateUsername = (rule, value, callback) => {
            if (!validUsername(value)) {
                callback(new Error('请填写登录账号'));
            } else {
                callback();
            }
        };
        const validatePassword = (rule, value, callback) => {
            if (value.length < 6) {
                callback(new Error('请填写密码'));
            } else {
                callback();
            }
        };
        const validateRePassword = (rule, value, callback) => {
            if (value != this.registerForm.password) {
                callback(new Error('两次密码不一致'));
            } else {
                callback();
            }
        };
        const validateQq = (rule, value, callback) => {
            if (!validUsername(value)) {
                callback(new Error('请填写QQ账号'));
            } else {
                callback();
            }
        };
        return {
            title: process.env.VUE_APP_BASE_SITE_TITLE,
            registerForm: {
                username: '',
                password: '',
                repassword: '',
                qq: ''
            },
            registerRules: {
                username: [
                    {
                        required: true,
                        trigger: 'blur',
                        validator: validateUsername
                    }
                ],
                qq: [
                    {
                        required: true,
                        trigger: 'blur',
                        validator: validateQq
                    }
                ],
                password: [
                    {
                        required: true,
                        trigger: 'blur',
                        validator: validatePassword
                    }
                ],
                repassword: [
                    {
                        required: true,
                        trigger: 'blur',
                        validator: validateRePassword
                    }
                ]
            },
            passwordType: 'password',
            capsTooltip: false,
            loading: false,
            redirect: undefined,
            otherQuery: {},
            videoSrc: '',
            videoSrcArr: [
                'https://cdn.jsdelivr.net/gh/810331109/w@w/1.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/2.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/3.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/4.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/5.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/6.mp4',
                'https://cdn.jsdelivr.net/gh/810331109/w@w/7.mp4',
                'https://img-baofun.zhhainiao.com/pcwallpaper_ugc/scene/eb703a80e4ff898f5feb35ff3ec45925_preview.mp4',
                'https://img-baofun.zhhainiao.com/pcwallpaper_ugc/scene/09cc83f575bb663ecbf26d7724ea232c_preview.mp4',
                'https://img-baofun.zhhainiao.com/pcwallpaper_ugc/preview/864dc8dbf4918c2b481bf1deea899bf9_preview.mp4',
                'https://img-baofun.zhhainiao.com/pcwallpaper_ugc/preview/83cc918f8ef325ea6ceaaaf55dccaf5d_preview.mp4',
                'https://img-baofun.zhhainiao.com/pcwallpaper_ugc/preview/864dc8dbf4918c2b481bf1deea899bf9_preview.mp4'
            ]
        };
    },
    watch: {
        $route: {
            handler: function(route) {
                const query = route.query;
                if (query) {
                    this.redirect = query.redirect;
                    this.otherQuery = this.getOtherQuery(query);
                }
            },
            immediate: true
        }
    },
    created() {
        let index = Math.floor(Math.random() * this.videoSrcArr.length);
        this.videoSrc = this.videoSrcArr[index];
    },
    mounted() {
        if (this.registerForm.username === '') {
            this.$refs.username.focus();
        } else if (this.registerForm.password === '') {
            this.$refs.password.focus();
        }
    },
    methods: {
        checkCapslock(e) {
            const { key } = e;
            this.capsTooltip =
                key && key.length === 1 && key >= 'A' && key <= 'Z';
        },
        showPwd() {
            if (this.passwordType === 'password') {
                this.passwordType = '';
            } else {
                this.passwordType = 'password';
            }
            this.$nextTick(() => {
                this.$refs.password.focus();
            });
        },
        handleRegister() {
            this.$refs.registerForm.validate(valid => {
                if (valid) {
                    this.loading = true;
                    this.$store
                        .dispatch('user/regrister', this.registerForm)
                        .then(() => {
                            this.$router.push({
                                path: this.redirect || '/',
                                query: this.otherQuery
                            });
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        getOtherQuery(query) {
            return Object.keys(query).reduce((acc, cur) => {
                if (cur !== 'redirect') {
                    acc[cur] = query[cur];
                }
                return acc;
            }, {});
        },
        menuPlayer() {
            return false;
        }
    }
};
</script>

<style lang="scss">
$bg: #191a1b;
$light_gray: #fff;
$cursor: #fff;

@supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
    .register-container .el-input input {
        color: $cursor;
    }
}

.register-container {
    background-color: #282c34 !important;
    .video-box {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        object-fit: fill;
    }

    video::-webkit-media-controls {
        display: none !important;
    }

    .btn-text {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
    }
    .btn-text a li {
        color: #fff;
    }

    .el-input {
        display: inline-block;
        height: 47px;
        width: 85%;

        input {
            background: transparent;
            border: 0px;
            -webkit-appearance: none;
            border-radius: 0px;
            padding: 12px 5px 12px 15px;
            color: $light_gray;
            height: 47px;
            caret-color: $cursor;

            &:-webkit-autofill {
                box-shadow: 0 0 0px 1000px $bg inset !important;
                -webkit-text-fill-color: $cursor !important;
            }
        }
    }

    .el-form-item {
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        color: #454545;
    }
}
.el-form-item__error {
    color: #ffffff !important;
}
.el-dropdown-menu__item:hover{
    background-color: #e8f4ff !important;
    font-weight: 600;
    border-radius: 10px;
}
</style>

<style lang="scss" scoped>
$bg: #2d3a4b;
$dark_gray: #889aa4;
$light_gray: #fff;
.register-container {
    min-height: 100%;
    width: 100%;
    background-color: $bg;
    overflow: hidden;

    .register-form {
        position: relative;
        width: 520px;
        max-width: 100%;
        padding: 160px 35px 0;
        margin: 0 auto;
        overflow: hidden;
    }

    .tips {
        font-size: 14px;
        color: #fff;
        margin-bottom: 10px;

        span {
            &:first-of-type {
                margin-right: 16px;
            }
        }
    }

    .svg-container {
        padding: 6px 5px 6px 15px;
        color: $dark_gray;
        vertical-align: middle;
        width: 30px;
        display: inline-block;
    }

    .title-container {
        position: relative;

        .title {
            font-size: 26px;
            color: $light_gray;
            margin: 0px auto 40px auto;
            text-align: center;
            font-weight: bold;
        }
    }

    .show-pwd {
        position: absolute;
        right: 10px;
        top: 7px;
        font-size: 16px;
        color: $dark_gray;
        cursor: pointer;
        user-select: none;
    }

    .thirdparty-button {
        position: absolute;
        right: 0;
        bottom: 6px;
    }

    @media only screen and (max-width: 470px) {
        .thirdparty-button {
            display: none;
        }
    }
}

</style>
