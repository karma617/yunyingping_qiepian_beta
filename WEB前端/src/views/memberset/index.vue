<template>
    <div class="memberset-pages" v-loading="loading">
        <div class="form-box">
            <el-form
                ref="settingForm"
                :model="settingForm"
                autocomplete="on"
                label-position="right"
                label-width="150px"
                size="mini"
            >
                <el-form-item label="上传API Token">
                    <el-input
                        @focus="copyToken"
                        v-model="apiToken"
                        placeholder="上传API Token"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                        readonly=""
                    />
                </el-form-item>

                <el-form-item label="API上传接口地址">
                    <el-input
                        v-model="settingForm.ext_conifg.api_url"
                        placeholder="请填写API上传接口地址,例:https://域名/api.php,请确保可以正常访问"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                        @input="apiChange"
                    >
                        <el-button
                            slot="append"
                            type="warning"
                            @click.native.prevent="handleCheck"
                        >
                            点此检测通讯是否正常
                        </el-button>
                    </el-input>
                </el-form-item>

                <el-form-item label="允许空referrer访问">
                    <el-switch
                        @change="apiChange"
                        v-model="settingForm.ext_conifg.origin_empty"
                    ></el-switch>
                </el-form-item>

                <el-form-item label="开启授权域名访问">
                    <el-switch
                        @change="apiChange"
                        v-model="settingForm.ext_conifg.origin_all"
                    ></el-switch>
                </el-form-item>

                <el-form-item label="域名白名单">
                    <el-input
                        :rows="4"
                        placeholder="开启授权域名访问时，此处填写允许访问的域名白名单，多个域名换行分割。"
                        type="textarea"
                        v-model="settingForm.ext_conifg.hosts"
                    ></el-input>
                </el-form-item>

                <!-- <el-form-item label="苹果CMS TOKEN">
                    <el-input
                        v-model="settingForm.ext_conifg.cmsToken"
                        placeholder="点击右侧按钮生成cmsToken"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    >
                        <el-button
                            slot="append"
                            v-if="
                                settingForm.ext_conifg.cmsToken == '' ||
                                    !settingForm.ext_conifg.cmsToken
                            "
                            @click.native.prevent="handleToken"
                        >
                            生成
                        </el-button>
                        <el-button
                            slot="append"
                            v-else
                            @click.native.prevent="handleReToken"
                        >
                            重新生成
                        </el-button>
                    </el-input>
                </el-form-item> -->

                <hr />

                <el-form-item label="七牛CND加速域名">
                    <el-input
                        v-model="settingForm.ext_conifg.qn.file_host"
                        placeholder="请填写七牛存储桶绑定的CND加速域名,http(s)开头!"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    >
                        <span slot="append">若此处为https，则上面API上传接口也应为https，反之同样为http</span>
                    </el-input>
                </el-form-item>
                <el-form-item label="七牛加速上传域名">
                    <el-input
                        v-model="settingForm.ext_conifg.qn.upload_host"
                        placeholder="请填写七牛存储区域“加速上传”域名,只要域名,不要http://"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    >
                        <a
                            slot="append"
                            target="_blank"
                            href="https://developer.qiniu.com/kodo/1671/region-endpoint-fq"
                        >
                            查看
                        </a>
                    </el-input>
                </el-form-item>
                <el-form-item label="七牛ak">
                    <el-input
                        v-model="settingForm.ext_conifg.qn.ak"
                        placeholder="请填写七牛ak,在密钥管理获取"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>
                <el-form-item label="七牛sk">
                    <el-input
                        v-model="settingForm.ext_conifg.qn.sk"
                        placeholder="请填写七牛sk,在密钥管理获取"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>
                <el-form-item label="七牛bucket">
                    <el-input
                        v-model="settingForm.ext_conifg.qn.bucket"
                        placeholder="请填写七牛bucket空间名称"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>
                
                <hr />
                
                <el-form-item label="腾讯ak">
                    <el-input
                        v-model="settingForm.ext_conifg.tx.ak"
                        placeholder="请填写腾讯ak,在密钥管理获取"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>
                <el-form-item label="腾讯sk">
                    <el-input
                        v-model="settingForm.ext_conifg.tx.sk"
                        placeholder="请填写腾讯sk,在密钥管理获取"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>
                <el-form-item label="腾讯bucket">
                    <el-input
                        v-model="settingForm.ext_conifg.tx.bucket"
                        placeholder="请填写腾讯bucket空间名称"
                        type="text"
                        tabindex="1"
                        autocomplete="on"
                    ></el-input>
                </el-form-item>

                <el-form-item size="large">
                    <el-button
                        :disabled="disabled"
                        type="primary"
                        @click.native.prevent="handleSave"
                    >
                        {{ disabled ? '请先检测通讯是否正常' : '保存' }}
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import Utils from '@/utils/utils.js';
import { getToken } from '@/utils/auth';
export default {
    name: 'System',
    data() {
        return {
            settingForm: {
                ext_conifg: {
                    api_url: '',
                    hosts: '',
                    origin_all: false,
                    origin_empty: false,
                    qn: {
                        file_host: '',
                        upload_host: '',
                        ak: '',
                        sk: '',
                        bucket: ''
                    },
                    tx: {
                        ak: '',
                        sk: '',
                        bucket: '',
                    },
                }
            },
            apiToken: '',
            loading: true,
            disabled: false,
            userInfo: []
        };
    },
    created() {},
    mounted() {
        this.apiToken = getToken();
        this.getUserInfo();
    },
    methods: {
        apiChange() {
            this.disabled = true;
        },
        handleCheck() {
            this.loading = true;
            this.$store
                .dispatch('user/checkHost', this.settingForm)
                .then(res => {
                    this.loading = false;
                    if (res.code == 200) {
                        this.$message({
                            message: '通讯正常',
                            type: 'success'
                        });
                        this.disabled = false;
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.disabled = false;
                });
        },
        copyToken() {
            let rt = Utils.copyTxt(this.apiToken);
            if (rt === true) {
                this.$message({
                    message: '复制成功',
                    type: 'success'
                });
            } else {
                this.$message.error(rt);
            }
        },
        handleReToken() {
            this.$confirm(
                '是否要重置token，重置后须在苹果cms里重新配置该token！',
                '提示',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }
            )
                .then(() => {
                    this.settingForm.ext_conifg.cmsToken = Utils.randomString(
                        32
                    );
                    this.$forceUpdate();
                })
                .catch(() => {});
        },
        handleToken() {
            this.settingForm.ext_conifg.cmsToken = Utils.randomString(32);
            this.$forceUpdate();
        },
        getUserInfo() {
            this.$store
                .dispatch('user/getInfo')
                .then(res => {
                    Utils.setStorage('userInfo', res);
                    this.userInfo = res;
                    this.loading = false;
                    if (res.ext_conifg) {
                        for (let key in this.settingForm.ext_conifg) {
                            if (res.ext_conifg[key]) {
                                this.settingForm.ext_conifg[key] =
                                    res.ext_conifg[key];
                            }
                        }
                    }
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        handleSave() {
            this.loading = true;
            this.$store
                .dispatch('user/modifySetting', this.settingForm)
                .then(res => {
                    this.loading = false;
                    this.$message({
                        showClose: true,
                        message: res.msg,
                        type: 'success'
                    });
                    this.getUserInfo();
                })
                .catch(() => {
                    this.loading = false;
                });
        }
    }
};
</script>

<style lang="scss">
.memberset-pages {
    padding: 20px;
    .form-box {
        width: 50%;
    }
}
</style>
