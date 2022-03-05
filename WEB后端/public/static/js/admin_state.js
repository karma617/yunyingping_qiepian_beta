Vue.use(Vuex)

const store = new Vuex.Store({
    plugins: [createPersistedState({key:'onephp-admin'})],
    state: {
        menu:{asideColl:!0},
    },
    mutations: {
        'SET_MENU'(state, info) {
            state.menu = info;
        }
    },

    actions: {
        // test({commit}) {
        //     return new Promise((resolve, reject) => {
        //         let promises = Promise.all([
        //             //天气
        //             axios.request({
        //                 url: 'url',
        //             }).then((e) => {
        //                 return e.data
        //             }),
        //         ]);
        //         promises.then(res => {
        //             let [menu] = res;
        //             commit('SET_MENU', menu);
        //             resolve()
        //         })
        //     })
        // },
    }

})