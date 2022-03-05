<template>
    <div class="panel-pages">
        <el-row :gutter="40" class="panel-group">
            <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
                <div class="card-panel">
                    <div class="card-panel-icon-wrapper icon-people">
                        <i class="el-icon-folder" />
                    </div>
                    <div class="card-panel-description">
                        <div class="card-panel-text">个人资源</div>
                        <count-to
                            :start-val="0"
                            :end-val="userInfo.file_count || 0"
                            :duration="2600"
                            class="card-panel-num"
                        />
                    </div>
                </div>
            </el-col>

            <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
                <div class="card-panel">
                    <div class="card-panel-icon-wrapper icon-shopping">
                        <i class="el-icon-postcard" />
                    </div>
                    <div class="card-panel-description">
                        <div class="card-panel-text">
                            用户名：{{ userInfo.username || '-' }}
                        </div>
                        <div class="card-panel-text">
                            用户ID：{{ userInfo.id || '-' }}
                        </div>
                    </div>
                </div>
            </el-col>

            <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
                <div class="card-panel">
                    <div class="card-panel-icon-wrapper icon-shopping">
                        <i class="el-icon-user" />
                    </div>
                    <div class="card-panel-description">
                        <div class="card-panel-text">
                            当前套餐：{{ userInfo.level_name || '-' }}
                        </div>
                        <div class="card-panel-num">
                            {{ userInfo ? userInfo.exp_time : '-' }}
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>
        <el-row :gutter="40" class="panel-group" v-if="msgList.length > 0">
            <el-col :xs="12" :sm="12" :lg="18" class="card-panel-col">
                <div class="msg-list">
                    <div
                        v-for="(item, index) in msgList"
                        :key="index"
                        class="msg-list-title"
                    >
                        <a @click="msgDetail(item)">☛ {{ item.title }}</a>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-dialog
            :title="msgContent.title"
            :visible.sync="dialogVisible"
            width="30%"
        >
            <span v-html="msgContent.content"></span>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogVisible = false">
                    确 定
                </el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
import CountTo from 'vue-count-to';
import { getLists } from '@/api/news';
export default {
    components: {
        CountTo
    },
    data() {
        return {
            userInfo: {},
            msgList: [],
            dialogVisible: false,
            msgContent: {
                title: '',
                content: ''
            }
        };
    },
    mounted() {
        this.getList();
        this.getUserInfo();
    },
    methods: {
        msgDetail(item) {
            this.msgContent = {
                title: item.title,
                content: item.content
            };
            this.dialogVisible = !this.dialogVisible;
        },
        async getList() {
            const { data } = await getLists();
            this.msgList = data.list;
        },
        getUserInfo() {
            this.$store
                .dispatch('user/getInfo', this.loginForm)
                .then(res => {
                    this.userInfo = res;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        }
    }
};
</script>

<style lang="scss" scoped>
.panel-pages {
    height: 80vh;
    .panel-group {
        margin-top: 18px;

        .card-panel-col {
            margin-bottom: 32px;
        }

        .card-panel {
            height: 108px;
            cursor: pointer;
            font-size: 12px;
            position: relative;
            overflow: hidden;
            color: #666;
            background: #fff;
            box-shadow: 4px 4px 40px rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 0, 0, 0.05);

            &:hover {
                .card-panel-icon-wrapper {
                    color: #fff;
                }

                .icon-people {
                    background: #40c9c6;
                }

                .icon-message {
                    background: #36a3f7;
                }

                .icon-money {
                    background: #f4516c;
                }

                .icon-shopping {
                    background: #34bfa3;
                }
            }

            .icon-people {
                color: #40c9c6;
                font-size: 45px;
            }

            .icon-message {
                color: #36a3f7;
            }

            .icon-money {
                color: #f4516c;
            }

            .icon-shopping {
                color: #34bfa3;
                font-size: 45px;
            }

            .card-panel-icon-wrapper {
                float: left;
                margin: 14px 0 0 14px;
                padding: 16px;
                transition: all 0.38s ease-out;
                border-radius: 6px;
            }

            .card-panel-icon {
                float: left;
                font-size: 48px;
            }

            .card-panel-description {
                float: right;
                font-weight: bold;
                margin: 26px;
                margin-left: 0px;

                .card-panel-text {
                    line-height: 18px;
                    color: rgba(0, 0, 0, 0.45);
                    font-size: 16px;
                    margin-bottom: 12px;
                }

                .card-panel-num {
                    font-size: 16px;
                }
            }
        }
    }
    .msg-list {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        background-color: #ffffff;
        padding: 15px;

        .msg-list-title {
            font-size: 16px;
            padding: 5px 0;
        }
    }
}

@media (max-width: 550px) {
    .card-panel-description {
        display: none;
    }

    .card-panel-icon-wrapper {
        float: none !important;
        width: 100%;
        height: 100%;
        margin: 0 !important;

        .svg-icon {
            display: block;
            margin: 14px auto !important;
            float: none !important;
        }
    }
}
</style>
