<template>
    <div class="app-container">
        <div class="app-container-form">
            <div class="filter-container">
                <el-input
                    v-model="listQuery.keywords"
                    placeholder="文件名"
                    style="width: 300px;"
                    class="filter-item"
                    clearable
                    @keyup.enter.native="handleFilter"
                ></el-input>
                <el-button
                    class="filter-item"
                    type="primary"
                    icon="el-icon-search"
                    @click="handleFilter"
                >
                    搜索
                </el-button>
                <el-button
                    class="filter-item"
                    type="info"
                    plain
                    icon="el-icon-refresh-right"
                    @click="handleReset"
                >
                    重置
                </el-button>
            </div>
            <div class="filter-container">
                <el-form
                    ref="memberFileForm"
                    :model="memberFileForm"
                    autocomplete="on"
                    label-position="right"
                    label-width="150px"
                >
                    <el-form-item label="批量设置共享">
                        <el-input
                            v-model="memberFileForm.ext_ids"
                            placeholder="填写想要共享资源的用户id,多个用英文逗号分割"
                            type="text"
                            tabindex="1"
                            autocomplete="on"
                            style="width: 500px;"
                        >
                            <el-button
                                type="primary"
                                slot="append"
                                @click.native.prevent="handleSave"
                            >
                                批量共享给他们
                            </el-button>
                        </el-input>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <el-table
            v-loading="listLoading"
            :data="list"
            border
            fit
            empty-text="暂无数据"
            highlight-current-row
            style="width: 100%"
        >
            <el-table-column align="center" label="#" width="80">
                <template slot-scope="{ row }">
                    <span>{{ row.fileId }}</span>
                </template>
            </el-table-column>

            <el-table-column width="180px" align="center" label="文件名称">
                <template slot-scope="{ row }">
                    <p>{{ row.fileName }}</p>
                    <p>来源用户ID：{{ row.userId }}</p>
                </template>
            </el-table-column>

            <el-table-column width="200px" align="center" label="ID">
                <template slot-scope="{ row }">
                    <span>{{ row.fileSha }}</span>
                </template>
            </el-table-column>

            <el-table-column width="300px" label="文件链接">
                <template slot-scope="{ row }">
                    <span>{{ row.fileUrl }}</span>
                </template>
            </el-table-column>

            <el-table-column width="150px" label="文件来源">
                <template slot-scope="{ row }">
                    <span>{{ row.from }}</span>
                </template>
            </el-table-column>

            <el-table-column width="130px" label="分辨率">
                <template slot-scope="{ row }">
                    <span>{{ row.resolute }}</span>
                </template>
            </el-table-column>

            <el-table-column width="200px" label="更新时间">
                <template slot-scope="{ row }">
                    <span>{{ row.update_time }}</span>
                </template>
            </el-table-column>

            <el-table-column min-width="200px" label="共享用户id(单击可修改)">
                <template slot-scope="{ row, $index }">
                    <template v-if="row.edit">
                        <div class="inline-form">
                            <el-input
                                v-model="row.ext_ids"
                                class="edit-input"
                            />
                            <el-button
                                size="small"
                                @click="saveEdit(row, $index)"
                            >
                                保存
                            </el-button>
                            <el-button
                                size="small"
                                @click="row.edit = !row.edit"
                            >
                                取消
                            </el-button>
                        </div>
                    </template>
                    <span
                        style="min-width: 200px;min-height: 20px;display: block;"
                        @click="row.edit = !row.edit"
                        v-else
                    >
                        {{ row.ext_ids || '点击添加或修改' }}
                    </span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="操作">
                <template slot-scope="{ row }">
                    <el-button
                        v-if="row.status != 'deleted'"
                        size="mini"
                        type="danger"
                        @click="handleDelete(row, $index)"
                    >
                        删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination-box">
            <pagination
                v-show="total > 0"
                :total="total"
                :page-size="10"
                :page.sync="listQuery.page"
                :limit.sync="listQuery.limit"
                :page-sizes="[10, 20, 30, 50]"
                background
                layout="total, prev, pager, next, sizes"
                @pagination="getList"
            />
        </div>
    </div>
</template>

<script>
import { getLists, deleteItem, saveExtUsers } from '@/api/files';
import Pagination from '@/components/Pagination';
export default {
    name: 'InlineEditTable',
    components: { Pagination },
    filters: {
        statusFilter(status) {
            const statusMap = {
                published: 'success',
                draft: 'info',
                deleted: 'danger'
            };
            return statusMap[status];
        }
    },
    data() {
        return {
            list: null,
            listLoading: true,
            total: 0,
            listQuery: {
                page: 1,
                limit: 10,
                keywords: ''
            },
            memberFileForm: {
                ext_ids: '',
                fileId: ''
            }
        };
    },
    created() {
        this.getList();
    },
    methods: {
        saveEdit(row, index) {
            let param = {
                ext_ids: row.ext_ids,
                fileId: row.fileId
            };
            saveExtUsers(param).then(res => {
                this.$message.success(res.msg);
                this.list[index].edit = false;
                this.handleFilter();
            });
        },
        handleSave() {
            this.$confirm(
                '此操作将修改全部文件的共享id，单独设置过的也会被覆盖，确认继续？',
                '提示',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }
            )
                .then(() => {
                    saveExtUsers(this.memberFileForm).then(res => {
                        this.$message.success(res.msg);
                        this.handleFilter();
                    });
                })
                .catch(() => {});
        },
        handleReset() {
            this.listQuery = {
                page: 1,
                limit: 10,
                keywords: ''
            };
            this.getList();
        },
        handleFilter() {
            this.listQuery.page = 1;
            this.getList();
        },
        async getList() {
            this.listLoading = true;
            const { data } = await getLists(this.listQuery);
            this.total = data.count;
            const items = data.list;
            this.list = items.map(v => {
                this.$set(v, 'edit', false);
                return v;
            });
            this.listLoading = false;
        },
        handleDelete(row) {
            this.$confirm('确定要删除该文件吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            })
                .then(() => {
                    this.listLoading = true;
                    deleteItem({
                        id: row.fileId
                    })
                        .then(res => {
                            const { msg } = res;
                            this.$notify({
                                title: '提示',
                                message: msg,
                                type: 'success',
                                duration: 2000
                            });
                            this.listLoading = false;
                            this.getList();
                        })
                        .catch(() => {
                            this.listLoading = false;
                        });
                })
                .catch(() => {
                    this.listLoading = false;
                });
        }
    }
};
</script>

<style scoped>
.edit-input {
    padding-right: 100px;
}
.cancel-btn {
    position: absolute;
    right: 15px;
    top: 10px;
}
.pagination-box {
    text-align: right;
}
.filter-item {
    margin-right: 10px;
}
.app-container-form {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.inline-form {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.edit-input {
    padding-right: 10px !important;
}
</style>
