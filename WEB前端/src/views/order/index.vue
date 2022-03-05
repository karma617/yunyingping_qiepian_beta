<template>
    <div class="order-pages" v-loading="loading">
        <el-card class="box-card" shadow="hover" v-if="payInfo.payQrCode == ''">
            <div slot="header" class="clearfix order-info-item">
                <span>订单号：{{ orderInfo.order_sn || ' - ' }}</span>
            </div>
            <div class="order-info">
                <div class="order-info-item order-info-goodsname">
                    商品名：{{ orderInfo.goods_name || ' - ' }}
                </div>
                <div class="order-info-item order-info-goodsprice">
                    商品金额：¥ {{ orderInfo.price || ' - ' }} 元
                </div>
                <div class="order-info-item order-info-goodsnumber">
                    购买数量：{{ orderInfo.number || ' - ' }} 个
                </div>
                <div class="order-info-item order-info-goodsremark">
                    备注：{{ orderInfo.remark || ' - ' }}
                </div>
                <div class="order-info-item order-info-orderstatus">
                    订单状态：{{ orderInfo.status == 0 ? '待支付' : '已支付' }}
                </div>
                <div class="order-info-item order-info-gopay">
                    <div class="order-info-gopay-payment order-info-gopay-item">
                        <span
                            class="order-info-gopay-payment-item order-info-gopay-payment-title"
                        >
                            支付方式：
                        </span>
                        <div
                            class="order-info-gopay-payment-item order-info-gopay-payment-box"
                        >
                            <el-radio-group
                                v-model="payment"
                                @change="changePayment"
                            >
                                <el-radio label="alipay_scan" border>
                                    支付宝扫码
                                </el-radio>
                                <!-- <el-radio label="wechat_qr" border>微信扫码</el-radio> -->
                            </el-radio-group>
                        </div>
                    </div>
                    <div class="order-info-gopay-btn order-info-gopay-item">
                        <el-button
                            size="medium"
                            type="primary"
                            icon="el-icon-shopping-cart-2"
                            @click="pay"
                        >
                            提交订单
                        </el-button>
                    </div>
                </div>
            </div>
        </el-card>

        <el-card class="box-card" shadow="hover" v-else>
            <div slot="header" class="clearfix order-info-item">
                <span>
                    第三方支付订单号：{{ payInfo.out_trade_no || ' - ' }}
                </span>
            </div>
            <div class="order-info">
                <div class="order-info-item">
                    <el-avatar
                        shape="square"
                        :size="200"
                        fit="fill"
                        :src="payInfo.payQrCode"
                    ></el-avatar>
                </div>
                <div class="order-info-item">
                    <p>您将购买 {{ orderInfo.goods_name }}</p>
                    <p>请扫码支付 ¥ {{ orderInfo.price }} 元</p>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
import { pay, getOrderInfo, getOrderStatus } from '@/api/order';
export default {
    name: 'Order',
    data() {
        return {
            loading: false,
            orderInfo: {},
            payment: 'alipay_scan',
            order_sn: '',
            payInfo: {
                payQrCode: '',
                out_trade_no: ''
            },
            timer: null
        };
    },
    created() {},
    beforeDestroy() {
        clearTimeout(this.timer);
    },
    mounted() {
        this.order_sn = this.$route.query.order_sn;
        this.getOrderInfo();
    },
    methods: {
        changePayment(val) {
            console.log(val);
        },
        getOrderInfo() {
            this.loading = true;
            getOrderInfo({ order_sn: this.order_sn }).then(res => {
                this.orderInfo = res.data;
                this.loading = false;
            });
        },
        pay() {
            this.loading = true;
            pay({
                order_sn: this.order_sn,
                method: this.payment
            }).then(res => {
                this.loading = false;
                if (res.code == 200) {
                    this.payInfo.payQrCode =
                        'https://api.pwmqr.com/qrcode/create/?url=' +
                        encodeURIComponent(res.data.qr_code);
                    this.payInfo.out_trade_no = res.data.out_trade_no;
                    this.getOrderStatus();
                }
            });
        },
        async getOrderStatus() {
            const { msg, data, code } = await getOrderStatus({
                order_sn: this.order_sn
            });
            if (code == 200) {
                if (data === 'success') {
                    this.$notify({
                        title: '成功',
                        message: '会员续费成功，感谢支持！',
                        type: 'success'
                    });
                    this.$router.push('/dashboard');
                } else {
                    this.timer = setTimeout(() => {
                        this.getOrderStatus();
                    }, 2000);
                }
            } else {
                this.$message.error(msg);
            }
        }
    }
};
</script>

<style lang="scss">
.order-pages {
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 70vh;

    .box-card {
        width: 30vw;

        .order-info {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            text-align: center;

            .order-info-item {
                font-size: 16px;
                padding: 10px;
                justify-content: space-around;
            }

            .order-info-sn {
                font-size: 20px;
                font-weight: bold;
            }

            .order-info-gopay {
                display: flex;
                flex-direction: column;
                justify-content: space-around;
                align-items: center;

                .order-info-gopay-item {
                    padding: 5px 0;
                }

                .order-info-gopay-payment {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    text-align: left;
                    .order-info-gopay-payment-item {
                        padding: 10px 0;
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 760px) {
        .order-info-item {
            font-size: 14px !important;
        }
        .order-info-gopay-payment {
            display: flex !important;
            flex-direction: column !important;
        }
    }
}
</style>
