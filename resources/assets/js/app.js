require('./bootstrap');

window.Vue = require('vue');

window.VueRouter=require('vue-router').default;

window.VueAxios=require('vue-axios').default;

window.Axios=require('axios').default;

let AppLayout= require('./components/App.vue');

// show the list Shops template
const Listshops=Vue.component('Listshops', require('./components/Listshops.vue'));

// show the list of liked Shops template
const Likedshops =Vue.component('Likedshops', require('./components/Likedshops.vue'));
//
// // view single post template
 const Viewshop =Vue.component('Viewshop', require('./components/Viewshop.vue'));
 //
// // edite post template
// const Editpost =Vue.component('Editpost', require('./components/Editpost.vue'));
//
// // delete post template
// const Deletepost =Vue.component('Deletepost', require('./components/Deletepost.vue'));
//


// registering Modules
Vue.use(VueRouter,VueAxios, axios);

const routes = [
  {
    name: 'Listshops',
    path: '/Listshops',
    component: Listshops
  },
  {
    name: 'Likedshops',
    path: '/LikedShops',
    component: Likedshops
  },
  {
    name: 'Viewpost',
    path: '/view/:id',
    component: Viewpost
   }

];

const router = new VueRouter({ mode: 'history', routes: routes});

new Vue(
 Vue.util.extend(
 { router },
 AppLayout
 )
).$mount('#app');
