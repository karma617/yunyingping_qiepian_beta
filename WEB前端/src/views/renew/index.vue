<template> 
    <div class="renew-pages">
        <div class="goods-list" v-loading="loading">
            <div
                class="goods-list-item"
                v-for="(item, index) in list"
                :key="index"
            >
                <div class="goods-list-item-img">
                    <el-image :src="item.thumb"></el-image>
                </div>
                <div class="goods-list-item-info">
                    <div class="goods-list-item-title">
                        {{ item.level_name }}
                    </div>
                    <div
                        class="font-size-15 goods-list-item-price"
                        v-if="item.limit_price == '0.00'"
                    >
                        <span class="goods-list-item-price-limit">
                            售价：¥ {{ item.price }}
                        </span>
                        <span class="goods-list-item-price-sale">
                            折扣价：¥ {{ item.price }}
                        </span>
                    </div>
                    <div class="font-size-15 goods-list-item-price" v-else>
                        <span class="goods-list-item-price-limit">
                            折扣价：¥ {{ item.limit_price }}
                        </span>
                        <span class="goods-list-item-price-sale">
                            原价：¥ {{ item.price }}
                        </span>
                    </div>

                    <div class="font-size-15 goods-list-item-des">
                        {{ item.intro || ' ' }}
                    </div>
                    <div class="goods-list-item-buy">
                        <el-button
                            size="small"
                            type="danger"
                            plain
                            icon="el-icon-shopping-cart-2"
                            @click="buy(item)"
                        >
                            立即购买 
                        </el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { getLists, buy } from '@/api/renew';
export default {
    name: 'Renew',
    data() {
        return {
            list: null,
            listQuery: {
                page: 1,
                limit: 10
            },
            loading: true
        };
    },
    created() {},
    mounted() {
        this.getList();
    },
    methods: {
        async getList() {
            const { data } = await getLists(this.listQuery);
            const items = data.list;
            this.list = items.map(v => {
                v.thumb = v.thumb && v.thumb.length > 0 ? process.env.VUE_APP_BASE_IMAGE + v.thumb[0] : '';
                return v;
            });
            this.loading = false;
        },
        async buy(row) {
            this.$confirm('是否要下单购买 ' + row.level_name + ' ？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            })
                .then(() => {
                    this.loading = true;
                    buy({ id: row.id }).then(res => {
                        this.loading = false;
                        const { msg, code, data } = res;
                        if (code == 200) {
                            this.$router.push(`/order/order?order_sn=${data}`);
                        }
                        this.$message({
                            message: msg,
                            type: 'success'
                        });
                    });
                })
                .catch(() => {});
        }
    }
};
</script>

<style lang="scss">
.renew-pages {
    padding: 20px;

    .font-size-15 {
        font-size: 15px;
        color: #282c35;
    }

    .goods-list {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        justify-content: flex-start;

        .goods-list-item {
            flex: 1;
            width: 25%;
            min-width: 25%;
            max-width: 25%;
            padding: 10px;
            text-align: center;
            div {
                margin-bottom: 10px;
            }

            .goods-list-item-img {
                max-width: 150px;
                max-height: 150px;
                margin: 10px auto;
            }

            .goods-list-item-info {
                .goods-list-item-price {
                    .goods-list-item-price-sale {
                        font-size: 14px;
                        text-decoration: line-through;
                        color: #ababab;
                    }
                }

                .goods-list-item-title {
                    font-weight: bold;
                    font-size: 18px;
                }
            }
        }
    }

    @media only screen and (max-width: 1066px) {
        .goods-list-item-price {
            display: flex;
            flex-direction: column;
        }
    }

    @media only screen and (max-width: 760px) {
        .goods-list {
            flex-direction: column;
            justify-content: space-around;

            .goods-list-item {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                padding: 10px;
                text-align: center;

                .goods-list-item-img {
                    width: 100%;
                }
            }
        }
    }
}
</style>
